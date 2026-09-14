/**
 * addgereja.js - NodeJS port of _DEV/PYTHON/add_gereja.py
 *
 * Fitur:
 *  - baca Excel (default: _DEV/NODEJS/data_mateng/*.xlsx - data mentah per-provinsi, 37 files)
 *    fallback: _DEV/PYTHON/data_matang/Gereja-Katolik.xlsx jika data_mateng kosong
 *  - cek log.txt & DB (nama_gereja_exists) -> skip HANYA jika ada di LOG && DB; jika salah satu tidak ada tetap input, tapi tidak tulis log ulang jika sudah ada di log
 *  - login via Selenium WebDriver + Chrome
 *  - extract wilayah dari alamat (provinsi/kabupaten/kecamatan/kelurahan)
 *  - select2 cascading (addProvinsi -> addKabupaten -> addKecamatan -> addKelurahan)
 *  - handle link_maps via buka tab baru & ambil current_url
 *  - handle sosmed platform detection via website
 *  - simpan & tulis log
 *
 * Usage:
 *   node addgereja.js                          # jalankan automation (seperti python)
 *   node addgereja.js --preview                # hanya preview Excel (mode lama)
 *   node addgereja.js --preview --limit 2      # preview 2 baris
 *   node addgereja.js --dry-run                # jalan tanpa selenium (cek skip/logic saja)
 *   node addgereja.js --headless               # chrome headless
 *   node addgereja.js --excel "path/to/file.xlsx"  # custom excel
 */

const fs = require("fs");
const path = require("path");
const XLSX = require("xlsx");
const mysql = require("mysql2/promise");

// Selenium
const { Builder, By, until, Key, Select } = require("selenium-webdriver");
const chrome = require("selenium-webdriver/chrome");
require("chromedriver");

// ---------------------------------------------------------------------------
// Konfigurasi (mirror Python)
// ---------------------------------------------------------------------------
const BASE_URL = process.env.BASE_URL || "http://192.168.1.240/Gereja-Katolik-Indonesia";
// const BASE_URL = "http://192.168.1.4/Gereja-Katolik-Indonesia";
const EMAIL = process.env.GEREJA_EMAIL || "admin.gereja.katolik.indonesia@gmail.com";
const PASSWORD = process.env.GEREJA_PASSWORD || "Admin123_@";

const LOG_PATH = path.join(__dirname, "log.txt");

// ---------------------------------------------------------------------------
// Terminal Styling - informatif & mudah dibaca (support Windows CMD/PowerShell)
// ---------------------------------------------------------------------------
const _isWin = process.platform === 'win32';
const _hasColor = !_isWin || !!process.env.FORCE_COLOR || !!process.env.WT_SESSION || !!process.env.ConEmuANSI || (process.env.TERM && process.env.TERM !== 'dumb');
const C = _hasColor ? {
  reset: "\x1b[0m", bold: "\x1b[1m", dim: "\x1b[2m", italic: "\x1b[3m",
  red: "\x1b[31m", green: "\x1b[32m", yellow: "\x1b[33m", blue: "\x1b[34m", magenta: "\x1b[35m", cyan: "\x1b[36m", white: "\x1b[37m", gray: "\x1b[90m",
  bgRed: "\x1b[41m", bgGreen: "\x1b[42m", bgYellow: "\x1b[43m", bgBlue: "\x1b[44m",
} : { reset:"", bold:"", dim:"", italic:"", red:"", green:"", yellow:"", blue:"", magenta:"", cyan:"", white:"", gray:"", bgRed:"", bgGreen:"", bgYellow:"", bgBlue:"" };
const S = {
  ok: C.green + "✔" + C.reset, fail: C.red + "✘" + C.reset, warn: C.yellow + "⚠" + C.reset,
  skip: C.gray + "↷" + C.reset, info: C.cyan + "ℹ" + C.reset, arrow: C.gray + "→" + C.reset,
  dot: C.gray + "·" + C.reset, star: C.yellow + "★" + C.reset,
};
function col(t,c){ return c + t + C.reset; }
function badge(text, color){ return color + C.bold + ` ${text} ` + C.reset; }
function bar(cur, total, width=24){
  const pct = total? cur/total : 0;
  const filled = Math.round(width*pct);
  const empty = width-filled;
  const p = (pct*100).toFixed(1).padStart(5);
  return col("█".repeat(filled), C.cyan) + col("░".repeat(empty), C.gray) + ` ${col(p+"%", C.bold)} ${col(`(${cur}/${total})`, C.dim)}`;
}
function hr(char="─", len=72){ return col(char.repeat(len), C.gray); }
function boxTitle(title){
  const w=72; const pad = Math.max(0, w - 2 - title.length);
  const l = Math.floor(pad/2), r = pad - l;
  return `\n${col("╔"+"═".repeat(w-2)+"╗", C.cyan)}\n${col("║",C.cyan)}${" ".repeat(l)}${col(C.bold+title, C.white)}${" ".repeat(r)}${col("║",C.cyan)}\n${col("╚"+"═".repeat(w-2)+"╝", C.cyan)}`;
}
function fmtTime(s){ const m=Math.floor(s/60), sec=(s%60).toFixed(1); return m? `${m}m ${sec}s` : `${sec}s`; }
function truncate(s, n){ s=String(s||""); return s.length>n ? s.slice(0,n-1)+"…" : s; }

// Excel path - prioritas: _DEV/NODEJS/data_mateng (data mentah) dulu, baru fallback ke data matang
function resolveExcelSources(custom) {
  if (custom) {
    const abs = path.resolve(custom);
    if (fs.existsSync(abs)) {
      const stat = fs.statSync(abs);
      if (stat.isDirectory()) {
        const files = fs.readdirSync(abs).filter(f=>f.toLowerCase().endsWith(".xlsx")).sort().map(f=>path.join(abs,f));
        if (files.length) return files;
      } else {
        return [abs];
      }
    }
    // allow partial name like "ACEH" -> cari di data_mateng
    const dataMentahDirs = [path.join(__dirname, "data_mateng"), path.join(__dirname, "..", "data_mateng")];
    for (const d of dataMentahDirs) {
      if (fs.existsSync(d)) {
        const matched = fs.readdirSync(d).filter(f=>f.toLowerCase().includes(custom.toLowerCase()) && f.toLowerCase().endsWith(".xlsx")).sort().map(f=>path.join(d,f));
        if (matched.length) return matched;
      }
    }
  }
  // DEFAULT: _DEV/NODEJS/data_mateng (data mentah per-provinsi) - sesuai request
  const primaryDirs = [
    path.join(__dirname, "data_mateng"),
    path.join(__dirname, "..", "data_mateng"),
  ];
  for (const d of primaryDirs) {
    if (fs.existsSync(d)) {
      const files = fs.readdirSync(d).filter(f=>f.toLowerCase().endsWith(".xlsx")).sort().map(f=>path.join(d,f));
      if (files.length) return files;
    }
  }
  // fallback: data matang (Gereja-Katolik.xlsx) jika data mentah kosong
  const fallbackFiles = [
    path.join(__dirname, "..", "PYTHON", "data_matang", "Gereja-Katolik.xlsx"),
    path.join(__dirname, "data_matang", "Gereja-Katolik.xlsx"),
    path.join(__dirname, "..", "data_matang", "Gereja-Katolik.xlsx"),
  ];
  for (const p of fallbackFiles) if (fs.existsSync(p)) return [p];
  // last fallback: any xlsx di PYTHON/data_matang
  const lastDir = path.join(__dirname, "..", "PYTHON", "data_matang");
  if (fs.existsSync(lastDir)) {
    const files = fs.readdirSync(lastDir).filter(f=>f.toLowerCase().endsWith(".xlsx")).sort().map(f=>path.join(lastDir,f));
    if (files.length) return files;
  }
  return [];
}
function resolveExcelPath(custom) {
  const src = resolveExcelSources(custom);
  return src.length ? src[0] : null;
}

const DB_CONFIG = {
  host: process.env.DB_HOST || "localhost",
  user: process.env.DB_USER || "root",
  password: process.env.DB_PASS || "",
  database: process.env.DB_NAME || "db_gereja",
  charset: "utf8",
};

const PROVINSI_LIST = [
  "ACEH","SUMATERA UTARA","SUMATERA BARAT","RIAU","JAMBI","SUMATERA SELATAN",
  "BENGKULU","LAMPUNG","KEPULAUAN BANGKA BELITUNG","KEPULAUAN RIAU",
  "DAERAH KHUSUS IBUKOTA JAKARTA","DKI JAKARTA","JAWA BARAT","JAWA TENGAH","DAERAH ISTIMEWA YOGYAKARTA",
  "JAWA TIMUR","BANTEN","BALI","NUSA TENGGARA BARAT","NUSA TENGGARA TIMUR",
  "KALIMANTAN BARAT","KALIMANTAN TENGAH","KALIMANTAN SELATAN","KALIMANTAN TIMUR","KALIMANTAN UTARA",
  "SULAWESI UTARA","SULAWESI TENGAH","SULAWESI SELATAN","SULAWESI TENGGARA","GORONTALO","SULAWESI BARAT",
  "MALUKU","MALUKU UTARA","PAPUA","PAPUA BARAT","PAPUA BARAT DAYA","PAPUA SELATAN","PAPUA TENGAH","PAPUA PEGUNUNGAN",
];

// ---------------------------------------------------------------------------
// Helpers - mirror Python
// ---------------------------------------------------------------------------
function writeLog(namaGereja, status = "") {
  if (!namaGereja) return;
  const now = new Date();
  const pad = (n) => String(n).padStart(2, "0");
  const ts = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())} ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
  let line = `${ts} | ${namaGereja}`;
  if (status) line += ` [${status}]`;
  line += "\n";
  fs.appendFileSync(LOG_PATH, line, "utf-8");
  console.log(`   [LOG] ${line.trim()}`);
}

function namaGerejaInLog(namaGereja) {
  if (!namaGereja || !fs.existsSync(LOG_PATH)) return false;
  const nama = String(namaGereja).trim().toLowerCase();
  try {
    const content = fs.readFileSync(LOG_PATH, "utf-8");
    for (const line of content.split("\n")) {
      const parts = line.trim().split(" | ");
      if (parts.length >= 2) {
        const logged = parts[1].split(" [")[0].trim().toLowerCase();
        if (logged === nama) return true;
      }
    }
  } catch (e) { console.log(`  Error membaca log: ${e.message}`); }
  return false;
}

// ---------------------------------------------------------------------------
// Pemetaan alamat Bahasa Inggris (Google Maps) -> Bahasa Indonesia.
// Contoh EN: "Dairi Regency, North Sumatra" ~= "Kabupaten Dairi, Sumatera Utara"
//            "South Jakarta City, Jakarta"   ~= "Kota Jakarta Selatan, DKI Jakarta"
//            "West Rawa, Kebayoran Baru"     ~= kelurahan "Rawa Barat"
// ---------------------------------------------------------------------------
// Bahasa Inggris (lowercase, tanpa "province") -> nama kanonis Indonesia (kolom provinces.name)
const EN_PROVINCE_MAP = {
  "aceh": "ACEH",
  "north sumatra": "SUMATERA UTARA", "north sumatera": "SUMATERA UTARA",
  "west sumatra": "SUMATERA BARAT", "west sumatera": "SUMATERA BARAT",
  "riau": "RIAU",
  "jambi": "JAMBI",
  "south sumatra": "SUMATERA SELATAN", "south sumatera": "SUMATERA SELATAN",
  "bengkulu": "BENGKULU",
  "lampung": "LAMPUNG",
  "bangka belitung": "KEPULAUAN BANGKA BELITUNG",
  "bangka belitung islands": "KEPULAUAN BANGKA BELITUNG",
  "kepulauan bangka belitung": "KEPULAUAN BANGKA BELITUNG",
  "riau islands": "KEPULAUAN RIAU",
  "kepulauan riau": "KEPULAUAN RIAU",
  "jakarta": "DAERAH KHUSUS IBUKOTA JAKARTA",
  "dki jakarta": "DAERAH KHUSUS IBUKOTA JAKARTA",
  "special capital region of jakarta": "DAERAH KHUSUS IBUKOTA JAKARTA",
  "daerah khusus ibukota jakarta": "DAERAH KHUSUS IBUKOTA JAKARTA",
  "west java": "JAWA BARAT",
  "central java": "JAWA TENGAH",
  "east java": "JAWA TIMUR",
  "yogyakarta": "DAERAH ISTIMEWA YOGYAKARTA",
  "special region of yogyakarta": "DAERAH ISTIMEWA YOGYAKARTA",
  "daerah istimewa yogyakarta": "DAERAH ISTIMEWA YOGYAKARTA",
  "banten": "BANTEN",
  "bali": "BALI",
  "west nusa tenggara": "NUSA TENGGARA BARAT",
  "east nusa tenggara": "NUSA TENGGARA TIMUR",
  "west kalimantan": "KALIMANTAN BARAT",
  "central kalimantan": "KALIMANTAN TENGAH",
  "south kalimantan": "KALIMANTAN SELATAN",
  "east kalimantan": "KALIMANTAN TIMUR",
  "north kalimantan": "KALIMANTAN UTARA",
  "north sulawesi": "SULAWESI UTARA",
  "central sulawesi": "SULAWESI TENGAH",
  "south sulawesi": "SULAWESI SELATAN",
  "southeast sulawesi": "SULAWESI TENGGARA", "south east sulawesi": "SULAWESI TENGGARA",
  "gorontalo": "GORONTALO",
  "west sulawesi": "SULAWESI BARAT",
  "maluku": "MALUKU",
  "north maluku": "MALUKU UTARA", "north moluccas": "MALUKU UTARA",
  "papua": "PAPUA",
  "west papua": "PAPUA BARAT",
  "southwest papua": "PAPUA BARAT DAYA", "south west papua": "PAPUA BARAT DAYA",
  "south papua": "PAPUA SELATAN",
  "central papua": "PAPUA TENGAH",
  "highland papua": "PAPUA PEGUNUNGAN", "highlands papua": "PAPUA PEGUNUNGAN",
  "papua highlands": "PAPUA PEGUNUNGAN", "papua pegunungan": "PAPUA PEGUNUNGAN",
};

const _EN_DIR_MAP = {
  "south east": "Tenggara", "southeast": "Tenggara",
  "south west": "Barat Daya", "southwest": "Barat Daya",
  "north east": "Timur Laut", "northeast": "Timur Laut",
  "north west": "Barat Laut", "northwest": "Barat Laut",
  "north": "Utara", "south": "Selatan", "east": "Timur", "west": "Barat", "central": "Tengah",
};

// "North Sumatra" -> "Sumatera Utara"; "Central Tapanuli" -> "Tapanuli Tengah";
// "South Jakarta" -> "Jakarta Selatan"; "Riau Islands" -> "Kepulauan Riau".
// Nama yang sudah Bahasa Indonesia dikembalikan apa adanya (Title Case).
function translateEnglishGeo(name) {
  if (!name) return "";
  const s = String(name).trim().replace(/\s+/g, " ");
  if (!s) return "";
  let low = s.toLowerCase();
  // buang kata administratif Inggris di akhir ("Toba Regency" sudah dikupas pemanggil, ini pengaman)
  low = low.replace(/\s+(province|regency|city|district|sub-district|subdistrict|village)\s*$/, "").trim();
  // "X Islands" -> "Kepulauan X" ("Riau Islands" -> "Kepulauan Riau")
  let isIslands = false;
  if (/\bislands\s*$/.test(low)) { isIslands = true; low = low.replace(/\s*islands\s*$/, "").trim(); }
  // pindahkan kata arah Inggris di DEPAN ke belakang dalam Bahasa Indonesia
  const dirKeys = Object.keys(_EN_DIR_MAP).sort((a, b) => b.length - a.length); // "south east" dicek sebelum "south"
  let dirID = "";
  for (const k of dirKeys) {
    if (low === k) { dirID = _EN_DIR_MAP[k]; low = ""; break; }
    if (low.startsWith(k + " ")) { dirID = _EN_DIR_MAP[k]; low = low.slice(k.length).trim(); break; }
  }
  low = low.replace(/^of\s+/, "").trim(); // sisa "City of ..."
  low = low.replace(/\bsumatra\b/g, "sumatera"); // ejaan EN "Sumatra" -> ID "Sumatera"
  let titled = low.split(/\s+/).filter(Boolean).map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(" ");
  if (isIslands) titled = (titled ? "Kepulauan " + titled : "Kepulauan").trim();
  if (dirID) titled = (titled ? titled + " " + dirID : dirID).trim();
  return titled;
}

function _cleanAddrPart(p) {
  // buang kode pos di akhir ("North Sumatra 22253" -> "North Sumatra")
  return String(p || "").trim().replace(/\s+\d{5}\s*$/, "").trim();
}

function _stripPlusCode(p) {
  // plus code kadang menempel dengan nama ("9CCJ+944 Hutarea" -> "Hutarea")
  const s = String(p || "").trim();
  const m = s.match(/^[A-Z0-9]{4}\+\S*\s+(.+)$/i);
  if (m) return m[1].trim();
  if (/^[A-Z0-9]{4}\+\S*\s*$/i.test(s)) return "";
  return s;
}

function _isNoisePart(p) {
  // Bagian alamat yang BUKAN nama wilayah: plus code, "Unnamed Road", angka/kode pos,
  // sebutan administratif telanjang ("City", "Kota", "RT.7/RW.6"), dsb.
  const s = _stripPlusCode(String(p || "").trim());
  if (!s) return true;
  const low = s.toLowerCase();
  if (/^[a-z0-9]{4}\+/.test(low)) return true;
  if (/^(unnamed\s+road|unnamed\s+rd)\b/.test(low)) return true;
  if (/^[\d\s.,\-/]+$/.test(s)) return true;
  if (["city", "cbd", "jalan", "jl", "jl.", "gang", "kota", "kabupaten", "kab.", "kecamatan", "kec.", "kelurahan", "desa", "dusun", "pulau sumatera", "pulau"].includes(low)) return true;
  if (/^(rt\.?|rw\.?|no\.?)\b[\s\d./rwrtno]*$/i.test(s)) return true;
  if (/^(gereja(\s+katolik)?|pastoran|paroki)\s*$/i.test(s)) return true;
  return false;
}

function _isStreetLike(c) {
  // Kandidat yang terlihat seperti nama jalan/nomor/gedung/gereja -> jangan jadikan wilayah.
  // NB: pakai \b agar "No" tidak cocok dengan "North", "Jl" tidak cocok dengan "Jli", dsb.
  return /^(Jl\b\.?|Jalan\b|Gang\b|RT\b\.?|RW\b\.?|No\b\.?|Unnamed\b|Blok\b|Km\b|Gedung\b|Kompleks\b|Komplek\b|Perumnas\b|Perumahan\b|Kapling\b|Kav\b\.?|Lantai\b|Lt\b\.?|St\b|Gereja\b|Pastoran\b|Paroki\b|Musala\b|Masjid\b|Pulau\s+Sumatera\s*$)/i.test(String(c || "").trim());
}

function _expandAbbrev(low) {
  // "Nusa Tenggara Tim." -> "nusa tenggara timur", "Dusun Tim." -> "dusun timur"
  return String(low || "")
    .replace(/\but\.?(?=\s|$)/g, "utara")
    .replace(/\btim\.?(?=\s|$)/g, "timur")
    .replace(/\bteng\.?(?=\s|$)/g, "tengah")
    .replace(/\bsel\.?(?=\s|$)/g, "selatan")
    .replace(/\bbar\.?(?=\s|$)/g, "barat")
    .replace(/\s+/g, " ").trim();
}

function _matchProvinsi(partRaw) {
  const cleaned = _cleanAddrPart(partRaw);
  if (!cleaned) return "";
  // 1) kamus Inggris/alias dulu (agar "Riau Islands" -> KEPULAUAN RIAU, bukan "RIAU")
  const low = cleaned.toLowerCase().replace(/\s+/g, " ").replace(/\s+province\s*$/, "").trim();
  if (EN_PROVINCE_MAP[low]) return EN_PROVINCE_MAP[low];
  const exp = _expandAbbrev(low);
  if (EN_PROVINCE_MAP[exp]) return EN_PROVINCE_MAP[exp];
  // 2) Bahasa Indonesia langsung (nama panjang dicek dulu agar "Kepulauan Riau" menang atas "Riau")
  const up = cleaned.toUpperCase();
  const upExp = exp.toUpperCase();
  const sorted = [...PROVINSI_LIST].sort((a, b) => b.length - a.length);
  for (const p of sorted) {
    if (up.includes(p) || upExp.includes(p)) return p;
  }
  if (/(^|[\s,])JAKARTA([\s,]|$)/.test(up)) return "DAERAH KHUSUS IBUKOTA JAKARTA";
  // 3) generik: terjemahkan arah EN lalu cocokkan ("North Sumatra" -> "Sumatera Utara")
  const tr = translateEnglishGeo(cleaned);
  if (tr) {
    const tru = tr.toUpperCase();
    for (const p of sorted) {
      if (tru === p || tru.includes(p) || p.includes(tru)) return p;
    }
    if (tru === "JAKARTA") return "DAERAH KHUSUS IBUKOTA JAKARTA";
  }
  return "";
}

function _matchKabupaten(partRaw) {
  // Mengembalikan nama kabupaten/kota (tanpa "Kabupaten/Kota/Regency/City") atau "".
  const cleaned = _cleanAddrPart(_stripPlusCode(partRaw));
  if (!cleaned || _isNoisePart(partRaw)) return "";
  let m;
  // Indonesia: "Kabupaten X" / "Kota X" / "Kab. X"
  m = cleaned.match(/^(?:Kota|Kabupat[ae]n|Kab\.)\s+(.+)/i);
  if (m && m[1].trim()) return m[1].trim();
  // Inggris: "X Regency" / "X City" / "City of X"
  m = cleaned.match(/^(.*?)\s+Regency$/i);
  if (m && m[1].trim()) return translateEnglishGeo(m[1].trim());
  m = cleaned.match(/^(.*?)\s+City$/i);
  if (m && m[1].trim()) return translateEnglishGeo(m[1].trim());
  m = cleaned.match(/^City\s+of\s+(.+)/i);
  if (m && m[1].trim()) return translateEnglishGeo(m[1].trim());
  return "";
}

function extractWilayah(alamat) {
  const result = { provinsi: "", kabupaten: "", kecamatan: "", kelurahan: "" };
  if (!alamat) return result;
  let parts = String(alamat).split(",").map(p => p.trim()).filter(Boolean);

  // 1) Provinsi: cari dari belakang (posisi paling akhir)
  for (let idx = parts.length - 1; idx >= 0; idx--) {
    const found = _matchProvinsi(parts[idx]);
    if (found) { result.provinsi = found; parts.splice(idx, 1); break; }
  }
  // normalisasi varian Jakarta ke nama kanonis DB (provinces.name)
  if (result.provinsi === "DKI JAKARTA") result.provinsi = "DAERAH KHUSUS IBUKOTA JAKARTA";

  // 2) Kabupaten/Kota: pola Indonesia (Kabupaten/Kota/Kab.) atau Inggris (X Regency / X City / City of X)
  for (let idx = parts.length - 1; idx >= 0; idx--) {
    const name = _matchKabupaten(parts[idx]);
    if (name) { result.kabupaten = name; parts.splice(idx, 1); break; }
  }
  // fallback: bagian non-noise terakhir dianggap kabupaten
  // (mis. "..., Porsea, Toba" -> kabupaten "Toba")
  if (!result.kabupaten) {
    for (let idx = parts.length - 1; idx >= 0; idx--) {
      if (_isNoisePart(parts[idx])) continue;
      const c = _cleanAddrPart(_stripPlusCode(parts[idx]));
      if (!c) continue;
      if (_isStreetLike(c)) continue;
      const cand = translateEnglishGeo(c);
      if (cand && cand.length < 60) { result.kabupaten = cand; parts.splice(idx, 1); break; }
    }
  }

  // 3) Kecamatan: prefiks Indonesia (Kec./Kecamatan) atau Inggris (X District) dulu ...
  for (let idx = parts.length - 1; idx >= 0; idx--) {
    if (_isNoisePart(parts[idx])) continue;
    const c = _cleanAddrPart(_stripPlusCode(parts[idx]));
    if (!c) continue;
    let m = c.match(/^(?:Kecamat[ae]n|Kec\.|Distrik)\s+(.+)/i);
    if (m && m[1].trim()) { result.kecamatan = translateEnglishGeo(m[1].trim()); parts.splice(idx, 1); break; }
    m = c.match(/^(.*?)\s+(?:District|Sub-?district)$/i);
    if (m && m[1].trim()) { result.kecamatan = translateEnglishGeo(m[1].trim()); parts.splice(idx, 1); break; }
  }
  // ... fallback: bagian non-noise terakhir = kecamatan (alamat tanpa "Kec.",
  // mis. "Laut Dendang, Percut Sei Tuan" -> kecamatan "Percut Sei Tuan")
  if (!result.kecamatan) {
    for (let idx = parts.length - 1; idx >= 0; idx--) {
      if (_isNoisePart(parts[idx])) continue;
      const c = _cleanAddrPart(_stripPlusCode(parts[idx]));
      if (!c) continue;
      if (_isStreetLike(c)) continue;
      const tr = translateEnglishGeo(c);
      if (tr && tr.length < 60) { result.kecamatan = tr; parts.splice(idx, 1); break; }
    }
  }

  // 4) Kelurahan/Desa: bagian non-noise terakhir yang tersisa
  for (let pass = 0; pass < 2 && !result.kelurahan; pass++) {
    const relaxed = pass === 1; // pass 2: izinkan nama berawalan "Kota ..." (mis. kelurahan "Kota Uneng")
    for (let idx = parts.length - 1; idx >= 0; idx--) {
      if (_isNoisePart(parts[idx])) continue;
      let c = _cleanAddrPart(_stripPlusCode(parts[idx]));
      if (!c) continue;
      c = c.replace(/^(?:Kelurahan|Kel\.|Desa|Dusun|Dsn\.?|Ling\.?|Kp\.?|Kampung|Gampong|Jorong|Nagari|Banjar|Dukuh|Village)\s+/i, "").trim();
      if (!c) continue;
      if (!relaxed && /^(?:Kota|Kabupat[ae]n|Kab\.|Kecamat[ae]n|Kec\.)\b/i.test(c)) continue;
      if (_isStreetLike(c)) continue;
      if (c.length >= 60) continue;
      if (result.kecamatan && c.toLowerCase() === result.kecamatan.toLowerCase()) continue;
      if (result.kabupaten && c.toLowerCase() === result.kabupaten.toLowerCase()) continue;
      result.kelurahan = translateEnglishGeo(c);
      break;
    }
  }
  return result;
}

function getPlatform(website) {
  if (!website) return "website";
  const w = String(website).toLowerCase();
  if (w.includes("instagram.com")) return "instagram";
  if (w.includes("facebook.com") || w.includes("fb.com")) return "facebook";
  if (w.includes("twitter.com") || w.includes("x.com")) return "twitter";
  if (w.includes("youtube.com") || w.includes("youtu.be")) return "youtube";
  if (w.includes("tiktok.com")) return "tiktok";
  if (w.includes("linkedin.com")) return "linkedin";
  if (w.includes("wa.me") || w.includes("whatsapp")) return "whatsapp";
  if (w.includes("t.me") || w.includes("telegram")) return "telegram";
  return "website";
}

async function loadAllNamaGerejaFromDB() {
  let conn;
  try {
    conn = await mysql.createConnection(DB_CONFIG);
    const [rows] = await conn.execute("SELECT nama_gereja FROM gereja");
    const set = new Set();
    for (const r of rows) set.add(String(r.nama_gereja).trim().toLowerCase());
    console.log(`[CACHE] Loaded ${set.size} nama_gereja dari DB`);
    return set;
  } catch (e) {
    console.log(`  [CACHE DB] Error: ${e.message}`);
    return new Set();
  } finally { if (conn) await conn.end(); }
}

function namaGerejaExistsCached(cacheSet, nama) {
  return cacheSet.has(String(nama).trim().toLowerCase());
}

async function buildCookieHeader(driver) {
  try {
    const cookies = await driver.manage().getCookies();
    return cookies.map(c => `${c.name}=${c.value}`).join('; ');
  } catch (_) { return ''; }
}

async function loadExistsViaAPI(driver, allNames) {
  // Batch API: POST /admin/gereja/check-batch {names:[...]} -> {data:{ "Nama": true/false }}
  // Cepat: 1 HTTP request untuk 10k+ names vs 10k query / 10k UI filter
  // Endpoint GET single juga tersedia: GET /admin/gereja/check?nama=...
  try {
    const cookie = await buildCookieHeader(driver);
    if (!cookie) throw new Error('No cookies');
    // chunk jika terlalu besar (batas body)
    const chunkSize = 2000;
    const existsSet = new Set();
    const totalChunks = Math.ceil(allNames.length / chunkSize);
    for (let i = 0; i < allNames.length; i += chunkSize) {
      const idx = Math.floor(i/chunkSize)+1;
      const chunk = allNames.slice(i, i + chunkSize);
      const endpoint = `${BASE_URL}/admin/gereja/check-batch`;
      console.log(`[API] POST ${endpoint} [chunk ${idx}/${totalChunks}] ${chunk.length} names...`);
      const t0 = Date.now();
      const res = await fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Cookie': cookie },
        body: JSON.stringify({ names: chunk })
      });
      const elapsed = Date.now()-t0;
      if (!res.ok) throw new Error(`HTTP ${res.status} ${await res.text().then(t=>t.slice(0,200))}`);
      const json = await res.json();
      const map = json.data || {};
      let foundInChunk = 0;
      for (const [k, v] of Object.entries(map)) if (v) { existsSet.add(String(k).trim().toLowerCase()); foundInChunk++; }
      console.log(`[API] <- ${res.status} chunk ${idx}/${totalChunks} ${foundInChunk}/${chunk.length} sudah ada (${elapsed}ms)`);
    }
    console.log(`[API] Batch check selesai: ${existsSet.size}/${allNames.length} sudah ada (via POST /admin/gereja/check-batch)`);
    // Contoh GET single juga tersedia: GET /admin/gereja/check?nama=Gereja%20A -> {"data":{"exists":true}}
    if (allNames.length>0) console.log(`[API] Contoh GET single: GET ${BASE_URL}/admin/gereja/check?nama=${encodeURIComponent(allNames[0].slice(0,40))} -> pakai batch cache`);
    return existsSet;
  } catch (e) {
    console.log(`[API] Gagal batch check: ${e.message} -> fallback DB`);
    return null;
  }
}

async function checkSingleViaAPI(driver, nama) {
  try {
    const cookie = await buildCookieHeader(driver);
    const url = `${BASE_URL}/admin/gereja/check?nama=${encodeURIComponent(String(nama).trim())}`;
    const res = await fetch(url, { headers: { 'Cookie': cookie, 'Accept': 'application/json' } });
    const text = await res.text();
    if (!res.ok) {
      console.log(`  [API GET] HTTP ${res.status} body=${text.slice(0,200)}`);
      return null;
    }
    let json;
    try { json = JSON.parse(text); } catch (e) {
      console.log(`  [API GET] JSON parse fail body=${text.slice(0,300)}`);
      return null;
    }
    return !!(json.data && json.data.exists);
  } catch (e) { console.log(`  [API GET] fetch error ${e.message}`); return null; }
}

async function ensureOnGerejaPage(driver) {
  try {
    const url = await driver.getCurrentUrl();
    if (!url.includes("/admin/gereja")) {
      console.log("   [Recovery] Halaman berubah, kembali ke /admin/gereja...");
      await driver.get(`${BASE_URL}/admin/gereja`);
      await driver.sleep(800);
    }
    // Pastikan modal tertutup
    const backdrops = await driver.findElements(By.css(".modal-backdrop"));
    if (backdrops.length > 0) {
      console.log("   [Recovery] Menutup backdrop yang tersisa...");
      await driver.executeScript(`
        document.querySelectorAll('.modal-backdrop').forEach(e=>e.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        const m = document.getElementById('modalAdd');
        if (m) { m.classList.remove('show'); m.style.display='none'; }
      `);
      await driver.sleep(300);
    }
    const modal = await driver.findElements(By.css("#modalAdd.show"));
    if (modal.length > 0) {
      console.log("   [Recovery] Menutup modal yang tersisa...");
      await driver.executeScript(`
        const m = document.getElementById('modalAdd');
        if (window.bootstrap && bootstrap.Modal.getInstance(m)) bootstrap.Modal.getInstance(m).hide();
        else if (window.jQuery) jQuery(m).modal('hide');
        else { m.classList.remove('show'); m.style.display='none'; }
        document.querySelectorAll('.modal-backdrop').forEach(e=>e.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
      `);
      await driver.sleep(300);
    }
    return true;
  } catch (e) {
    console.log(`   [Recovery] Error: ${e.message}. Memaksa navigasi ulang...`);
    await driver.get(`${BASE_URL}/admin/gereja`);
    await driver.sleep(1000);
    return true;
  }
}

// ---------------------------------------------------------------------------
// select2_set - mirror Python select2_set()
// ---------------------------------------------------------------------------
async function select2Set(driver, selectId, value) {
  const sel = await driver.findElement(By.id(selectId));
  const options = await sel.findElements(By.css("option"));
  const opts = [];
  for (const o of options) {
    const val = await o.getAttribute("value");
    const txt = await o.getText();
    opts.push({ val: val || "", txt: txt || "" });
  }
  let found = null;
  for (const { val, txt } of opts) {
    if (txt.toUpperCase().trim() === value.toUpperCase().trim() || val.toUpperCase().trim() === value.toUpperCase().trim()) {
      found = { val, txt }; break;
    }
  }
  if (!found) {
    for (const { val, txt } of opts) {
      if (txt.toUpperCase().includes(value.toUpperCase()) || val.toUpperCase().includes(value.toUpperCase())) {
        found = { val, txt }; break;
      }
    }
  }
  if (!found) {
    // fallback: abaikan spasi/tanda baca ("Tanjung Pinang" ~= "TANJUNGPINANG",
    // "Siborong Borong" ~= "SIBORONGBORONG") - umum di data terjemahan Inggris
    const norm = (s) => String(s || "").toUpperCase().replace(/[^A-Z0-9]/g, "");
    const nv = norm(value);
    if (nv.length >= 3) {
      for (const { val, txt } of opts) {
        const nt = norm(txt), nvv = norm(val);
        if ((nt.length >= 3 && (nt.includes(nv) || nv.includes(nt))) ||
            (nvv.length >= 3 && (nvv.includes(nv) || nv.includes(nvv)))) {
          found = { val, txt }; break;
        }
      }
    }
  }
  if (!found) {
    console.log(`   Debug ${selectId}:`, opts.map(o=>`${o.val}=>${o.txt}`).join(" | ").slice(0,300));
    return false;
  }

  // try select2 UI
  try {
    const selection = await driver.findElement(By.css(`span[aria-labelledby='select2-${selectId}-container']`));
    await driver.executeScript("arguments[0].click();", selection);
    await driver.sleep(150);
    try {
      const search = await driver.findElement(By.css("input.select2-search__field"));
      await search.clear();
      await search.sendKeys(found.txt);
      await driver.sleep(150);
    } catch (_) {}
    const option = await driver.wait(until.elementLocated(By.xpath(`//li[contains(@class,'select2-results__option')][normalize-space()='${found.txt}']`)), 3000);
    await driver.wait(until.elementIsVisible(option), 3000);
    await driver.wait(until.elementIsEnabled(option), 3000);
    await option.click();
    return true;
  } catch (_) {}

  // fallback: native select + trigger change
  try {
    await driver.executeScript(`
      var sel = arguments[0];
      var val = arguments[1];
      sel.value = val;
      sel.dispatchEvent(new Event('change', {bubbles: true}));
      if (window.jQuery) jQuery(sel).trigger('change.select2');
    `, sel, found.val);
    return true;
  } catch (_) { return false; }
}

// ---------------------------------------------------------------------------
// Excel loading (preview + automation) - sekarang per-file agar proses file jelas
// ---------------------------------------------------------------------------
function loadExcelRows(excelPath) {
  // kompatibilitas: tetap load single path atau gabungan (dipakai legacy)
  if (!excelPath) {
    const sources = loadExcelSources(null);
    if (!sources.length) throw new Error("Tidak ada file .xlsx ditemukan");
    // gabung untuk kompatibilitas return lama
    const allRows = sources.flatMap(s=>s.rows);
    return { header: sources[0].header, rows: allRows, source: `${sources.length} files gabungan`, path: sources.map(s=>s.path).join(", ") };
  }
  const wb = XLSX.readFile(excelPath, { cellDates: false });
  const ws = wb.Sheets[wb.SheetNames[0]] || wb.Sheets[wb.SheetNames[0]];
  const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: null });
  const header = rows[0];
  const dataRows = rows.slice(1);
  return { header, rows: dataRows, source: path.basename(excelPath), path: excelPath };
}

function loadExcelSources(custom) {
  const files = resolveExcelSources(custom);
  if (!files.length) throw new Error("Tidak ada file .xlsx ditemukan");
  const sources = [];
  for (const f of files) {
    const wb = XLSX.readFile(f, { cellDates: false });
    const sheetName = wb.SheetNames[0];
    const ws = wb.Sheets[sheetName];
    const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: null });
    const header = rows[0] || [];
    const dataRows = rows.slice(1);
    const stat = fs.existsSync(f) ? fs.statSync(f) : null;
    sources.push({ path: f, file: path.basename(f), header, rows: dataRows, sheetName, sizeKB: stat ? (stat.size/1024).toFixed(1) : "?" });
  }
  return sources;
}

function previewExcel(excelPath, limit=5) {
  const sources = excelPath ? [loadExcelRows(excelPath)].map(r=>({ file: path.basename(r.path), path: r.path, header: r.header, rows: r.rows, sizeKB: "?" })) : loadExcelSources(null);
  const actualSources = excelPath ? loadExcelSources(excelPath) : sources;
  console.log(boxTitle("ADDGEREJA — Preview Excel  (mode --preview)"));
  console.log(`\n${S.info} ${col(`Ditemukan ${actualSources.length} file .xlsx`, C.bold)}  ${col(`limit ${limit}/file`, C.dim)}`);
  let grandTotal = 0;
  for (let sIdx=0; sIdx<actualSources.length; sIdx++) {
    const s = actualSources[sIdx];
    grandTotal += s.rows.length;
    console.log(`\n${col("┌─",C.cyan)} ${badge(`FILE ${sIdx+1}/${actualSources.length}`, C.bgBlue+C.white)} ${col(s.file, C.bold+C.white)} ${col(`(${s.sizeKB} KB)`,C.dim)} ${col(`Sheet: ${s.sheetName}`,C.gray)} ${col(`${s.rows.length} rows × ${s.header.length} cols`, C.cyan)}`);
    console.log(`${col("│",C.cyan)}  ${col("Path",C.dim)}  ${S.arrow} ${col(s.path, C.gray)}`);
    console.log(`${col("│",C.cyan)}  ${col("Header",C.dim)} ${S.arrow} ${col(truncate(s.header.join(" | "), 110), C.white)}`);
    const preview = s.rows.slice(0, limit).map(r=>{ const obj={}; s.header.forEach((h,i)=> obj[h]=r[i]); return obj; });
    console.log(`${col("│",C.cyan)}  ${col(`Preview ${Math.min(limit,s.rows.length)} baris:`, C.yellow)}`);
    console.table(preview);
    if (s.rows.length > limit) console.log(`${col("│",C.cyan)}  ${col(`… ${s.rows.length-limit} baris lagi (total ${s.rows.length})`, C.dim)}`);
    console.log(`${col("│",C.cyan)}  ${col("Kolom penting:",C.dim)} ${col("name=1",C.green)} ${S.dot} ${col("website=7",C.cyan)} ${S.dot} ${col("phone=8",C.yellow)} ${S.dot} ${col("address=18",C.magenta)} ${S.dot} ${col("link=20",C.blue)}`);
    preview.forEach((obj,i)=>{
      const r = s.rows[i];
      const name = r[1] ? col(truncate(r[1],48), C.bold+C.white) : col("(empty)",C.red);
      console.log(`${col("│",C.cyan)}   ${col(`#${i+1}`,C.dim)} ${name} ${S.arrow} ${col(truncate(String(r[18]||"-"),42),C.gray)} ${S.dot} ${col(String(r[8]||"-"),C.yellow)}`);
    });
    console.log(col("└"+("─".repeat(70)), C.cyan));
  }
  console.log(`\n${col("▶ SUMMARY",C.bold+C.cyan)}  ${col(`${actualSources.length} file`,C.bold)} ${S.dot} ${col(`Total rows: ${grandTotal}`, C.bold+C.green)}  ${bar(grandTotal, grandTotal)}`);
  if (actualSources.length>1) {
    console.log(`\n${col("Ringkasan per-file:", C.bold)}`);
    console.table(actualSources.map((s,i)=>({ "#": i+1, file: col(s.file,C.cyan), rows: col(String(s.rows.length),C.bold), sizeKB: s.sizeKB })));
  }
}

// ---------------------------------------------------------------------------
// Main automation
// ---------------------------------------------------------------------------
async function runAutomation(opts) {
  const sources = loadExcelSources(opts.excel);
  const totalRows = sources.reduce((a,s)=>a+s.rows.length, 0);
  const header = sources[0]?.header || [];
  console.log(boxTitle("ADDGEREJA  —  Automation"));
  console.log(`${S.info} ${col(`Ditemukan ${sources.length} file Excel`,C.bold)}  ${bar(sources.length, sources.length)}`);
  sources.forEach((s,i)=>{
    const idx = col(`[${String(i+1).padStart(2)}/${sources.length}]`, C.dim);
    console.log(`  ${idx} ${col(s.file, C.cyan+C.bold)} ${col(`(${s.sizeKB} KB`,C.dim)} ${S.dot} ${col(`${s.rows.length} rows`, C.yellow)}${col(")",C.dim)} ${col("→",C.gray)} ${col(s.path, C.gray)}`);
  });
  console.log(`\n  ${col("BASE_URL",C.dim)} ${S.arrow} ${col(BASE_URL, C.cyan)}`);
  console.log(`  ${col("LOG_PATH",C.dim)} ${S.arrow} ${col(LOG_PATH, C.gray)}`);
  console.log(`  ${col("DB",C.dim)}       ${S.arrow} ${col(`${DB_CONFIG.host}/${DB_CONFIG.database}`, C.yellow)}  ${S.dot} ${col(`Header: ${header.slice(0,6).join(", ")} … (${header.length} cols)`, C.dim)}`);
  console.log(`  ${col("TOTAL",C.dim)}    ${S.arrow} ${col(String(totalRows), C.bold+C.green)} rows ${bar(0,totalRows)}`);

  // DB cache untuk dry-run (tanpa login API tidak tersedia)
  let dbCacheSet = null;
  if (opts.dryRun) {
    console.log(`\n${col("▶ Meng-cache nama_gereja dari DB (dry-run)...", C.yellow)}`);
    dbCacheSet = await loadAllNamaGerejaFromDB();
    console.log(boxTitle("DRY-RUN  —  Cek Skip Logic (tanpa browser)"));
    console.log(`${col("Rule:",C.bold)} skip ${badge("HANYA",C.bgGreen)} jika ${col("LOG",C.cyan)} ${S.dot} ${col("DB",C.yellow)} ada keduanya`);
    console.log(`${col("Jika salah satu tidak ada → AKAN PROSES",C.dim)} (log tidak ditulis ulang jika sudah ada)\n`);
    let skipBoth=0, skipEmpty=0, willProcess=0, willProcessLogExists=0, willProcessDbExists=0;
    let globalIdx=0;
    for (let sIdx=0; sIdx<sources.length; sIdx++) {
      const src = sources[sIdx];
      console.log(`\n${col("┌─",C.cyan)} ${badge(`FILE ${sIdx+1}/${sources.length}`, C.bgBlue)} ${col(src.file, C.bold)} ${col(`(${src.rows.length} rows)`,C.dim)} ${bar(0, src.rows.length)}`);
      for (let idx=0; idx<src.rows.length; idx++) {
        globalIdx++;
        const row = src.rows[idx];
        const nameVal = row[1];
        const tag = `${col(`[F${sIdx+1} ${src.file}`,C.dim)} ${col(`${idx+1}/${src.rows.length}`,C.cyan)} ${col(`G${globalIdx}/${totalRows}`,C.yellow)}${col("]",C.dim)}`;
        if (!nameVal) { skipEmpty++; console.log(`  ${tag} ${badge("SKIP EMPTY", C.bgYellow)}`); continue; }
        const inLog = namaGerejaInLog(nameVal);
        const inDb = namaGerejaExistsCached(dbCacheSet, String(nameVal));
        const checkIcon = inDb ? col("● SUDAH ADA", C.green) : col("○ BELUM ADA", C.yellow);
        console.log(`  ${tag} ${S.arrow} ${col(truncate(nameVal,52), C.white)} ${S.dot} ${checkIcon} ${S.dot} inLog=${inLog?col("ya",C.green):col("tidak",C.dim)} ${col("(dry-run DB)",C.dim)}`);
        if (inLog && inDb) { skipBoth++; console.log(`  ${" ".repeat(6)}${S.skip} ${col(`SKIP LOG+DB`,C.gray)} ${col(nameVal, C.dim)}`); continue; }
        willProcess++;
        if (inLog && !inDb) willProcessLogExists++;
        if (!inLog && inDb) willProcessDbExists++;
        const detailStatus = inLog ? "[AKAN PROSES - sudah di LOG, belum di DB -> input tapi tidak tulis log ulang]" : (!inDb ? "[AKAN PROSES - belum di LOG & belum di DB]" : "[AKAN PROSES - belum di LOG, sudah di DB -> input & akan tulis log]");
        if (willProcess<=10) {
          const w = extractWilayah(String(row[18]||""));
          console.log(`  ${tag} ${col(detailStatus, inDb?C.yellow:C.green)} ${col(truncate(nameVal,44),C.white)}`);
          console.log(`       ${S.dot} ${col("alamat",C.dim)} ${truncate(String(row[18]||"-"),46)} ${S.dot} ${col("wilayah",C.dim)} ${col(JSON.stringify(w),C.cyan)} ${S.dot} ${col("platform",C.dim)} ${col(getPlatform(row[7]),C.magenta)}`);
        } else if (willProcess===11) {
          console.log(`  ${col(`… ${totalRows-globalIdx} baris lagi tidak ditampilkan`, C.dim)}`);
        }
      }
    }
    console.log(boxTitle("DRY-RUN SUMMARY"));
    console.log(`${col("Files",C.dim)} ${S.arrow} ${sources.length}  ${S.dot} ${col("Total",C.dim)} ${totalRows}  ${S.dot} ${badge(`SKIP EMPTY ${skipEmpty}`,C.bgYellow)} ${badge(`SKIP LOG+DB ${skipBoth}`,C.bgGreen)} ${badge(`AKAN PROSES ${willProcess}`,C.bgBlue)}`);
    console.log(`${col(`dari AKAN PROSES: sudahDiLog=${willProcessLogExists} sudahDiDb=${willProcessDbExists}`, C.dim)}`);
    console.table(sources.map((s,i)=>({ "#": i+1, file: col(s.file,C.cyan), rows: col(String(s.rows.length),C.bold), sizeKB: s.sizeKB })));
    return;
  }

  // Selenium setup
  const chromeOpts = new chrome.Options();
  if (opts.headless) { chromeOpts.addArguments("--headless=new", "--disable-gpu"); }
  chromeOpts.addArguments("--no-sandbox","--disable-dev-shm-usage","--window-size=1920,1080");
  chromeOpts.addArguments("--disable-blink-features=AutomationControlled");

  // chromedriver service otomatis dari npm chromedriver
  let driver = await new Builder().forBrowser("chrome").setChromeOptions(chromeOpts).build();
  try {
    await driver.manage().window().maximize();
    const wait = { until: until, timeout: 10000 };

    console.log(`\n${col("▶",C.cyan)} ${col("1. Buka halaman login",C.bold)}  ${col(BASE_URL+"/login",C.dim)}`);
    await driver.get(`${BASE_URL}/login`);
    await driver.wait(until.elementLocated(By.name("email")), 10000);
    await driver.findElement(By.name("email")).sendKeys(EMAIL);
    await driver.findElement(By.name("password")).sendKeys(PASSWORD);
    await driver.findElement(By.css("button[type='submit']")).click();
    console.log(`  ${S.ok} ${col("Login berhasil", C.green+C.bold)} ${col(`(${EMAIL})`,C.dim)}`);
    await driver.sleep(800);

    console.log(`\n${col("▶",C.cyan)} ${col("2. Buka admin/gereja",C.bold)}  ${col(BASE_URL+"/admin/gereja",C.dim)}`);
    await driver.get(`${BASE_URL}/admin/gereja`);
    await driver.sleep(800);

    const apiCache = new Map();
    let dbCacheSet = new Set();
    console.log(`\n${S.info} ${col("Mode per-item API GET",C.bold+C.cyan)} ${col(`${BASE_URL}/admin/gereja/check?nama=...`,C.dim)} ${badge("identik add()",C.bgGreen)}`);

    let globalIdx = 0;
    let globalBerhasil = 0;
    let globalSkipBoth = 0;
    let globalGagal = 0;
    const startedAt = Date.now();
    for (let sIdx=0; sIdx<sources.length; sIdx++) {
      const src = sources[sIdx];
      const pctFile = ((sIdx+1)/sources.length*100).toFixed(0);
      console.log(`\n${col("┏━",C.cyan)} ${badge(`FILE ${sIdx+1}/${sources.length}`, C.bgBlue)} ${col(src.file, C.bold+C.white)} ${col(`${src.rows.length} rows`,C.yellow)} ${col(`(${src.sizeKB} KB)`,C.dim)} ${col(pctFile+"%",C.bold)}`);
      console.log(`${col("┃",C.cyan)}  ${col(src.path, C.gray)}`);
      console.log(`${col("┗━",C.cyan)} ${hr("━",68)}`);
      for (let idx=0; idx<src.rows.length; idx++) {
        globalIdx++;
        const row = src.rows[idx];
        const nameVal = row[1];
        const addressVal = row.length > 18 ? row[18] : null;
        const linkVal = row.length > 20 ? row[20] : null;
        const phoneVal = row.length > 8 ? row[8] : null;
        const websiteVal = row.length > 7 ? row[7] : null;

        if (!nameVal) continue;
        const progLabel = col(`[F${sIdx+1}/${sources.length} ${String(idx+1).padStart(2)}/${src.rows.length}  G${globalIdx}/${totalRows}]`, C.dim);
        const gPct = ((globalIdx/totalRows)*100).toFixed(1);
        console.log(`\n${col("─".repeat(72), C.gray)}`);
        console.log(`${progLabel} ${bar(globalIdx, totalRows)}  ${col(`${gPct}%`, C.bold)}`);
        console.log(`${col("▸",C.cyan)} ${col(`Data ke-${globalIdx}`,C.bold)} ${S.arrow} ${col(truncate(nameVal,64), C.bold+C.white)}`);

        const inLog = namaGerejaInLog(nameVal);
        const cacheKey = String(nameVal).trim().toLowerCase();
        let inDb;
        const apiUrl = `${BASE_URL}/admin/gereja/check?nama=${encodeURIComponent(String(nameVal).trim())}`;
        if (apiCache.has(cacheKey)) {
          inDb = apiCache.get(cacheKey);
          const icon = inDb ? col("● SUDAH ADA",C.green) : col("○ BELUM ADA",C.yellow);
          console.log(`  ${col("[API GET]",C.dim)} ${col(truncate(apiUrl,68),C.gray)} ${S.arrow} ${icon} ${col("(cache)",C.dim)} ${S.dot} inLog=${inLog?col("ya",C.green):col("tidak",C.dim)}`);
        } else {
          const t0 = Date.now();
          const live = await checkSingleViaAPI(driver, String(nameVal));
          const elapsed = Date.now() - t0;
          if (live === null) {
            inDb = false;
            console.log(`  ${col("[API GET]",C.yellow)} ${col(truncate(apiUrl,68),C.gray)} ${S.arrow} ${col("GAGAL",C.red)} ${col(`(${elapsed}ms)`,C.dim)} ${S.arrow} ${col("fallback BELUM ADA",C.dim)} ${S.dot} inLog=${inLog?col("ya",C.green):col("tidak",C.dim)}`);
          } else {
            inDb = live;
            const icon = inDb ? col("● SUDAH ADA",C.green+C.bold) : col("○ BELUM ADA",C.yellow);
            console.log(`  ${col("[API GET]",C.cyan)} ${col(truncate(apiUrl,68),C.gray)} ${S.arrow} ${icon} ${col(`(${elapsed}ms)`,C.dim)} ${S.dot} inLog=${inLog?col("ya",C.green):col("tidak",C.dim)}`);
          }
          apiCache.set(cacheKey, inDb);
          if (inDb) dbCacheSet.add(cacheKey);
        }
        if (inLog && inDb) {
          console.log(`  ${S.skip} ${col("SKIP", C.bgYellow+C.bold)} ${col("sudah di LOG & DB",C.gray)} ${bar(idx+1, src.rows.length)}`);
          globalSkipBoth++;
          continue;
        }
        if (inLog && !inDb) {
          console.log(`  ${col("→",C.yellow)} ${badge("LOG ada / DB belum", C.yellow)} ${col("tetap input (tidak tulis log ulang)",C.dim)}`);
        } else if (!inLog && inDb) {
          console.log(`  ${col("→",C.magenta)} ${badge("LOG belum / DB ada", C.magenta)} ${col("tetap input & nanti tulis log",C.dim)}`);
        } else {
          console.log(`  ${col("→",C.green)} ${badge("BARU", C.bgGreen)} ${col("belum di LOG & DB → input baru",C.white)}`);
        }

        // Recovery: pastikan halaman masih di admin/gereja sebelum proses
        await ensureOnGerejaPage(driver);

        // Wrap seluruh proses input dalam try-catch agar 1 error tidak hentikan semua
        try {
          // Klik Tambah Gereja - tunggu modal benar-benar visible (fix ElementNotInteractableError)
          const btnTambah = await driver.wait(until.elementLocated(By.css("button[data-bs-target='#modalAdd']")), 10000);
          await driver.wait(until.elementIsVisible(btnTambah), 5000);
          await driver.executeScript("arguments[0].scrollIntoView({block:'center'});", btnTambah);
          await driver.executeScript("arguments[0].click();", btnTambah);
          console.log(`  ${col("▶",C.cyan)} ${col("4. Klik Tambah Gereja", C.bold)} ${col("→ #modalAdd",C.dim)}`);
          try { await driver.wait(until.elementLocated(By.css("#modalAdd.show")), 5000); } catch {}
          await driver.sleep(600);

      const namaInput = await driver.wait(until.elementLocated(By.name("nama_gereja")), 5000);
      await driver.wait(until.elementIsVisible(namaInput), 5000);
      await driver.wait(until.elementIsEnabled(namaInput), 5000);
      await driver.executeScript("arguments[0].scrollIntoView({block:'center'});", namaInput);
      await driver.sleep(200);
      try { await namaInput.clear(); } catch { await driver.executeScript("arguments[0].value=''; arguments[0].dispatchEvent(new Event('input',{bubbles:true}));", namaInput); }
      try { await namaInput.sendKeys(String(nameVal)); } catch { await driver.executeScript("arguments[0].value=arguments[1]; arguments[0].dispatchEvent(new Event('input',{bubbles:true})); arguments[0].dispatchEvent(new Event('change',{bubbles:true}));", namaInput, String(nameVal)); }
      console.log(`  ${col("▶",C.cyan)} ${col("5. Nama",C.bold)}  ${S.arrow} ${col(truncate(nameVal,62), C.white)}`);

      if (addressVal) {
        const addrStr = String(addressVal);
        const alamatEl = await driver.findElement(By.name("alamat"));
        await alamatEl.clear();
        await alamatEl.sendKeys(addrStr);
        console.log(`  ${S.dot} ${col("Alamat",C.dim)} ${truncate(addrStr,56)}`);

        const wilayah = extractWilayah(addrStr);
        console.log(`  ${S.dot} ${col("Wilayah",C.dim)} ${col(JSON.stringify(wilayah), C.cyan)}`);

        if (wilayah.provinsi) {
          const ok = await select2Set(driver, "addProvinsi", wilayah.provinsi);
          if (ok) {
            console.log(`  ${S.ok} ${col("Provinsi",C.green)} ${S.arrow} ${col(wilayah.provinsi, C.white)}`);
            await driver.sleep(600);
            if (wilayah.kabupaten) {
              try {
                await driver.wait(async ()=>{
                  const el = await driver.findElement(By.id("addKabupaten"));
                  const dis = await el.getAttribute("disabled");
                  return dis === null;
                }, 5000);
                await driver.sleep(300);
                const ok2 = await select2Set(driver, "addKabupaten", wilayah.kabupaten);
                if (ok2) {
                  console.log(`  ${S.ok} ${col("Kabupaten",C.green)} ${S.arrow} ${col(wilayah.kabupaten, C.white)}`);
                  await driver.sleep(600);
                  if (wilayah.kecamatan) {
                    try {
                      await driver.wait(async ()=>{
                        const el = await driver.findElement(By.id("addKecamatan"));
                        const dis = await el.getAttribute("disabled");
                        return dis === null;
                      }, 5000);
                      await driver.sleep(300);
                      const ok3 = await select2Set(driver, "addKecamatan", wilayah.kecamatan);
                      if (ok3) {
                        console.log(`  ${S.ok} ${col("Kecamatan",C.green)} ${S.arrow} ${col(wilayah.kecamatan, C.white)}`);
                        await driver.sleep(600);
                        if (wilayah.kelurahan) {
                          try {
                            await driver.wait(async ()=>{
                              const el = await driver.findElement(By.id("addKelurahan"));
                              const dis = await el.getAttribute("disabled");
                              return dis === null;
                            }, 5000);
                            await driver.sleep(300);
                            const ok4 = await select2Set(driver, "addKelurahan", wilayah.kelurahan);
                            if (ok4) console.log(`  ${S.ok} ${col("Kelurahan",C.green)} ${S.arrow} ${col(wilayah.kelurahan, C.white)}`);
                            await driver.sleep(300);
                          } catch { console.log(`  ${S.fail} ${col("Kelurahan tidak terpilih: "+wilayah.kelurahan, C.yellow)}`); }
                        }
                      }
                    } catch { console.log(`  ${S.fail} ${col("Kecamatan tidak terpilih: "+wilayah.kecamatan, C.yellow)}`); }
                  }
                }
              } catch { console.log(`  ${S.fail} ${col("Kabupaten tidak terpilih: "+wilayah.kabupaten, C.yellow)}`); }
            }
          } else {
            console.log(`  ${S.warn} ${col("Provinsi tidak ditemukan: "+wilayah.provinsi, C.yellow)}`);
          }
        }
      }

      if (phoneVal) {
        const tel = await driver.findElement(By.name("kontak_telepon"));
        await tel.clear();
        await tel.sendKeys(String(phoneVal));
        console.log(`  ${S.dot} ${col("Telepon",C.dim)} ${col(phoneVal, C.yellow)}`);
      }

      if (linkVal) {
        const originalHandle = await driver.getWindowHandle();
        await driver.executeScript("window.open(arguments[0]);", String(linkVal));
        const handles = await driver.getAllWindowHandles();
        const newHandle = handles[handles.length-1];
        await driver.switchTo().window(newHandle);
        console.log(`  ${col("▶",C.cyan)} ${col("6. Link Maps → tab baru", C.bold)} ${col(truncate(linkVal,48),C.dim)}`);
        await driver.sleep(800);
        try {
          const searchInput = await driver.wait(until.elementLocated(By.css("input[name='q']")), 10000);
          await searchInput.click();
          await driver.sleep(150);
          await searchInput.sendKeys(Key.ENTER);
          console.log(`  ${S.ok} ${col("Klik search + Enter",C.green)}`);
          await driver.sleep(1200);
        } catch {
          console.log(`  ${S.warn} ${col("Input search tidak ditemukan → pakai URL saat ini",C.yellow)}`);
          await driver.sleep(600);
        }
        const currentUrl = await driver.getCurrentUrl();
        await driver.close();
        await driver.switchTo().window(originalHandle);
        await driver.sleep(400);
        const linkInput = await driver.findElement(By.name("link_maps"));
        await linkInput.clear();
        await linkInput.sendKeys(currentUrl);
        await driver.executeScript("arguments[0].dispatchEvent(new Event('input'));", linkInput);
        console.log(`  ${S.dot} ${col("Link Maps",C.dim)} ${col(truncate(currentUrl,76), C.cyan)}`);
      }

      if (websiteVal) {
        const platform = getPlatform(String(websiteVal));
        const sosmedSelect = await driver.findElement(By.css("#addSosmedList select[name='sosmed_platform[]']"));
        const sel = new Select(sosmedSelect);
        try { await sel.selectByValue(platform); } catch { await sel.selectByVisibleText(platform); }
        const sosmedUrl = await driver.findElement(By.css("#addSosmedList input[name='sosmed_url[]']"));
        await sosmedUrl.clear();
        await sosmedUrl.sendKeys(String(websiteVal));
        console.log(`  ${S.dot} ${col("Sosmed",C.dim)} ${col(platform, C.magenta)} ${S.arrow} ${col(truncate(websiteVal,48),C.cyan)}`);
      }

      const simpanBtn = await driver.wait(until.elementLocated(By.css("#modalAdd button[type='submit']")), 5000);
      await driver.wait(until.elementIsVisible(simpanBtn), 5000);
      await driver.wait(until.elementIsEnabled(simpanBtn), 5000);
      await driver.executeScript("arguments[0].scrollIntoView({block:'center'});", simpanBtn);
      await driver.executeScript("arguments[0].click();", simpanBtn);
      console.log(`  ${col("▶",C.green)} ${col(`7. Simpan diklik`,C.bold+C.white)} ${col(`F${sIdx+1} ${idx+1}/${src.rows.length} G${globalIdx}`,C.dim)}`);
      await driver.sleep(3000);

      // Cek apakah muncul pesan error "Data sudah ada di database" (flash message via Lobibox setelah redirect)
      let alreadyExists = false;
      let serverMsg = '';
      try {
        // Lobibox notification: class "lobibox-notify-error" atau "lobibox-notify"
        const notifs = await driver.findElements(By.css(".lobibox-notify"));
        for (const n of notifs) {
          const t = await n.getText();
          if (t) serverMsg = t;
          const cls = await n.getAttribute("class");
          if (t.toLowerCase().includes("sudah ada") || t.toLowerCase().includes("duplicate") || (cls && cls.includes("error"))) {
            if (t.toLowerCase().includes("sudah ada") || t.toLowerCase().includes("duplicate")) {
              console.log(`   [!] SERVER REJECT (lobibox): ${t} -> ${nameVal}`);
              alreadyExists = true; break;
            }
          }
        }
        if (!alreadyExists && notifs.length) {
          // cek error khusus
          try {
            const err = await driver.findElement(By.css(".lobibox-notify-error"));
            const et = await err.getText();
            if (et.toLowerCase().includes("sudah ada")) { console.log(`   [!] SERVER REJECT (lobibox-error): ${et}`); alreadyExists = true; serverMsg = et; }
          } catch {}
        }
      } catch (_) {}
      try {
        const alertDanger = await driver.findElement(By.css(".alert-danger"));
        const alertText = await alertDanger.getText();
        if (alertText.toLowerCase().includes("sudah ada") || alertText.toLowerCase().includes("duplicate")) {
          console.log(`   [!] SERVER REJECT (alert): ${alertText} -> ${nameVal}`);
          alreadyExists = true; serverMsg = alertText;
        }
      } catch (_) {}
      // Juga cek URL flash via page source (kadang lobibox delay)
      try {
        const bodyText = await driver.findElement(By.css("body")).getText();
        if (!alreadyExists && bodyText.toLowerCase().includes("data sudah ada di database")) {
          console.log(`   [!] SERVER REJECT (body): Data sudah ada di database -> ${nameVal}`);
          alreadyExists = true;
        }
      } catch {}
      console.log(`  [API POST] /admin/gereja/add nama="${String(nameVal).slice(0,60)}" -> ${alreadyExists ? 'REJECT SUDAH ADA' : 'OK (diasumsikan berhasil)'} ${serverMsg ? '| msg: '+serverMsg.slice(0,80) : ''}`);

      if (alreadyExists) {
        // Masukkan ke cache supaya duplikat berikutnya langsung terdeteksi (update Map + Set)
        const k = String(nameVal).trim().toLowerCase();
        dbCacheSet.add(k); apiCache.set(k, true);
        console.log(`   [CACHE] Ditambahkan: ${nameVal} (dari server reject) -> apiCache + dbCacheSet`);
      } else {
        // Sukses - tambah ke cache juga
        const k = String(nameVal).trim().toLowerCase();
        dbCacheSet.add(k); apiCache.set(k, true);
        console.log(`   [CACHE] Ditambahkan: ${nameVal} (dari berhasil input) -> apiCache + dbCacheSet`);
      }

      // tunggu modal tertutup & backdrop hilang sebelum loop berikutnya (fix stale/not-interactable di data berikutnya)
      try {
        await driver.wait(async () => {
          const modals = await driver.findElements(By.css("#modalAdd.show"));
          return modals.length === 0;
        }, 5000);
      } catch {}
      try {
        await driver.wait(async () => {
          const backs = await driver.findElements(By.css(".modal-backdrop.show"));
          return backs.length === 0;
        }, 3000);
      } catch {}
      // fallback paksa tutup jika masih nyangkut
      try {
        const modalEl = await driver.findElement(By.id("modalAdd"));
        if (await modalEl.isDisplayed()) {
          await driver.executeScript(`
            const m = document.getElementById('modalAdd');
            if (window.bootstrap && bootstrap.Modal.getInstance(m)) bootstrap.Modal.getInstance(m).hide();
            else if (window.jQuery) jQuery(m).modal('hide');
            document.querySelectorAll('.modal-backdrop').forEach(e=>e.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
          `);
          await driver.sleep(500);
        }
      } catch {}

      if (alreadyExists) {
        console.log(`  ${S.skip} ${badge("SKIP SERVER", C.bgYellow)} ${col("ditolak server",C.yellow)} ${S.arrow} ${col(truncate(nameVal,48),C.dim)} ${col("(sudah ada)",C.dim)}`);
        globalGagal++;
      } else {
        if (!inLog) {
          writeLog(nameVal, "BERHASIL DITAMBAHKAN");
          console.log(`  ${S.ok} ${badge("BERHASIL", C.bgGreen)} ${col(truncate(nameVal,48),C.white)} ${S.arrow} ${col("log ditulis",C.green)}`);
        } else {
          console.log(`  ${S.ok} ${badge("BERHASIL", C.bgGreen)} ${col(truncate(nameVal,48),C.white)} ${S.arrow} ${col("sudah di log → tidak tulis ulang",C.dim)}`);
        }
        globalBerhasil++;
        const elapsed = ((Date.now()-startedAt)/1000).toFixed(1);
        console.log(`  ${col("▸ Progress",C.bold)} ${bar(globalIdx, totalRows)} ${S.dot} ${col(`berhasil: ${globalBerhasil}`,C.green+C.bold)} ${S.dot} ${col(`gagal: ${globalGagal}`,C.dim)} ${S.dot} ${col(fmtTime(elapsed), C.cyan)}`);
      }
        } catch (e) {
          globalGagal++;
          console.log(`\n  ${col("✘ ERROR", C.bgRed+C.white+C.bold)} ${col(truncate(nameVal,52),C.white)}`);
          console.log(`  ${S.dot} ${col(e.message, C.red)}`);
          console.log(`  ${S.dot} ${col(e.stack.split('\n')[0].slice(0,120), C.dim)}`);
          try { await ensureOnGerejaPage(driver); } catch (_) {}
          console.log(`  ${S.skip} ${col("SKIP item → lanjut berikutnya",C.yellow)}  ${bar(globalIdx, totalRows)}`);
        }
      } // end idx loop
      const fileElapsed = ((Date.now()-startedAt)/1000).toFixed(1);
      console.log(`\n${col("┌─ FILE SELESAI",C.green)} ${badge(`${src.file}`, C.bgBlue)} ${col(`(${sIdx+1}/${sources.length})`,C.dim)} ${S.dot} ${col(`kumulatif berhasil: ${globalBerhasil}`, C.bold+C.green)} ${S.dot} ${col(`skip LOG+DB: ${globalSkipBoth}`,C.yellow)} ${S.dot} ${col(fmtTime(fileElapsed),C.cyan)}`);
    } // end sources loop

    const totalElapsed = ((Date.now()-startedAt)/1000).toFixed(1);
    console.log(boxTitle("SELESAI SEMUA DATA"));
    console.log(`${S.ok} ${col(`Files: ${sources.length}`,C.bold)}  ${S.dot} ${col(`Total: ${totalRows}`,C.white)}  ${S.dot} ${badge(`BERHASIL ${globalBerhasil}`,C.bgGreen)}  ${badge(`SKIP ${globalSkipBoth}`,C.bgYellow)}  ${badge(`GAGAL ${globalGagal}`,C.bgRed)}  ${S.dot} ${col(fmtTime(totalElapsed),C.cyan)}`);
    console.log(`${col("Rincian per-file:",C.bold)}`);
    console.table(sources.map((s,i)=>({ "#": col(String(i+1),C.dim), file: col(s.file,C.cyan), rows: col(String(s.rows.length),C.white), sizeKB: s.sizeKB })));
  } finally {
    console.log(`\n${S.info} ${col("Menutup browser dalam 3 detik...", C.dim)}`);
    await new Promise(r=>setTimeout(r,3000));
    await driver.quit();
    console.log(`${S.ok} ${col("Browser closed", C.gray)}`);
  }
}

// ---------------------------------------------------------------------------
// CLI
// ---------------------------------------------------------------------------
function parseArgs() {
  const args = process.argv.slice(2);
  const opts = { preview:false, limit:5, dryRun:false, headless:false, excel:null, noLogOnSkip:false };
  for (let i=0;i<args.length;i++) {
    const a=args[i];
    if (a==="--preview") opts.preview=true;
    else if (a==="--dry-run") opts.dryRun=true;
    else if (a==="--headless") opts.headless=true;
    else if (a==="--limit" && args[i+1]) { opts.limit=parseInt(args[++i],10)||5; }
    else if (a==="--excel" && args[i+1]) { opts.excel=args[++i]; }
    else if (a==="--help"||a==="-h") { opts.help=true; }
    else if (!a.startsWith("--") && a.toLowerCase().endsWith(".xlsx")) { opts.excel=a; }
  }
  return opts;
}

async function main() {
  const opts = parseArgs();
  if (opts.help) {
    console.log(`
Usage:
  node addgereja.js [options]

Options:
  --preview              Hanya tampilkan isi Excel (tidak buka browser)
  --limit N              Jumlah baris preview (default 5)
  --dry-run              Cek logika skip tanpa buka browser
  --headless             Jalankan Chrome headless
  --excel <path>         Path Excel custom (default: PYTHON/data_matang/Gereja-Katolik.xlsx)
  --help                 Tampilkan bantuan

Env:
  BASE_URL, DB_HOST, DB_USER, DB_PASS, DB_NAME, GEREJA_EMAIL, GEREJA_PASSWORD
`);
    return;
  }
  if (opts.preview) {
    // preview per-file, tampil jelas file mana sedang di-preview
    const p = opts.excel ? resolveExcelPath(opts.excel) : null;
    previewExcel(p, opts.limit);
    return;
  }
  await runAutomation(opts);
}

if (require.main === module) {
  main().catch(e=>{
    console.error("[FATAL]", e);
    process.exit(1);
  });
}

module.exports = { extractWilayah, getPlatform, namaGerejaInLog, writeLog, PROVINSI_LIST };
