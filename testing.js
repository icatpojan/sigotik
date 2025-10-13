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

    // --- Speed (1 byte) & Heading (1 byte -> scale 0-360) ---
    const speedRaw = bytes[idx++];
    const headingRaw = bytes[idx++] & 0xff;
    const speedKnots = speedRaw; // langsung nilai aslinya
    const heading = Math.round((headingRaw / 255) * 360);

    // --- DC Supply & PSU/Battery ---
    const dcByte = bytes[idx++];
    const dcSupply1 = (dcByte >> 5) & 0x07; // 3 bit
    const dcSupply2 = dcByte & 0x1f; // 5 bit

    const psuBat = bytes[idx++];
    const psuOut = (psuBat >> 4) & 0x0f; // 4 bit
    const battery = psuBat & 0x0f; // 4 bit

    // --- DateTime ---
    const dt = decodeDatetime(bytes.slice(idx, idx + 3));
    idx += 3;

    // --- RPM1 & RPM2 ---
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
        speedKnots,
        heading,
        dcSupply1,
        dcSupply2,
        psuOut,
        battery,
        ...dt,
        windSpeed,
        temperature,
        humidity,
        tamper,
        rpm1,
        rpm2,
        fuel,
    };
}

const result = decodePacket("04065E294BFFA1B8140A156EC8584BA502BC0384051B4D");
console.log(result);

function encodePacket(data) {
    const bytes = [];

    // --- Header ---
    bytes.push(data.header ?? 0x03);

    // --- Longitude & Latitude (Int32) ---
    const lon = Math.round(data.longitude * 1e6);
    const lat = Math.round(data.latitude * 1e6);
    for (let i = 3; i >= 0; i--) bytes.push((lon >> (i * 8)) & 0xff);
    for (let i = 3; i >= 0; i--) bytes.push((lat >> (i * 8)) & 0xff);

    // --- Speed (6 bit) + Heading (10 bit) ---
    const headingBits = Math.round((data.heading / 359) * 1023) & 0x3ff;
    const speedBits = (data.speedKnots ?? 0) & 0x3f;
    const spdHead = (speedBits << 10) | headingBits;
    bytes.push((spdHead >> 8) & 0xff);
    bytes.push(spdHead & 0xff);

    // --- DC Supply1 (3 bit) + DC Supply2 (5 bit) ---
    const dcByte = ((data.dcSupply1 & 0x07) << 5) | (data.dcSupply2 & 0x1f);
    bytes.push(dcByte);

    // --- PSU Out (4 bit) + Battery (4 bit) ---
    const psuBat = ((data.psuOut & 0x0f) << 4) | (data.battery & 0x0f);
    bytes.push(psuBat);

    // --- DateTime encode ---
    const yearOffset = (data.year ?? 2025) - 2025;
    let bits = 0;
    bits |= yearOffset & 0x0f;
    bits = (bits << 4) | ((data.month ?? 1) & 0x0f);
    bits = (bits << 5) | ((data.day ?? 1) & 0x1f);
    bits = (bits << 5) | ((data.hour ?? 0) & 0x1f);
    bits = (bits << 6) | ((data.minute ?? 0) & 0x3f);
    bytes.push((bits >> 16) & 0xff);
    bytes.push((bits >> 8) & 0xff);
    bytes.push(bits & 0xff);

    // --- RPM1 & RPM2 ---
    bytes.push((data.rpm1 >> 8) & 0xff, data.rpm1 & 0xff);
    bytes.push((data.rpm2 >> 8) & 0xff, data.rpm2 & 0xff);

    // --- Optional sensors ---
    if (
        [0x04, 0x05, 0x08, 0x0a, 0x0b].includes(data.header) &&
        data.windSpeed !== undefined
    )
        bytes.push(data.windSpeed & 0xff);

    if (
        [0x04, 0x06, 0x08, 0x09, 0x0b].includes(data.header) &&
        data.temperature !== undefined
    )
        bytes.push(data.temperature & 0xff);

    if (
        [0x04, 0x07, 0x09, 0x0a, 0x0b].includes(data.header) &&
        data.humidity !== undefined
    )
        bytes.push(data.humidity & 0xff);

    if (
        (data.header === 0x0b || data.header === 0x0c) &&
        data.fuel !== undefined
    )
        bytes.push(data.fuel & 0xff);

    if (data.header === 0x0d && data.tamper !== undefined)
        bytes.push(data.tamper & 0xff);

    // --- Convert ke hex string ---
    return bytes
        .map((b) => b.toString(16).padStart(2, "0"))
        .join("")
        .toUpperCase();
}

// contoh uji: encode hasil dari decode sebelumnya
const hex = encodePacket({
    header: 0x03,
    longitude: 106.989643,
    latitude: -95.056808,
    speedKnots: 20,
    heading: 47,
    year: 2029,
    month: 10,
    day: 21,
    hour: 12,
    minute: 8,
    dcSupply1: 1,
    dcSupply2: 8,
    psuOut: 5,
    battery: 2,
    rpm1: 700,
    rpm2: 900,
});

console.log(hex);
