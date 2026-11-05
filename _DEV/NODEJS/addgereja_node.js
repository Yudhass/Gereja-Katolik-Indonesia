/**
 * addgereja_node.js - Backend-direct (tanpa Chrome/Selenium) port.
 *
 * Fitur:
 *  - baca Excel sama seperti addgereja.js (data_mateng/*.xlsx, fallback data_matang)
 *  - cek log.txt & DB (nama_gereja) -> skip HANYA jika ada di LOG && DB
 *  - login langsung ke backend via HTTP (POST /login) dengan cookie session
 *  - tambah data via POST /admin/gereja/add (jalur belakang, tanpa Chrome)
 *  - extract wilayah dari alamat; komponen yang tidak ditemukan dikosongkan ("")
 *  - tulis log jika belum ada di log
 *
 * Usage:
 *   node addgereja_node.js                 # jalankan automation
 *   node addgereja_node.js --dry-run        # cek skip logic tanpa HTTP add
 *   node addgereja_node.js --preview        # preview Excel
 *   node addgereja_node.js --excel "path"   # custom excel
 */

const fs = require("fs");
const path = require("path");
const XLSX = require("xlsx");
const mysql = require("mysql2/promise");

// ---------------------------------------------------------------------------
// Konfigurasi
// ---------------------------------------------------------------------------
const BASE_URL = process.env.BASE_URL || "http://192.168.1.240/Gereja-Katolik-Indonesia";
const EMAIL = process.env.GEREJA_EMAIL || "admin.gereja.katolik.indonesia@gmail.com";
const PASSWORD = process.env.GEREJA_PASSWORD || "Admin123_@";

const LOG_PATH = path.join(__dirname, "log.txt");

const DB_CONFIG = {
  host: process.env.DB_HOST || "localhost",
  user: process.env.DB_USER || "root",
  password: process.env.DB_PASS || "",
  database: process.env.DB_NAME || "db_gereja",
  charset: "utf8",
};

// ---------------------------------------------------------------------------
// Terminal Styling
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

// ---------------------------------------------------------------------------
// Helpers (mirror Python / addgereja.js)
// ---------------------------------------------------------------------------
const PROVINSI_LIST = [
  "ACEH","SUMATERA UTARA","SUMATERA BARAT","RIAU","JAMBI","SUMATERA SELATAN",
  "BENGKULU","LAMPUNG","KEPULAUAN BANGKA BELITUNG","KEPULAUAN RIAU",
  "DAERAH KHUSUS IBUKOTA JAKARTA","DKI JAKARTA","JAWA BARAT","JAWA TENGAH","DAERAH ISTIMEWA YOGYAKARTA",
  "JAWA TIMUR","BANTEN","BALI","NUSA TENGGARA BARAT","NUSA TENGGARA TIMUR",
  "KALIMANTAN BARAT","KALIMANTAN TENGAH","KALIMANTAN SELATAN","KALIMANTAN TIMUR","KALIMANTAN UTARA",
  "SULAWESI UTARA","SULAWESI TENGAH","SULAWESI SELATAN","SULAWESI TENGGARA","GORONTALO","SULAWESI BARAT",
  "MALUKU","MALUKU UTARA","PAPUA","PAPUA BARAT","PAPUA BARAT DAYA","PAPUA SELATAN","PAPUA TENGAH","PAPUA PEGUNUNGAN",
];

function sanitizeString(input) {
  let s = String(input == null ? "" : input).trim();
  s = s.replace(/<[^>]*>/g, "");
  s = s.replace(/&/g, "&amp;")
       .replace(/</g, "&lt;")
       .replace(/>/g, "&gt;")
       .replace(/"/g, "&quot;")
       .replace(/'/g, "&#039;");
  return s.trim();
}

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
  const nama = sanitizeString(namaGereja).trim().toLowerCase();
  try {
    const content = fs.readFileSync(LOG_PATH, "utf-8");
    for (const line of content.split("\n")) {
      const parts = line.trim().split(" | ");
      if (parts.length >= 2) {
        const logged = sanitizeString(parts[1].split(" [")[0]).trim().toLowerCase();
        if (logged === nama) return true;
      }
    }
  } catch (e) { console.log(`  Error membaca log: ${e.message}`); }
  return false;
}

// (copy extractWilayah helpers from addgereja.js)
const EN_PROVINCE_MAP = {
  "aceh": "ACEH",
  "north sumatra": "SUMATERA UTARA", "north sumatera": "SUMATERA UTARA",
  "west sumatra": "SUMATERA BARAT", "west sumatera": "SUMATERA BARAT",
  "riau": "RIAU", "jambi": "JAMBI",
  "south sumatra": "SUMATERA SELATAN", "south sumatera": "SUMATERA SELATAN",
  "bengkulu": "BENGKULU", "lampung": "LAMPUNG",
  "bangka belitung": "KEPULAUAN BANGKA BELITUNG",
  "bangka belitung islands": "KEPULAUAN BANGKA BELITUNG",
  "kepulauan bangka belitung": "KEPULAUAN BANGKA BELITUNG",
  "riau islands": "KEPULAUAN RIAU", "kepulauan riau": "KEPULAUAN RIAU",
  "jakarta": "DAERAH KHUSUS IBUKOTA JAKARTA", "dki jakarta": "DAERAH KHUSUS IBUKOTA JAKARTA",
  "special capital region of jakarta": "DAERAH KHUSUS IBUKOTA JAKARTA",
  "daerah khusus ibukota jakarta": "DAERAH KHUSUS IBUKOTA JAKARTA",
  "west java": "JAWA BARAT", "central java": "JAWA TENGAH", "east java": "JAWA TIMUR",
  "yogyakarta": "DAERAH ISTIMEWA YOGYAKARTA",
  "special region of yogyakarta": "DAERAH ISTIMEWA YOGYAKARTA",
  "daerah istimewa yogyakarta": "DAERAH ISTIMEWA YOGYAKARTA",
  "banten": "BANTEN", "bali": "BALI",
  "west nusa tenggara": "NUSA TENGGARA BARAT", "east nusa tenggara": "NUSA TENGGARA TIMUR",
  "west kalimantan": "KALIMANTAN BARAT", "central kalimantan": "KALIMANTAN TENGAH",
  "south kalimantan": "KALIMANTAN SELATAN", "east kalimantan": "KALIMANTAN TIMUR",
  "north kalimantan": "KALIMANTAN UTARA",
  "north sulawesi": "SULAWESI UTARA", "central sulawesi": "SULAWESI TENGAH",
  "south sulawesi": "SULAWESI SELATAN", "southeast sulawesi": "SULAWESI TENGGARA",
  "south east sulawesi": "SULAWESI TENGGARA", "gorontalo": "GORONTALO",
  "west sulawesi": "SULAWESI BARAT", "maluku": "MALUKU", "north maluku": "MALUKU UTARA",
  "north moluccas": "MALUKU UTARA", "papua": "PAPUA", "west papua": "PAPUA BARAT",
  "southwest papua": "PAPUA BARAT DAYA", "south west papua": "PAPUA BARAT DAYA",
  "south papua": "PAPUA SELATAN", "central papua": "PAPUA TENGAH",
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

function translateEnglishGeo(name) {
  if (!name) return "";
  const s = String(name).trim().replace(/\s+/g, " ");
  if (!s) return "";
  let low = s.toLowerCase();
  low = low.replace(/\s+(province|regency|city|district|sub-district|subdistrict|village)\s*$/, "").trim();
  let isIslands = false;
  if (/\bislands\s*$/.test(low)) { isIslands = true; low = low.replace(/\s*islands\s*$/, "").trim(); }
  const dirKeys = Object.keys(_EN_DIR_MAP).sort((a, b) => b.length - a.length);
  let dirID = "";
  for (const k of dirKeys) {
    if (low === k) { dirID = _EN_DIR_MAP[k]; low = ""; break; }
    if (low.startsWith(k + " ")) { dirID = _EN_DIR_MAP[k]; low = low.slice(k.length).trim(); break; }
  }
  low = low.replace(/^of\s+/, "").trim();
  low = low.replace(/\bsumatra\b/g, "sumatera");
  let titled = low.split(/\s+/).filter(Boolean).map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(" ");
  if (isIslands) titled = (titled ? "Kepulauan " + titled : "Kepulauan").trim();
  if (dirID) titled = (titled ? titled + " " + dirID : dirID).trim();
  return titled;
}
function _cleanAddrPart(p) { return String(p || "").trim().replace(/\s+\d{5}\s*$/, "").trim(); }
function _stripPlusCode(p) {
  const s = String(p || "").trim();
  const m = s.match(/^[A-Z0-9]{4}\+\S*\s+(.+)$/i);
  if (m) return m[1].trim();
  if (/^[A-Z0-9]{4}\+\S*\s*$/i.test(s)) return "";
  return s;
}
function _isNoisePart(p) {
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
  return /^(Jl\b\.?|Jalan\b|Gang\b|RT\b\.?|RW\b\.?|No\b\.?|Unnamed\b|Blok\b|Km\b|Gedung\b|Kompleks\b|Komplek\b|Perumnas\b|Perumahan\b|Kapling\b|Kav\b\.?|Lantai\b|Lt\b\.?|St\b|Gereja\b|Pastoran\b|Paroki\b|Musala\b|Masjid\b|Pulau\s+Sumatera\s*$)/i.test(String(c || "").trim());
}
function _expandAbbrev(low) {
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
  const low = cleaned.toLowerCase().replace(/\s+/g, " ").replace(/\s+province\s*$/, "").trim();
  if (EN_PROVINCE_MAP[low]) return EN_PROVINCE_MAP[low];
  const exp = _expandAbbrev(low);
  if (EN_PROVINCE_MAP[exp]) return EN_PROVINCE_MAP[exp];
  const up = cleaned.toUpperCase();
  const upExp = exp.toUpperCase();
  const sorted = [...PROVINSI_LIST].sort((a, b) => b.length - a.length);
  for (const p of sorted) { if (up.includes(p) || upExp.includes(p)) return p; }
  if (/(^|[\s,])JAKARTA([\s,]|$)/.test(up)) return "DAERAH KHUSUS IBUKOTA JAKARTA";
  const tr = translateEnglishGeo(cleaned);
  if (tr) {
    const tru = tr.toUpperCase();
    for (const p of sorted) { if (tru === p || tru.includes(p) || p.includes(tru)) return p; }
    if (tru === "JAKARTA") return "DAERAH KHUSUS IBUKOTA JAKARTA";
  }
  return "";
}
function _matchKabupaten(partRaw) {
  const cleaned = _cleanAddrPart(_stripPlusCode(partRaw));
  if (!cleaned || _isNoisePart(partRaw)) return "";
  let m;
  m = cleaned.match(/^(?:Kota|Kabupat[ae]n|Kab\.)\s+(.+)/i);
  if (m && m[1].trim()) return m[1].trim();
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
  for (let idx = parts.length - 1; idx >= 0; idx--) {
    const found = _matchProvinsi(parts[idx]);
    if (found) { result.provinsi = found; parts.splice(idx, 1); break; }
  }
  if (result.provinsi === "DKI JAKARTA") result.provinsi = "DAERAH KHUSUS IBUKOTA JAKARTA";
  for (let idx = parts.length - 1; idx >= 0; idx--) {
    const name = _matchKabupaten(parts[idx]);
    if (name) { result.kabupaten = name; parts.splice(idx, 1); break; }
  }
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
  for (let idx = parts.length - 1; idx >= 0; idx--) {
    if (_isNoisePart(parts[idx])) continue;
    const c = _cleanAddrPart(_stripPlusCode(parts[idx]));
    if (!c) continue;
    let m = c.match(/^(?:Kecamat[ae]n|Kec\.|Distrik)\s+(.+)/i);
    if (m && m[1].trim()) { result.kecamatan = translateEnglishGeo(m[1].trim()); parts.splice(idx, 1); break; }
    m = c.match(/^(.*?)\s+(?:District|Sub-?district)$/i);
    if (m && m[1].trim()) { result.kecamatan = translateEnglishGeo(m[1].trim()); parts.splice(idx, 1); break; }
  }
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
  for (let pass = 0; pass < 2 && !result.kelurahan; pass++) {
    const relaxed = pass === 1;
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

// ---------------------------------------------------------------------------
// DB cache
// ---------------------------------------------------------------------------
async function loadAllNamaGerejaFromDB() {
  let conn;
  try {
    conn = await mysql.createConnection(DB_CONFIG);
    const [rows] = await conn.execute("SELECT nama_gereja FROM gereja");
    const set = new Set();
    for (const r of rows) set.add(sanitizeString(r.nama_gereja).toLowerCase());
    console.log(`[CACHE] Loaded ${set.size} nama_gereja dari DB`);
    return set;
  } catch (e) {
    console.log(`  [CACHE DB] Error: ${e.message}`);
    return new Set();
  } finally { if (conn) await conn.end(); }
}
function namaGerejaExistsCached(cacheSet, nama) {
  return cacheSet.has(sanitizeString(nama).toLowerCase());
}

// ---------------------------------------------------------------------------
// Excel loading (copy)
// ---------------------------------------------------------------------------
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
    const dataMentahDirs = [path.join(__dirname, "data_mateng"), path.join(__dirname, "..", "data_mateng")];
    for (const d of dataMentahDirs) {
      if (fs.existsSync(d)) {
        const matched = fs.readdirSync(d).filter(f=>f.toLowerCase().includes(custom.toLowerCase()) && f.toLowerCase().endsWith(".xlsx")).sort().map(f=>path.join(d,f));
        if (matched.length) return matched;
      }
    }
  }
  const primaryDirs = [path.join(__dirname, "data_mateng"), path.join(__dirname, "..", "data_mateng")];
  for (const d of primaryDirs) {
    if (fs.existsSync(d)) {
      const files = fs.readdirSync(d).filter(f=>f.toLowerCase().endsWith(".xlsx")).sort().map(f=>path.join(d,f));
      if (files.length) return files;
    }
  }
  const fallbackFiles = [
    path.join(__dirname, "..", "PYTHON", "data_matang", "Gereja-Katolik.xlsx"),
    path.join(__dirname, "data_matang", "Gereja-Katolik.xlsx"),
    path.join(__dirname, "..", "data_matang", "Gereja-Katolik.xlsx"),
  ];
  for (const p of fallbackFiles) if (fs.existsSync(p)) return [p];
  const lastDir = path.join(__dirname, "..", "PYTHON", "data_matang");
  if (fs.existsSync(lastDir)) {
    const files = fs.readdirSync(lastDir).filter(f=>f.toLowerCase().endsWith(".xlsx")).sort().map(f=>path.join(lastDir,f));
    if (files.length) return files;
  }
  return [];
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
  const actualSources = excelPath ? loadExcelSources(excelPath) : loadExcelSources(null);
  console.log(boxTitle("ADDGEREJA_NODE — Preview Excel"));
  console.log(`\n${S.info} ${col(`Ditemukan ${actualSources.length} file .xlsx`, C.bold)}  limit ${limit}/file`);
  let grandTotal = 0;
  for (let sIdx=0; sIdx<actualSources.length; sIdx++) {
    const s = actualSources[sIdx];
    grandTotal += s.rows.length;
    console.log(`\n${col("┌─",C.cyan)} ${badge(`FILE ${sIdx+1}/${actualSources.length}`, C.bgBlue)} ${col(s.file, C.bold+C.white)} ${col(`(${s.sizeKB} KB)`,C.dim)} ${col(`${s.rows.length} rows`, C.yellow)}`);
    const preview = s.rows.slice(0, limit).map(r=>{ const obj={}; s.header.forEach((h,i)=> obj[h]=r[i]); return obj; });
    console.table(preview);
  }
  console.log(`\n${col("▶ SUMMARY",C.bold+C.cyan)}  ${col(`${actualSources.length} file`,C.bold)} ${col(`Total rows: ${grandTotal}`, C.bold+C.green)}`);
}

// ---------------------------------------------------------------------------
// HTTP helpers (backend langsung, tanpa Chrome)
// ---------------------------------------------------------------------------
const cookieJar = {};
function applySetCookie(res) {
  try {
    if (typeof res.headers.getSetCookie === 'function') {
      for (const c of res.headers.getSetCookie()) {
        const m = c.match(/^\s*([^=]+)=([^;]*)/);
        if (m) cookieJar[m[1].trim()] = m[2].trim();
      }
    } else {
      const sc = res.headers.get('set-cookie');
      if (sc) {
        for (const part of String(sc).split(/,(?=[^;]+;)/)) {
          const m = part.match(/^\s*([^=]+)=([^;]*)/);
          if (m) cookieJar[m[1].trim()] = m[2].trim();
        }
      }
    }
  } catch (_) {}
}
function cookieHeader() {
  return Object.entries(cookieJar).map(([k,v])=>`${k}=${v}`).join('; ');
}
function extractCsrf(html) {
  let m = html.match(/name=["']csrf_token["']\s+value=["']([^"']+)["']/i);
  if (!m) m = html.match(/value=["']([^"']+)["']\s+name=["']csrf_token["']/i);
  if (!m) m = html.match(/csrf_token[^"']*["']\s*value=["']([^"']+)["']/i);
  return m ? m[1] : '';
}

async function httpLogin() {
  // GET /login untuk session cookie + CSRF
  const r1 = await fetch(BASE_URL + '/login', { redirect: 'manual' });
  applySetCookie(r1);
  const html1 = await r1.text();
  const csrf1 = extractCsrf(html1);
  // POST /login
  const body = new URLSearchParams();
  body.append('email', EMAIL);
  body.append('password', PASSWORD);
  body.append('csrf_token', csrf1);
  const r2 = await fetch(BASE_URL + '/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Cookie': cookieHeader() },
    body: body.toString(),
    redirect: 'manual'
  });
  applySetCookie(r2);
  const loc = r2.headers.get('location');
  console.log(`  ${S.ok} Login POST -> ${r2.status} ${loc || ''}`);
  // GET admin/gereja untuk memastikan session
  const r3 = await fetch(BASE_URL + '/admin/gereja', { headers: { 'Cookie': cookieHeader() }, redirect: 'follow' });
  applySetCookie(r3);
  const html3 = await r3.text();
  if (r3.status !== 200) {
    throw new Error(`Gagal akses admin/gereja setelah login (HTTP ${r3.status})`);
  }
  console.log(`  ${S.ok} ${col("Login berhasil & session aktif", C.green+C.bold)} ${col(`(${EMAIL})`,C.dim)}`);
  return html3;
}

async function fetchAdminGerejaPage() {
  const r = await fetch(BASE_URL + '/admin/gereja', { headers: { 'Cookie': cookieHeader() }, redirect: 'follow' });
  applySetCookie(r);
  const html = await r.text();
  if (r.status !== 200) throw new Error(`HTTP ${r.status} saat GET admin/gereja`);
  return html;
}

async function addGerejaViaBackend(row) {
  const nameVal = row[1];
  const addressVal = row.length > 18 ? row[18] : null;
  const linkVal = row.length > 20 ? row[20] : null;
  const phoneVal = row.length > 8 ? row[8] : null;
  const websiteVal = row.length > 7 ? row[7] : null;

  // Ambil halaman admin/gereja untuk CSRF token segar
  const pageHtml = await fetchAdminGerejaPage();
  const csrf = extractCsrf(pageHtml);
  if (!csrf) throw new Error("CSRF token tidak ditemukan");

  const wilayah = extractWilayah(addressVal ? String(addressVal) : "");
  // komponen yang tidak ditemukan -> "" (sudah "" dari extractWilayah)

  const params = new URLSearchParams();
  params.append('csrf_token', csrf);
  params.append('nama_gereja', String(nameVal));
  params.append('alamat', addressVal ? String(addressVal) : '');
  params.append('provinsi', wilayah.provinsi || '');
  params.append('kabupaten_kota', wilayah.kabupaten || '');
  params.append('kecamatan', wilayah.kecamatan || '');
  params.append('kelurahan', wilayah.kelurahan || '');
  params.append('link_maps', linkVal ? String(linkVal) : '');
  params.append('latitude', '');
  params.append('longitude', '');
  params.append('kontak_telepon', phoneVal ? String(phoneVal) : '');
  params.append('deskripsi', '');
  if (websiteVal) {
    params.append('sosmed_platform[]', getPlatform(String(websiteVal)));
    params.append('sosmed_url[]', String(websiteVal));
  }

  const res = await fetch(BASE_URL + '/admin/gereja/add', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Cookie': cookieHeader(), 'Referer': BASE_URL + '/admin/gereja' },
    body: params.toString(),
    redirect: 'manual'
  });
  applySetCookie(res);

  let loc = res.headers.get('location');
  if (loc && !/^https?:\/\//i.test(loc)) {
    loc = BASE_URL.replace(/\/$/, '') + (loc.startsWith('/') ? loc : '/' + loc);
  }

  let finalHtml = '';
  if (loc) {
    const r2 = await fetch(loc, { headers: { 'Cookie': cookieHeader() }, redirect: 'follow' });
    applySetCookie(r2);
    finalHtml = await r2.text();
  } else {
    finalHtml = await res.text();
  }

  const lower = (finalHtml || '').toLowerCase();
  const alreadyExists = lower.includes('sudah ada di database');
  const success = !alreadyExists && (lower.includes('alert-success') || lower.includes('berhasil ditambahkan'));
  return { alreadyExists, success, status: res.status, wilayah };
}

// ---------------------------------------------------------------------------
// Main automation (backend langsung)
// ---------------------------------------------------------------------------
async function runAutomation(opts) {
  const sources = loadExcelSources(opts.excel);
  const totalRows = sources.reduce((a,s)=>a+s.rows.length, 0);
  console.log(boxTitle("ADDGEREJA_NODE — Automation (backend langsung)"));
  console.log(`${S.info} ${col(`Ditemukan ${sources.length} file Excel`,C.bold)}  total ${totalRows} rows`);
  console.log(`  ${col("BASE_URL",C.dim)} ${S.arrow} ${col(BASE_URL, C.cyan)}`);
  console.log(`  ${col("LOG_PATH",C.dim)} ${S.arrow} ${col(LOG_PATH, C.gray)}`);

  if (opts.dryRun) {
    const dbCacheSet = await loadAllNamaGerejaFromDB();
    console.log(boxTitle("DRY-RUN — Cek Skip Logic"));
    let skipBoth=0, willProcess=0, globalIdx=0;
    for (let sIdx=0; sIdx<sources.length; sIdx++) {
      const src = sources[sIdx];
      for (let idx=0; idx<src.rows.length; idx++) {
        globalIdx++;
        const row = src.rows[idx];
        const nameVal = row[1];
        if (!nameVal) continue;
        const inLog = namaGerejaInLog(nameVal);
        const inDb = namaGerejaExistsCached(dbCacheSet, String(nameVal));
        if (inDb) { skipBoth++; console.log(`  ${S.skip} SKIP DB ${truncate(nameVal,50)} ${inLog?'(log ada)':'(log ditulis nanti)'}`); continue; }
        willProcess++;
        const w = extractWilayah(String(row.length>18?row[18]||"": ""));
        console.log(`  ${S.arrow} PROSES ${truncate(nameVal,44)} ${S.dot} wilayah=${JSON.stringify(w)}`);
      }
    }
    console.log(boxTitle("DRY-RUN SUMMARY"));
    console.log(`${col("Total",C.dim)} ${totalRows} ${S.dot} ${badge(`SKIP LOG+DB ${skipBoth}`,C.bgGreen)} ${badge(`AKAN PROSES ${willProcess}`,C.bgBlue)}`);
    return;
  }

  // Login backend
  console.log(`\n${col("▶",C.cyan)} ${col("1. Login backend",C.bold)}  ${col(BASE_URL+"/login",C.dim)}`);
  await httpLogin();

  const dbCacheSet = await loadAllNamaGerejaFromDB();

  let globalIdx = 0;
  let globalBerhasil = 0;
  let globalSkipBoth = 0;
  let globalGagal = 0;
  const startedAt = Date.now();

  for (let sIdx=0; sIdx<sources.length; sIdx++) {
    const src = sources[sIdx];
    console.log(`\n${col("┏━",C.cyan)} ${badge(`FILE ${sIdx+1}/${sources.length}`, C.bgBlue)} ${col(src.file, C.bold+C.white)} ${col(`${src.rows.length} rows`,C.yellow)}`);
    for (let idx=0; idx<src.rows.length; idx++) {
      globalIdx++;
      const row = src.rows[idx];
      const nameVal = row[1];
      if (!nameVal) continue;

      const inLog = namaGerejaInLog(nameVal);
      const inDb = namaGerejaExistsCached(dbCacheSet, String(nameVal));
      console.log(`\n${col("─".repeat(72), C.gray)}`);
      console.log(`${col(`[F${sIdx+1}/${sources.length} ${String(idx+1).padStart(2)}/${src.rows.length}  G${globalIdx}/${totalRows}]`, C.dim)} ${bar(globalIdx, totalRows)}`);
      console.log(`${col("▸",C.cyan)} ${col(`Data ke-${globalIdx}`,C.bold)} ${S.arrow} ${col(truncate(nameVal,64), C.bold+C.white)}`);
      const icon = inDb ? col("● SUDAH ADA",C.green+C.bold) : col("○ BELUM ADA",C.yellow);
      console.log(`  ${col("[DB CHECK]",C.cyan)} ${icon} ${S.dot} inLog=${inLog?col("ya",C.green):col("tidak",C.dim)}`);

      if (inDb) {
        // Sudah ada di DB -> tidak perlu input ke server, langsung tulis log jika belum ada
        if (!inLog) {
          writeLog(nameVal, "SUDAH ADA DI DB (SKIP INPUT)");
          console.log(`  ${S.ok} ${badge("LOG DITULIS", C.bgGreen)} ${col(truncate(nameVal,48),C.white)} ${col("sudah di DB → log ditulis",C.green)}`);
        } else {
          console.log(`  ${S.skip} ${col("SKIP", C.bgYellow+C.bold)} ${col("sudah di LOG & DB",C.gray)}`);
        }
        globalSkipBoth++;
        continue;
      }
      if (inLog) {
        console.log(`  ${col("→",C.yellow)} ${badge("LOG ada / DB belum", C.yellow)} ${col("tetap input (tidak tulis log ulang)",C.dim)}`);
      } else {
        console.log(`  ${col("→",C.green)} ${badge("BARU", C.bgGreen)} ${col("belum di LOG & DB → input baru",C.white)}`);
      }

      const addressVal = row.length > 18 ? row[18] : null;
      const wilayah = extractWilayah(addressVal ? String(addressVal) : "");
      console.log(`  ${S.dot} ${col("Wilayah",C.dim)} ${col(JSON.stringify(wilayah), C.cyan)}`);

      try {
        const result = await addGerejaViaBackend(row);
        if (result.alreadyExists) {
          const k = sanitizeString(nameVal).toLowerCase();
          dbCacheSet.add(k);
          if (!inLog) {
            writeLog(nameVal, "SUDAH ADA DI DB (SKIP INPUT)");
            console.log(`  ${S.ok} ${badge("LOG DITULIS", C.bgGreen)} ${col(truncate(nameVal,48),C.white)} ${col("server: sudah ada → log ditulis",C.green)}`);
          } else {
            console.log(`  ${S.skip} ${badge("SKIP SERVER", C.bgYellow)} ${col("ditolak server (sudah ada)",C.yellow)}`);
          }
          globalSkipBoth++;
        } else if (result.success) {
          const k = sanitizeString(nameVal).toLowerCase();
          dbCacheSet.add(k);
          if (!inLog) {
            writeLog(nameVal, "BERHASIL DITAMBAHKAN");
            console.log(`  ${S.ok} ${badge("BERHASIL", C.bgGreen)} ${col(truncate(nameVal,48),C.white)} ${col("log ditulis",C.green)}`);
          } else {
            console.log(`  ${S.ok} ${badge("BERHASIL", C.bgGreen)} ${col(truncate(nameVal,48),C.white)} ${col("sudah di log → tidak tulis ulang",C.dim)}`);
          }
          globalBerhasil++;
        } else {
          globalGagal++;
          console.log(`  ${S.fail} ${badge("GAGAL", C.bgRed)} ${col(truncate(nameVal,48),C.white)} ${col("status="+result.status,C.red)}`);
        }
        const elapsed = ((Date.now()-startedAt)/1000).toFixed(1);
        console.log(`  ${col("▸ Progress",C.bold)} ${bar(globalIdx, totalRows)} ${S.dot} ${col(`berhasil: ${globalBerhasil}`,C.green+C.bold)} ${S.dot} ${col(`gagal: ${globalGagal}`,C.dim)} ${S.dot} ${col(fmtTime(elapsed), C.cyan)}`);
      } catch (e) {
        globalGagal++;
        console.log(`\n  ${col("✘ ERROR", C.bgRed+C.white+C.bold)} ${col(truncate(nameVal,52),C.white)}`);
        console.log(`  ${S.dot} ${col(e.message, C.red)}`);
      }
    }
    const fileElapsed = ((Date.now()-startedAt)/1000).toFixed(1);
    console.log(`\n${col("┌─ FILE SELESAI",C.green)} ${badge(src.file, C.bgBlue)} ${col(`kumulatif berhasil: ${globalBerhasil}`, C.bold+C.green)} ${col(fmtTime(fileElapsed),C.cyan)}`);
  }

  const totalElapsed = ((Date.now()-startedAt)/1000).toFixed(1);
  console.log(boxTitle("SELESAI SEMUA DATA"));
  console.log(`${S.ok} ${col(`Files: ${sources.length}`,C.bold)}  ${S.dot} ${col(`Total: ${totalRows}`,C.white)}  ${S.dot} ${badge(`BERHASIL ${globalBerhasil}`,C.bgGreen)}  ${badge(`SKIP ${globalSkipBoth}`,C.bgYellow)}  ${badge(`GAGAL ${globalGagal}`,C.bgRed)}  ${S.dot} ${col(fmtTime(totalElapsed),C.cyan)}`);
}

// ---------------------------------------------------------------------------
// CLI
// ---------------------------------------------------------------------------
function parseArgs() {
  const args = process.argv.slice(2);
  const opts = { preview:false, limit:5, dryRun:false, excel:null };
  for (let i=0;i<args.length;i++) {
    const a=args[i];
    if (a==="--preview") opts.preview=true;
    else if (a==="--dry-run") opts.dryRun=true;
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
  node addgereja_node.js [options]

Options:
  --preview              Hanya tampilkan isi Excel
  --limit N              Jumlah baris preview (default 5)
  --dry-run              Cek logika skip tanpa tambah data
  --excel <path>         Path Excel custom
  --help                 Tampilkan bantuan
`);
    return;
  }
  if (opts.preview) {
    previewExcel(opts.excel ? opts.excel : null, opts.limit);
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

module.exports = { extractWilayah, getPlatform, namaGerejaInLog, writeLog, sanitizeString };