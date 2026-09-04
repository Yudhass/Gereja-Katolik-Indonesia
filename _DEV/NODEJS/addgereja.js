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
  "DKI JAKARTA","JAWA BARAT","JAWA TENGAH","DAERAH ISTIMEWA YOGYAKARTA",
  "JAWA TIMUR","BANTEN","BALI","NUSA TENGGARA BARAT","NUSA TENGGARA TIMUR",
  "KALIMANTAN BARAT","KALIMANTAN TENGAH","KALIMANTAN SELATAN","KALIMANTAN TIMUR","KALIMANTAN UTARA",
  "SULAWESI UTARA","SULAWESI TENGAH","SULAWESI SELATAN","SULAWESI TENGGARA","GORONTALO","SULAWESI BARAT",
  "MALUKU","MALUKU UTARA","PAPUA","PAPUA BARAT","PAPUA SELATAN","PAPUA TENGAH","PAPUA PEGUNUNGAN",
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

function extractWilayah(alamat) {
  const result = { provinsi: "", kabupaten: "", kecamatan: "", kelurahan: "" };
  if (!alamat) return result;
  let parts = alamat.split(",").map(p => p.trim()).filter(Boolean);

  for (let idx = parts.length - 1; idx >= 0; idx--) {
    const part = parts[idx];
    for (const p of PROVINSI_LIST) {
      if (part.toUpperCase().includes(p)) {
        result.provinsi = p;
        parts.splice(idx, 1);
        break;
      }
    }
    if (result.provinsi) break;
  }

  for (let idx = parts.length - 1; idx >= 0; idx--) {
    const part = parts[idx];
    const m = part.match(/^(?:Kota|Kabupat[ae]n|Kab\.)\s+(.+)/i);
    if (m) { result.kabupaten = m[1].trim(); parts.splice(idx, 1); break; }
  }
  if (!result.kabupaten && parts.length) result.kabupaten = parts.pop();

  for (let idx = parts.length - 1; idx >= 0; idx--) {
    const part = parts[idx];
    const m = part.match(/^(?:Kecamat[ae]n|Kec\.)\s+(.+)/i);
    if (m) { result.kecamatan = m[1].trim(); parts.splice(idx, 1); break; }
  }
  if (parts.length) {
    let last = parts[parts.length - 1];
    last = last.replace(/^(?:Kelurahan|Kel\.|Desa|Dusun)\s+/i, "").trim();
    if (last && last.length < 60) result.kelurahan = last;
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

async function namaGerejaExists(nama) {
  let conn;
  try {
    conn = await mysql.createConnection(DB_CONFIG);
    const [rows] = await conn.execute("SELECT COUNT(*) as cnt FROM gereja WHERE nama_gereja = ?", [String(nama).trim()]);
    return rows[0].cnt > 0;
  } catch (e) {
    console.log(`  DB Error: ${e.message}`);
    return false;
  } finally { if (conn) await conn.end(); }
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
  // jika excelPath null -> pakai semua sources agar jelas file mana
  const sources = excelPath ? [loadExcelRows(excelPath)].map(r=>({ file: path.basename(r.path), path: r.path, header: r.header, rows: r.rows, sizeKB: "?" })) : loadExcelSources(null);
  // fallback: kalau loadExcelRows single, pakai loadExcelSources untuk konsistensi file indicator
  const actualSources = excelPath ? loadExcelSources(excelPath) : sources;

  console.log("\n╔════════════════════════════════════════════════════════════╗");
  console.log("║  ADDGEREJA - Preview Excel (mode --preview)                ║");
  console.log("╚════════════════════════════════════════════════════════════╝");
  console.log(`\n[INFO] Ditemukan ${actualSources.length} file .xlsx`);
  let grandTotal = 0;
  for (let sIdx=0; sIdx<actualSources.length; sIdx++) {
    const s = actualSources[sIdx];
    grandTotal += s.rows.length;
    console.log(`\n┌─ [FILE ${sIdx+1}/${actualSources.length}] ${s.file} (${s.sizeKB} KB) | Sheet: ${s.sheetName} | ${s.rows.length} rows | ${s.header.length} cols`);
    console.log(`│  Path  : ${s.path}`);
    console.log(`│  Header: ${s.header.join(" | ")}`);
    const preview = s.rows.slice(0, limit).map(r=>{
      const obj={};
      s.header.forEach((h,i)=> obj[h]=r[i]);
      return obj;
    });
    console.log(`│  Preview ${Math.min(limit, s.rows.length)} baris pertama:`);
    console.table(preview);
    if (s.rows.length > limit) console.log(`│  ... ${s.rows.length-limit} baris lagi tidak ditampilkan (total ${s.rows.length})`);
    console.log(`│  Kolom penting: name=1, website=7, phone=8, address=18, link=20`);
    preview.forEach((obj,i)=>{
      const r = s.rows[i];
      console.log(`│   #${i+1} name=${r[1]} | alamat=${String(r[18]||"").slice(0,50)} | phone=${r[8]}`);
    });
    console.log(`└${"─".repeat(70)}`);
  }
  console.log(`\n[SUMMARY] ${actualSources.length} file | Total rows: ${grandTotal}`);
  if (actualSources.length>1) {
    console.log("\nRingkasan per-file:");
    console.table(actualSources.map((s,i)=>({ "#": i+1, file: s.file, rows: s.rows.length, sizeKB: s.sizeKB })));
  }
}

// ---------------------------------------------------------------------------
// Main automation
// ---------------------------------------------------------------------------
async function runAutomation(opts) {
  const sources = loadExcelSources(opts.excel);
  const totalRows = sources.reduce((a,s)=>a+s.rows.length, 0);
  const header = sources[0]?.header || [];
  console.log(`\n[INFO] Ditemukan ${sources.length} file Excel`);
  sources.forEach((s,i)=> console.log(`  [${i+1}/${sources.length}] ${s.file} (${s.sizeKB} KB) - ${s.rows.length} rows - ${s.path}`));
  console.log(`[INFO] BASE_URL: ${BASE_URL}`);
  console.log(`[INFO] LOG_PATH: ${LOG_PATH}`);
  console.log(`[INFO] DB: ${DB_CONFIG.host}/${DB_CONFIG.database}`);
  console.log(`[INFO] Header: ${header.slice(0,6).join(", ")} ... (${header.length} cols)`);
  console.log(`[INFO] Total rows: ${totalRows} (gabungan ${sources.length} file)`);

  if (opts.dryRun) {
    console.log("\n[DRY-RUN] Tidak membuka browser, hanya cek skip logic.\n");
    console.log("[DRY-RUN] Rule: skip HANYA jika ada di LOG && ada di DB. Jika salah satu tidak ada -> AKAN PROSES (log tidak ditulis ulang jika sudah ada di log).\n");
    let skipBoth=0, skipEmpty=0, willProcess=0, willProcessLogExists=0, willProcessDbExists=0;
    let globalIdx=0;
    for (let sIdx=0; sIdx<sources.length; sIdx++) {
      const src = sources[sIdx];
      console.log(`\n[FILE ${sIdx+1}/${sources.length}] ${src.file} - ${src.rows.length} rows`);
      for (let idx=0; idx<src.rows.length; idx++) {
        globalIdx++;
        const row = src.rows[idx];
        const nameVal = row[1];
        const tag = `[FILE ${sIdx+1}/${sources.length} | ${src.file} | ${idx+1}/${src.rows.length} | global ${globalIdx}/${totalRows}]`;
        if (!nameVal) { skipEmpty++; console.log(`  ${tag} [SKIP EMPTY]`); continue; }
        const inLog = namaGerejaInLog(nameVal);
        const inDb = await namaGerejaExists(String(nameVal));
        if (inLog && inDb) { skipBoth++; console.log(`  ${tag} [SKIP LOG+DB] ${nameVal} (ada di log & DB)`); continue; }
        willProcess++;
        if (inLog && !inDb) willProcessLogExists++;
        if (!inLog && inDb) willProcessDbExists++;
        const detailStatus = inLog ? "[AKAN PROSES - sudah di LOG, belum di DB -> input tapi tidak tulis log ulang]" : (!inDb ? "[AKAN PROSES - belum di LOG & belum di DB]" : "[AKAN PROSES - belum di LOG, sudah di DB -> input & akan tulis log]");
        if (willProcess<=10) {
          console.log(`  ${tag} ${detailStatus} ${nameVal} | ${String(row[18]||"").slice(0,60)} | inLog=${inLog} inDb=${inDb}`);
          console.log(`    wilayah=${JSON.stringify(extractWilayah(String(row[18]||"")))} platform=${getPlatform(row[7])}`);
        } else if (willProcess===11) {
          console.log(`  ... (sisa ${totalRows-globalIdx} baris tidak ditampilkan detail)`);
        }
      }
    }
    console.log(`\n[DRY-RUN SUMMARY] files=${sources.length} totalRows=${totalRows} skipEmpty=${skipEmpty} skipBoth(LOG+DB)=${skipBoth} willProcess=${willProcess} (dari willProcess: sudahDiLog=${willProcessLogExists}, sudahDiDb=${willProcessDbExists})`);
    console.table(sources.map((s,i)=>({ "#": i+1, file: s.file, rows: s.rows.length, sizeKB: s.sizeKB })));
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

    console.log("\n1. Buka halaman login");
    await driver.get(`${BASE_URL}/login`);
    await driver.wait(until.elementLocated(By.name("email")), 10000);
    await driver.findElement(By.name("email")).sendKeys(EMAIL);
    await driver.findElement(By.name("password")).sendKeys(PASSWORD);
    await driver.findElement(By.css("button[type='submit']")).click();
    console.log("2. Login berhasil");
    await driver.sleep(800);

    console.log("3. Buka admin/gereja");
    await driver.get(`${BASE_URL}/admin/gereja`);
    await driver.sleep(800);

    // iter per-file agar jelas sedang proses file mana (mirror Python tapi dengan indikator file)
    let globalIdx = 0;
    let globalBerhasil = 0;
    let globalSkipBoth = 0;
    const startedAt = Date.now();
    for (let sIdx=0; sIdx<sources.length; sIdx++) {
      const src = sources[sIdx];
      console.log(`\n════════════════════════════════════════════════════════════`);
      console.log(`[FILE ${sIdx+1}/${sources.length}] ${src.file} | ${src.rows.length} rows | ${src.path}`);
      console.log(`════════════════════════════════════════════════════════════`);
      for (let idx=0; idx<src.rows.length; idx++) {
        globalIdx++;
        const row = src.rows[idx];
        const nameVal = row[1];
        const addressVal = row.length > 18 ? row[18] : null;
        const linkVal = row.length > 20 ? row[20] : null;
        const phoneVal = row.length > 8 ? row[8] : null;
        const websiteVal = row.length > 7 ? row[7] : null;

        if (!nameVal) continue;
        const progress = `[FILE ${sIdx+1}/${sources.length} ${src.file} | ${idx+1}/${src.rows.length} | GLOBAL ${globalIdx}/${totalRows}]`;
        console.log(`\n${progress} --- Data ke-${globalIdx}: ${nameVal} ---`);

        // Rule baru: skip HANYA jika ada di LOG && ada di DB
        const inLog = namaGerejaInLog(nameVal);
        const inDb = await namaGerejaExists(String(nameVal));
        if (inLog && inDb) {
          console.log(`  ${progress} [!] Sudah ada di LOG & DB, skip. inLog=${inLog} inDb=${inDb}`);
          globalSkipBoth++;
          continue;
        }
        if (inLog && !inDb) {
          console.log(`  ${progress} [i] Ada di LOG tapi BELUM di DB -> tetap input (tidak tulis log ulang). inLog=${inLog} inDb=${inDb}`);
        } else if (!inLog && inDb) {
          console.log(`  ${progress} [i] Belum di LOG tapi sudah di DB -> tetap input & nanti tulis log. inLog=${inLog} inDb=${inDb}`);
        } else {
          console.log(`  ${progress} [i] Belum di LOG & belum di DB -> input baru. inLog=${inLog} inDb=${inDb}`);
        }

      // Klik Tambah Gereja - tunggu modal benar-benar visible (fix ElementNotInteractableError)
      const btnTambah = await driver.wait(until.elementLocated(By.css("button[data-bs-target='#modalAdd']")), 10000);
      await driver.wait(until.elementIsVisible(btnTambah), 5000);
      await driver.executeScript("arguments[0].scrollIntoView({block:'center'});", btnTambah);
      await driver.executeScript("arguments[0].click();", btnTambah);
      console.log("4. Klik Tambah Gereja");
      // tunggu animasi Bootstrap fade -> #modalAdd.show
      try { await driver.wait(until.elementLocated(By.css("#modalAdd.show")), 5000); } catch {}
      await driver.sleep(600);

      const namaInput = await driver.wait(until.elementLocated(By.name("nama_gereja")), 5000);
      await driver.wait(until.elementIsVisible(namaInput), 5000);
      await driver.wait(until.elementIsEnabled(namaInput), 5000);
      await driver.executeScript("arguments[0].scrollIntoView({block:'center'});", namaInput);
      await driver.sleep(200);
      try { await namaInput.clear(); } catch { await driver.executeScript("arguments[0].value=''; arguments[0].dispatchEvent(new Event('input',{bubbles:true}));", namaInput); }
      try { await namaInput.sendKeys(String(nameVal)); } catch { await driver.executeScript("arguments[0].value=arguments[1]; arguments[0].dispatchEvent(new Event('input',{bubbles:true})); arguments[0].dispatchEvent(new Event('change',{bubbles:true}));", namaInput, String(nameVal)); }
      console.log(`5. Nama: ${nameVal}`);

      if (addressVal) {
        const addrStr = String(addressVal);
        const alamatEl = await driver.findElement(By.name("alamat"));
        await alamatEl.clear();
        await alamatEl.sendKeys(addrStr);
        console.log(`   Alamat: ${addrStr.slice(0,50)}...`);

        const wilayah = extractWilayah(addrStr);
        console.log(`   Wilayah: ${JSON.stringify(wilayah)}`);

        if (wilayah.provinsi) {
          const ok = await select2Set(driver, "addProvinsi", wilayah.provinsi);
          if (ok) {
            console.log(`   Provinsi: ${wilayah.provinsi}`);
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
                  console.log(`   Kabupaten: ${wilayah.kabupaten}`);
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
                        console.log(`   Kecamatan: ${wilayah.kecamatan}`);
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
                            if (ok4) console.log(`   Kelurahan: ${wilayah.kelurahan}`);
                            await driver.sleep(300);
                          } catch { console.log(`   Kelurahan tidak terpilih: ${wilayah.kelurahan}`); }
                        }
                      }
                    } catch { console.log(`   Kecamatan tidak terpilih: ${wilayah.kecamatan}`); }
                  }
                }
              } catch { console.log(`   Kabupaten tidak terpilih: ${wilayah.kabupaten}`); }
            }
          } else {
            console.log(`   Provinsi tidak ditemukan di dropdown: ${wilayah.provinsi}`);
          }
        }
      }

      if (phoneVal) {
        const tel = await driver.findElement(By.name("kontak_telepon"));
        await tel.clear();
        await tel.sendKeys(String(phoneVal));
        console.log(`   Telepon: ${phoneVal}`);
      }

      if (linkVal) {
        const originalHandle = await driver.getWindowHandle();
        await driver.executeScript("window.open(arguments[0]);", String(linkVal));
        const handles = await driver.getAllWindowHandles();
        const newHandle = handles[handles.length-1];
        await driver.switchTo().window(newHandle);
        console.log("6. Buka link maps di tab baru...");
        await driver.sleep(800);
        try {
          const searchInput = await driver.wait(until.elementLocated(By.css("input[name='q']")), 10000);
          await searchInput.click();
          await driver.sleep(150);
          await searchInput.sendKeys(Key.ENTER);
          console.log("   Klik input search lalu Enter");
          await driver.sleep(1200);
        } catch {
          console.log("   Input search tidak ditemukan, tetap pakai URL saat ini");
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
        console.log(`   Link Maps: ${currentUrl.slice(0,80)}...`);
      }

      if (websiteVal) {
        const platform = getPlatform(String(websiteVal));
        const sosmedSelect = await driver.findElement(By.css("#addSosmedList select[name='sosmed_platform[]']"));
        // selenium Select helper
        const sel = new Select(sosmedSelect);
        try { await sel.selectByValue(platform); } catch { await sel.selectByVisibleText(platform); }
        const sosmedUrl = await driver.findElement(By.css("#addSosmedList input[name='sosmed_url[]']"));
        await sosmedUrl.clear();
        await sosmedUrl.sendKeys(String(websiteVal));
        console.log(`   Sosmed: ${platform} -> ${websiteVal}`);
      }

      const simpanBtn = await driver.wait(until.elementLocated(By.css("#modalAdd button[type='submit']")), 5000);
      await driver.wait(until.elementIsVisible(simpanBtn), 5000);
      await driver.wait(until.elementIsEnabled(simpanBtn), 5000);
      await driver.executeScript("arguments[0].scrollIntoView({block:'center'});", simpanBtn);
      await driver.executeScript("arguments[0].click();", simpanBtn);
      console.log(`7. Simpan diklik ${progress}`);
      await driver.sleep(900);
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
      // Jangan tulis log ulang jika sudah ada di log
      if (!inLog) {
        writeLog(nameVal, "BERHASIL DITAMBAHKAN");
      } else {
        console.log(`   [LOG] Sudah ada di log, tidak tulis ulang: ${nameVal}`);
      }
      globalBerhasil++;
      const elapsed = ((Date.now()-startedAt)/1000).toFixed(1);
      console.log(`   [PROGRESS] ${progress} => BERHASIL | total berhasil: ${globalBerhasil} | elapsed: ${elapsed}s`);
      } // end idx loop
      console.log(`\n[FILE SELESAI] ${src.file} (${sIdx+1}/${sources.length}) - berhasil di file ini: ${globalBerhasil} (kumulatif)`);
    } // end sources loop

    console.log(`\n[Selesai semua data] files=${sources.length} totalRows=${totalRows} berhasil=${globalBerhasil} skipBoth(LOG+DB)=${globalSkipBoth} elapsed=${((Date.now()-startedAt)/1000).toFixed(1)}s`);
    console.table(sources.map((s,i)=>({ "#": i+1, file: s.file, rows: s.rows.length, sizeKB: s.sizeKB })) );
  } finally {
    console.log("\n[INFO] Menutup browser dalam 3 detik...");
    await new Promise(r=>setTimeout(r,3000));
    await driver.quit();
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
