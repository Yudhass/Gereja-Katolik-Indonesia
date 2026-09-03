/**
 * filterdata.js - Baca Excel di data_mentah, filter by kata_kunci, simpan ke data_mateng
 * Tampilan terminal dibuat ringkas & mudah dibaca + progress jelas.
 *
 * Usage:
 *   node filterdata.js                  # default limit 2, simpan
 *   node filterdata.js --limit 5        # 5 nama per kategori per file
 *   node filterdata.js --limit 0        # tanpa sample nama (hanya ringkasan)
 *   node filterdata.js --file ACEH      # hanya file ACEH
 *   node filterdata.js --no-save        # tanpa simpan
 *   node filterdata.js --verbose        # tampil semua nama
 */

const fs = require("fs");
const path = require("path");
const XLSX = require("xlsx");

const kata_kunci = [
  "gereja katolik", "catholic", "keuskupan", "paroki", "pastoran",
  "biara", "katolik", "kongregasi", "suster", "retret", "katholik",
  "stasi", "gua maria", "kapel", "susteran", "toko rohani", "scj",
  "seminari", "st.", "santo", "santa", "taman doa", "katedral",
  "wisma", "superiorat",
];

const DATA_DIR = path.join(__dirname, "data_mentah");
const OUTPUT_DIR = path.join(__dirname, "data_mateng");

const C = {
  reset: "\x1b[0m", bold: "\x1b[1m", dim: "\x1b[2m",
  green: "\x1b[32m", yellow: "\x1b[33m", cyan: "\x1b[36m", red: "\x1b[31m", gray: "\x1b[90m",
};
const clr = (c, s) => `${c}${s}${C.reset}`;

function parseArgs() {
  const a = process.argv.slice(2);
  const o = { limit: 2, file: null, filter: null, noSave: false, verbose: false, help: false };
  for (let i = 0; i < a.length; i++) {
    const v = a[i];
    if (v === "--limit" && a[i + 1] != null) o.limit = parseInt(a[++i], 10);
    else if (v === "--file" && a[i + 1]) o.file = a[++i];
    else if (v === "--filter" && a[i + 1]) o.filter = a[++i];
    else if (v === "--no-save") o.noSave = true;
    else if (v === "--verbose") { o.verbose = true; o.limit = null; }
    else if (v === "--help" || v === "-h") o.help = true;
    else if (!v.startsWith("--") && !o.file) o.file = v;
  }
  if (o.verbose) o.limit = null;
  return o;
}

function getFiles(filterFile) {
  if (!fs.existsSync(DATA_DIR)) { console.error(`Folder tidak ada: ${DATA_DIR}`); process.exit(1); }
  let files = fs.readdirSync(DATA_DIR).filter(f => f.toLowerCase().endsWith(".xlsx")).sort();
  if (filterFile) {
    const q = filterFile.toLowerCase();
    files = files.filter(f => f.toLowerCase().includes(q));
    if (!files.length) { console.error(`Tidak ada file cocok "${filterFile}"`); process.exit(1); }
  }
  return files;
}

function readExcel(filePath) {
  const wb = XLSX.readFile(filePath, { cellDates: false });
  const ws = wb.Sheets[wb.SheetNames[0]];
  const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: null });
  if (!rows.length) return { header: [], names: [], sheet: wb.SheetNames[0], total: 0, rowsAll: rows };
  const header = rows[0].map(h => String(h || "").trim());
  let idx = header.findIndex(h => h.toLowerCase() === "name");
  if (idx === -1) idx = 1;
  const data = rows.slice(1);
  const names = data.map((r, i) => ({ no: i + 1, name: String(r[idx] || "").trim(), raw: r })).filter(x => x.name);
  return { header, names, sheet: wb.SheetNames[0], total: data.length, rowsAll: rows, idx };
}

function ensureOut() { if (!fs.existsSync(OUTPUT_DIR)) fs.mkdirSync(OUTPUT_DIR, { recursive: true }); }
function filterByKW(names, kws) {
  const low = kws.map(k => k.toLowerCase());
  return names.filter(x => low.some(k => x.name.toLowerCase().includes(k)));
}
function save(header, filtered, outPath, sheet) {
  const out = [header, ...filtered.map(x => x.raw)];
  const ws = XLSX.utils.aoa_to_sheet(out);
  ws["!cols"] = header.map((h, i) => {
    let m = String(h).length;
    for (const r of out.slice(1, 6)) m = Math.max(m, String(r[i] || "").length);
    return { wch: Math.min(m + 2, 28) };
  });
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, sheet || "Sheet1");
  XLSX.writeFile(wb, outPath);
}

function main() {
  const opts = parseArgs();
  if (opts.help) {
    console.log(`
 filterdata.js — Filter data_mentah → data_mateng

 Usage:
   node filterdata.js [--limit N] [--file KATA] [--filter KATA] [--no-save] [--verbose]

   --limit N     sample per kategori per file (default 2, 0 = hanya ringkasan)
   --file KATA   hanya file mengandung kata
   --filter KATA override kata_kunci
   --no-save     jangan simpan
   --verbose     tampil semua nama
`);
    return;
  }

  const t0 = Date.now();
  const files = getFiles(opts.file);
  if (!opts.noSave) ensureOut();

  console.log("");
  console.log(clr(C.bold, `FILTERDATA  •  ${files.length} file • limit ${opts.limit ?? "all"} ${opts.noSave ? "(no-save)" : ""}`));
  console.log(clr(C.dim, `  Data   : ${DATA_DIR}`));
  console.log(clr(C.dim, `  Output : ${OUTPUT_DIR}`));
  console.log(clr(C.dim, `  Kata kunci: ${kata_kunci.length} kata`));
  console.log("");

  let grandTotal = 0, grandLolos = 0;
  const summary = [];

  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    const { header, names, sheet, total } = readExcel(path.join(DATA_DIR, file));

    let lolos;
    if (opts.filter) {
      const q = opts.filter.toLowerCase();
      lolos = names.filter(x => x.name.toLowerCase().includes(q));
    } else {
      lolos = filterByKW(names, kata_kunci);
    }
    const ditolak = names.filter(x => !lolos.includes(x));
    const pct = names.length ? ((lolos.length / names.length) * 100).toFixed(1) : "0";

    grandTotal += names.length;
    grandLolos += lolos.length;

    // ---- header per file : 1 baris ringkas ----
    const prog = `${String(i + 1).padStart(2, "0")}/${files.length}`;
    const barLen = 14;
    const filled = Math.round(((i + 1) / files.length) * barLen);
    const barStr = clr(C.green, "█".repeat(filled)) + clr(C.gray, "░".repeat(barLen - filled));
    console.log(clr(C.cyan, "─".repeat(62)));
    console.log(`${clr(C.bold, `[${prog}]`)} ${clr(C.bold, file)}  ${clr(C.dim, `(${total} rows)`)}`);
    console.log(`  ${clr(C.green, `${lolos.length} lolos`)} ${clr(C.dim, `(${pct}%)`)}  ${clr(C.dim, "•")}  ${clr(C.red, `${ditolak.length} ditolak`)}  ${clr(C.dim, `• ${lolos.length} akan disimpan`)}`);
    console.log(`  ${barStr} ${clr(C.dim, `${(((i + 1) / files.length) * 100).toFixed(1)}%  •  ${i + 1}/${files.length} selesai`)}`);

    // ---- sample : lolos tetap pakai limit, ditolak tampil SEMUA ----
    const show = (arr, label, color, forceAll = false) => {
      if (opts.limit === 0 && !forceAll) return;
      const sample = !forceAll && opts.limit ? arr.slice(0, opts.limit) : arr;
      if (!arr.length) {
        console.log(`  ${clr(C.dim, label + ":")} ${clr(C.dim, "(kosong)")}`);
        return;
      }
      const suffix = forceAll ? ` (semua ${arr.length})` : ` (${sample.length}/${arr.length})`;
      console.log(`  ${clr(color, label + suffix + ":")}`);
      for (const it of sample) {
        const key = kata_kunci.find(k => it.name.toLowerCase().includes(k.toLowerCase())) || "";
        const tag = key ? clr(C.dim, ` [${key}]`) : "";
        console.log(`    ${clr(C.dim, String(it.no).padStart(3, " ") + ".")} ${it.name}${tag}`);
      }
      if (!forceAll && arr.length > sample.length) console.log(`    ${clr(C.dim, `... +${arr.length - sample.length} lagi lolos (pakai --verbose untuk semua)`)}`);
    };

    if (opts.limit !== 0) {
      show(lolos, "✓ Lolos", C.green, false);
      show(ditolak, "✗ Ditolak", C.red, true); // tampil SEMUA yang ditolak
    }

    // ---- save ----
    if (!opts.noSave) {
      try {
        save(header, lolos, path.join(OUTPUT_DIR, file), sheet);
        console.log(`  ${clr(C.green, "→ Tersimpan:")} ${clr(C.dim, path.join("data_mateng", file))} ${clr(C.dim, `(${lolos.length} rows)`)}`);
      } catch (e) {
        console.log(`  ${clr(C.red, "→ Gagal simpan:")} ${e.message}`);
      }
    }
    summary.push({
      "#": i + 1,
      file: file.replace(".xlsx", ""),
      rows: total,
      lolos: lolos.length,
      ditolak: ditolak.length,
      "% lolos": pct + "%",
    });
    console.log(""); // spasi antar file
  }

  const grandTolak = grandTotal - grandLolos;
  const elapsed = ((Date.now() - t0) / 1000).toFixed(1);
  console.log(clr(C.cyan, "═".repeat(62)));
  console.log(clr(C.bold, ` SELESAI • ${elapsed}s • ${files.length} file`));
  console.log(`  Total: ${clr(C.bold, grandTotal + " rows")} → ${clr(C.green, grandLolos + " lolos")} (${grandTotal ? ((grandLolos / grandTotal) * 100).toFixed(1) : 0}%) ${clr(C.dim, "•")} ${clr(C.red, grandTolak + " ditolak")} (${grandTotal ? ((grandTolak / grandTotal) * 100).toFixed(1) : 0}%)`);
  if (!opts.noSave) console.log(`  Output: ${clr(C.green, grandLolos + " rows tersimpan")} di ${OUTPUT_DIR}`);
  console.log("");
  console.log(clr(C.bold, " Ringkasan per file:"));
  console.table(summary);
  if (!opts.noSave) console.log(`  ${clr(C.green, "✔")} ${files.length} file tersimpan di ${clr(C.bold, OUTPUT_DIR)}`);
  console.log(clr(C.dim, `  Kata kunci: ${kata_kunci.join(", ")}`));
  console.log("");
}

if (require.main === module) main();
module.exports = { kata_kunci, readExcel };
