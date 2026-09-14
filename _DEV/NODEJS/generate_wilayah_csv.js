/**
 * Generate CSV dari wilayah_2020.sql
 * - Input  : _DEV/NODEJS/wilayah_2020.sql (fallback _DEV/wilayah_2020.sql)
 * - Output : data_wilayah/ (di root project) + _DEV/data_wilayah/
 * - Format CSV persis dengan app/database/data (delimiter ;, LF, header sama)
 * - kode: hapus semua '.' -> id
 * - provinces: id;name (name UPPERCASE)
 * - regencies: id;province_id;name (name UPPERCASE)
 * - districts: id;regency_id;name (name original)
 * - villages : id;district_id;name (name original)
 */

const fs = require('fs');
const path = require('path');

// --- resolve input ---
const inputCandidates = [
  path.join(__dirname, 'NODEJS', 'wilayah_2020.sql'),
  path.join(__dirname, 'wilayah_2020.sql'),
  path.resolve(__dirname, '..', '_DEV', 'NODEJS', 'wilayah_2020.sql'),
  path.resolve(__dirname, '..', 'wilayah_2020.sql'),
];
let inputPath = inputCandidates.find(p => fs.existsSync(p));
if (!inputPath) {
  console.error('[ERROR] wilayah_2020.sql tidak ditemukan. Kandidat:');
  inputCandidates.forEach(p => console.error(' -', p));
  process.exit(1);
}

// --- resolve output dirs ---
const outputDirs = [
  path.resolve(__dirname, '..', 'data_wilayah'), // root/data_wilayah
  path.join(__dirname, 'data_wilayah'),         // _DEV/data_wilayah
];
outputDirs.forEach(dir => {
  if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
});
const primaryOut = outputDirs[0];

console.log(`[INFO] Input : ${inputPath}`);
console.log(`[INFO] Output: ${primaryOut}`);

const content = fs.readFileSync(inputPath, 'utf8');

// regex untuk tuple ('kode','nama') handle '' escape
const regex = /\(\s*'([^']+)'\s*,\s*'((?:[^']|'')+)'\s*\)/g;
let m;
const provinces = [];
const regencies = [];
const districts = [];
const villages = [];

function csvEscape(field) {
  const str = String(field);
  // perlu quote jika mengandung delimiter ; atau double-quote atau newline atau spasi
  // di data existing: spasi memicu quote (contoh "SUMATERA UTARA", "Kluet Utara")
  // jadi kita quote jika ada spasi, ; , " , \n , \r
  const needsQuote = str.includes(';') || str.includes('"') || str.includes('\n') || str.includes('\r') || str.includes(' ');
  if (!needsQuote) return str;
  // escape double-quote dengan doubling
  const escaped = str.replace(/"/g, '""');
  return `"${escaped}"`;
}

let count = 0;
while ((m = regex.exec(content)) !== null) {
  const kodeDot = m[1].trim(); // contoh '11.01.01.2001'
  const namaRaw = m[2].replace(/''/g, "'").trim(); // unescape SQL ''
  const kodeClean = kodeDot.replace(/\./g, ''); // hapus semua '.'
  const dotCount = (kodeDot.match(/\./g) || []).length;
  count++;

  if (dotCount === 0) {
    // Provinsi: 11 -> ACEH
    provinces.push({
      id: kodeClean,
      name: namaRaw.toUpperCase(),
    });
  } else if (dotCount === 1) {
    // Kabupaten/Kota: 11.01 -> 1101 , province_id = 2 digit pertama
    const province_id = kodeClean.substring(0, 2);
    regencies.push({
      id: kodeClean,
      province_id,
      name: namaRaw.toUpperCase(),
    });
  } else if (dotCount === 2) {
    // Kecamatan: 11.01.01 -> 110101 , regency_id = 4 digit pertama
    const regency_id = kodeClean.substring(0, 4);
    districts.push({
      id: kodeClean,
      regency_id,
      name: namaRaw, // original case
    });
  } else if (dotCount === 3) {
    // Desa: 11.01.01.2001 -> 1101012001 , district_id = 6 digit pertama
    const district_id = kodeClean.substring(0, 6);
    villages.push({
      id: kodeClean,
      district_id,
      name: namaRaw, // original case
    });
  }
}

console.log(`[INFO] Parsed ${count} rows -> prov:${provinces.length} kab:${regencies.length} kec:${districts.length} desa:${villages.length}`);

// --- helper tulis CSV ---
function writeCsv(fileName, header, rows, mapper) {
  const lines = [];
  lines.push(header);
  for (const r of rows) {
    lines.push(mapper(r));
  }
  const data = lines.join('\n') + '\n'; // LF, sama seperti app/database/data
  // tulis ke semua outputDirs
  outputDirs.forEach(dir => {
    const outPath = path.join(dir, fileName);
    fs.writeFileSync(outPath, data, 'utf8');
    console.log(`[OK] ${outPath} (${rows.length} rows)`);
  });
}

writeCsv('provinces.csv', 'id;name', provinces, r => `${r.id};${csvEscape(r.name)}`);
writeCsv('regencies.csv', 'id;province_id;name', regencies, r => `${r.id};${r.province_id};${csvEscape(r.name)}`);
writeCsv('districts.csv', 'id;regency_id;name', districts, r => `${r.id};${r.regency_id};${csvEscape(r.name)}`);
writeCsv('villages.csv', 'id;district_id;name', villages, r => `${r.id};${r.district_id};${csvEscape(r.name)}`);

console.log('[DONE] Semua CSV berhasil digenerate.');
