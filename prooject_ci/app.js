const createConnection = require('./getConnection');
const axios = require('axios');
const fs = require('fs');

let lastHour = null;
let counter = 0;
let lastVarMOID = 3838693; // Inisialisasi dengan nilai default

// Fungsi untuk membaca lastVarMOID dari file
function loadLastVarMOID() {
    try {
        const data = fs.readFileSync('last_varmoid.txt', 'utf8');
        lastVarMOID = parseInt(data, 10);
        console.log('Loaded lastVarMOID:', lastVarMOID);
    } catch (error) {
        console.error('Error loading lastVarMOID, using default:', error);
    }
}

// Fungsi untuk menyimpan lastVarMOID ke file
function saveLastVarMOID() {
    try {
        fs.writeFileSync('last_varmoid.txt', lastVarMOID.toString());
        console.log('Saved lastVarMOID:', lastVarMOID);
    } catch (error) {
        console.error('Error saving lastVarMOID:', error);
    }
}

// Panggil fungsi untuk memuat lastVarMOID saat aplikasi mulai
loadLastVarMOID();

function getTransactionId(oTanggal) {
    const sTanggal = oTanggal.toISOString().slice(0, 13).replace(/[-T]/g, '');
    const currentHour = sTanggal;
    if (lastHour !== currentHour) {
        counter = 0;
        lastHour = currentHour;
    }
    const formattedCounter = counter.toString().padStart(4, '0');
    counter++;
    return sTanggal + formattedCounter;
}

async function runQuery() {
    try {
        const connection = await createConnection(); // Mendapatkan koneksi dengan retry
        console.time('QueryTime'); // Mulai menghitung waktu

        const query = `
            SELECT
                PSG_MOData.VAR_MOID,
                ai_mobile.id, 
                PSG_MOData.deviceID, 
                PSG_MOData.msgTimestamp_GMT, 
                PSG_MOData.Latitude, 
                PSG_MOData.Longitude, 
                PSG_MOData.Altitude, 
                PSG_MOData.Direction, 
                PSG_MOData.Speed
            FROM    
                kapal_pintar_db.ai_mobile as ai_mobile
            INNER JOIN
                PIM.PSG_MOData as PSG_MOData
            ON 
                ai_mobile.sn = PSG_MOData.deviceID
            WHERE
                PSG_MOData.VAR_MOID > ?
                AND PSG_MOData.MsgType = 'STD'
            AND ai_mobile.category_status_id = 6
            AND PSG_MOData.Latitude IS NOT NULL
            AND PSG_MOData.Longitude IS NOT NULL
            ORDER BY PSG_MOData.VAR_MOID ASC;
        `;

        connection.query(query, [lastVarMOID], async (error, results) => {
            if (error) {
                console.error('Query error:', error);
                return;
            }

            // console.log('Query results:', results);

            for (const element of results) {
                const nLatitude = parseFloat(element.Latitude);
                const nLongitude = parseFloat(element.Longitude);
                const kkpId = element.id;
                const nSpeed = element.Speed ? parseFloat(element.Speed) * 10 : 0;
                const sHeading = element.Direction || '0';
                console.log(element);
                let oTanggal = new Date(element.msgTimestamp_GMT);
                oTanggal.setHours(oTanggal.getHours());
                const updatedTimestamp = oTanggal.toISOString();
                oTanggal = new Date(updatedTimestamp);
                const sWaktu = oTanggal.toISOString().slice(11, 16).replace(':', '');
                const sTanggal = oTanggal.toISOString().slice(0, 11).replace(/[-T]/g, '');
                const sTransId = getTransactionId(oTanggal);
                let body = `//SR//AD/KKP//FR/PINTAR//TM/POS//TR/${sTransId}//IR/${kkpId}//NA/${kkpId}//LT/${nLatitude}//LG/${nLongitude}//SP/${nSpeed}//CO/${sHeading}//DA/${sTanggal}//TI/${sWaktu}//FS/IDN//NS/9//BH/168//GF/0//VE/12//PW/0//EM/0//AT/0//ER\n`;

                const soapData = `
                            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:vms="http://j2ee.netbeans.org/wsdl/VmsDataProvider/src/VmsDataProvider">
                               <soapenv:Header/>
                               <soapenv:Body>
                                  <vms:VmsDataProviderOperation>
                                     <rawData>${body}</rawData>
                                  </vms:VmsDataProviderOperation>
                               </soapenv:Body>
                            </soapenv:Envelope>
                            `;
                try {
                    const response = await axios.post('https://devspkp.kkp.go.id:8066/VmsDataProviderService/VmsDataProviderPort', soapData, {
                        headers: {
                            'Content-Type': 'text/xml',
                            'SOAPAction': 'http://j2ee.netbeans.org/wsdl/VmsDataProvider/src/VmsDataProvider/VmsDataProviderOperation'
                        }
                    });

                    const result = response.data;
                    fs.appendFileSync(`logs/result-${sTanggal}.txt`, ` data:${body}\n hasil:${result}\n ---\n`);
                    console.log(`Data sent: ${body}, Response result: ${result}`);
                } catch (error) {
                    fs.appendFileSync(`logs/id-error-${sTanggal}.txt`, `${element.VAR_MOID.toString()}\n`);
                    fs.appendFileSync(`logs/log-error-${sTanggal}.txt`, `${error}\n `);
                    console.error(`Error sending data: ${error}`);
                }
            }

            // Update lastVarMOID dengan VAR_MOID terakhir yang diterima
            if (results.length > 0) {
                lastVarMOID = results[results.length - 1].VAR_MOID;
                
                console.log('Data terakhir ' + lastVarMOID);
                console.log('Waktu terakhir ' + results[results.length - 1].msgTimestamp_GMT);
                saveLastVarMOID(); // Simpan lastVarMOID ke file
            }

            console.timeEnd('QueryTime'); // Akhiri penghitungan waktu dan cetak hasilnya
            connection.end(err => {
                if (err) {
                    return console.error('error ending the pool: ' + err.stack);
                }
                console.log('Pool connections closed');
            }); // Tutup koneksi
        });

    } catch (error) {
        console.error('Connection error:', error); // Tangani kesalahan koneksi
    }
}
runQuery();
setInterval(runQuery, 5 * 60 * 1000);
