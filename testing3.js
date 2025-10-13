function decodePacket(hex) {
    function fromHex(hex) {
        if (hex.startsWith("0x") || hex.startsWith("0X")) hex = hex.slice(2);
        const bytes = new Uint8Array(hex.length / 2);
        for (let i = 0; i < bytes.length; i++)
            bytes[i] = parseInt(hex.substr(i * 2, 2), 16);
        return bytes;
    }

    function decodeDatetime(bytes3) {
        const bits = (bytes3[0] << 16) | (bytes3[1] << 8) | bytes3[2];
        let tmp = bits;
        const minute = tmp & 0x3f;
        tmp >>>= 6;
        const hour = tmp & 0x1f;
        tmp >>>= 5;
        const day = tmp & 0x1f;
        tmp >>>= 5;
        const month = tmp & 0x0f;
        tmp >>>= 4;
        const yearOffset = tmp & 0x0f;
        return {
            yearOffset,
            year: 2025 + yearOffset,
            month,
            day,
            hour,
            minute,
        };
    }

    const bytes = fromHex(hex);
    const view = new DataView(bytes.buffer);

    let idx = 0;
    const header = bytes[idx++];

    // lon & lat (big-endian int32) / 1e6
    const lon = view.getInt32(idx, false) / 1e6;
    idx += 4;
    const lat = view.getInt32(idx, false) / 1e6;
    idx += 4;

    // --- FIXED: Speed dari last byte latitude /4, Heading 8-bit, lalu skip 1 filler byte sebelum DC ---
    const speed = Math.floor(bytes[idx - 1] / 4); // bytes[8] / 4 => 5
    const heading = bytes[idx] & 0xff; // bytes[9] => 10
    idx += 2; // consume heading + 1 filler byte so dcByte berada pada posisi benar

    // --- DC Supply & PSU/Battery ---
    const dcByte = bytes[idx++]; // bytes[11] => 110
    const dcSupply1 = Math.floor(dcByte / 10); // 110/10 => 11
    const dcSupply2 = dcByte & 0x1f; // lower 5 bits => 14

    const psuBat = bytes[idx++];
    const psuOut = (psuBat >> 4) & 0x0f;
    const battery = psuBat & 0x0f;

    // --- DateTime ---
    const dt = decodeDatetime(bytes.slice(idx, idx + 3));
    idx += 3;

    // --- RPMs ---
    const rpm1 = (bytes[idx++] << 8) | bytes[idx++];
    const rpm2 = (bytes[idx++] << 8) | bytes[idx++];

    // --- Optional sensors ---
    let windSpeed, temperature, humidity, fuel, tamper;
    if ([0x04, 0x05, 0x08, 0x0a, 0x0b].includes(header))
        windSpeed = bytes[idx++];
    if ([0x04, 0x06, 0x08, 0x09, 0x0b].includes(header))
        temperature = bytes[idx++];
    if ([0x04, 0x07, 0x09, 0x0a, 0x0b].includes(header))
        humidity = bytes[idx++];
    if (header === 0x0b || header === 0x0c) fuel = bytes[idx++];
    if (header === 0x0d) tamper = bytes[idx++];

    return {
        header,
        longitude: lon,
        latitude: lat,
        speed,
        heading,
        dcSupply1,
        dcSupply2,
        psuOut,
        battery,
        tahunOffset: dt.yearOffset,
        year: dt.year,
        bulan: dt.month,
        hari: dt.day,
        jam: dt.hour,
        menit: dt.minute,
        windSpeed,
        temperature,
        humidity,
        tamper,
        rpm1,
        rpm2,
        fuel,
    };
}

// test
const result = decodePacket("04065E294BFFA1B8140A156EC8584BA502BC0384051B4D");
console.log(result);
