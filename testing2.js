function fromHex(hex) {
    if (hex.startsWith("0x") || hex.startsWith("0X")) hex = hex.slice(2);
    const bytes = new Uint8Array(hex.length / 2);
    for (let i = 0; i < bytes.length; i++) bytes[i] = parseInt(hex.substr(i * 2, 2), 16);
    return bytes;
}

function toHex(bytes) {
    return Array.from(bytes).map(b => b.toString(16).padStart(2, "0")).join("");
}

// -------------------- DECODE --------------------
function decodePacket(hex) {
    function decodeDatetime(bytes3) {
        const bits = (bytes3[0] << 16) | (bytes3[1] << 8) | bytes3[2];
        let tmp = bits;
        const minute = tmp & 0x3F; tmp >>>= 6;
        const hour = tmp & 0x1F; tmp >>>= 5;
        const day = tmp & 0x1F; tmp >>>= 5;
        const month = tmp & 0x0F; tmp >>>= 4;
        const yearOffset = tmp & 0x0F;
        return { year: 2025 + yearOffset, month, day, hour, minute };
    }

    const bytes = fromHex(hex);
    const view = new DataView(bytes.buffer);
    let idx = 0;

    const header = bytes[idx++];

    const lon = view.getInt32(idx, false) / 1e6;
    idx += 4;
    const lat = view.getInt32(idx, false) / 1e6;
    idx += 4;

    const speed = bytes[idx++];
    const headingRaw = bytes[idx++];
    const heading = Math.round((headingRaw / 255) * 359);

    const dt = decodeDatetime(bytes.slice(idx, idx + 3));
    idx += 3;

    const rpm1 = (bytes[idx++] << 8) | bytes[idx++];
    const rpm2 = (bytes[idx++] << 8) | bytes[idx++];

    let windSpeed, temperature, humidity, fuel, temper;

    if ([0x04, 0x05, 0x08, 0x0A, 0x0B].includes(header)) windSpeed = bytes[idx++];
    if ([0x04, 0x06, 0x08, 0x09, 0x0B].includes(header)) temperature = bytes[idx++];
    if ([0x04, 0x07, 0x09, 0x0A, 0x0B].includes(header)) humidity = bytes[idx++];
    if ([0x0B, 0x0C].includes(header)) fuel = bytes[idx++];
    if (header === 0x0D) temper = bytes[idx++];

    return {
        header,
        longitude: lon,
        latitude: lat,
        speed,
        heading,
        ...dt,
        rpm1,
        rpm2,
        windSpeed,
        temperature,
        humidity,
        fuel,
        temper
    };
}

// -------------------- ENCODE --------------------
function encodePacket(data) {
    function encodeDatetime(dt) {
        const yearOffset = (dt.year - 2025) & 0x0F;
        let bits = 0;
        bits |= (yearOffset & 0x0F) << 20;
        bits |= (dt.month & 0x0F) << 16;
        bits |= (dt.day & 0x1F) << 11;
        bits |= (dt.hour & 0x1F) << 6;
        bits |= (dt.minute & 0x3F);
        return [(bits >> 16) & 0xFF, (bits >> 8) & 0xFF, bits & 0xFF];
    }

    const bytes = [];
    bytes.push(data.header & 0xFF);

    const lon = Math.round(data.longitude * 1e6);
    bytes.push((lon >> 24) & 0xFF, (lon >> 16) & 0xFF, (lon >> 8) & 0xFF, lon & 0xFF);

    const lat = Math.round(data.latitude * 1e6);
    bytes.push((lat >> 24) & 0xFF, (lat >> 16) & 0xFF, (lat >> 8) & 0xFF, lat & 0xFF);

    bytes.push(data.speed & 0xFF);

    const headingByte = Math.round((data.heading / 359) * 255) & 0xFF;
    bytes.push(headingByte);

    bytes.push(...encodeDatetime(data));

    bytes.push((data.rpm1 >> 8) & 0xFF, data.rpm1 & 0xFF);
    bytes.push((data.rpm2 >> 8) & 0xFF, data.rpm2 & 0xFF);

    if ([0x04, 0x05, 0x08, 0x0A, 0x0B].includes(data.header)) bytes.push(data.windSpeed ?? 0);
    if ([0x04, 0x06, 0x08, 0x09, 0x0B].includes(data.header)) bytes.push(data.temperature ?? 0);
    if ([0x04, 0x07, 0x09, 0x0A, 0x0B].includes(data.header)) bytes.push(data.humidity ?? 0);
    if ([0x0B, 0x0C].includes(data.header)) bytes.push(data.fuel ?? 0);
    if (data.header === 0x0D) bytes.push(data.temper ?? 0);

    return "0x" + toHex(bytes);
}

const hex = encodePacket({
  header: 0x04,
  longitude: 106.823,
  latitude: -6.212,
  speed: 22,
  heading: 180,
  year: 2025,
  month: 10,
  day: 6,
  hour: 13,
  minute: 2,
  rpm1: 1200,
  rpm2: 1250,
  windSpeed: 12,
  temperature: 30,
  humidity: 60
});

// console.log(hex);
console.log(decodePacket("04065E294BFFA1B8140A156EC8584BA502BC0384051B4D"));

