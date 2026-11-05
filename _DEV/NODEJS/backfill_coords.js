/**
 * backfill_coords.js - Isi latitude/longitude yang masih 0 dari link Google Maps di Excel.
 * Usage:
 *   node backfill_coords.js            # dry-run (hanya hitung)
 *   node backfill_coords.js --apply    # update DB
 */
const fs = require("fs");
const path = require("path");
const XLSX = require("xlsx");
const mysql = require("mysql2/promise");

const DB_CONFIG = {
  host: process.env.DB_HOST || "localhost",
  user: process.env.DB_USER || "root",
  password: process.env.DB_PASS || "",
  database: process.env.DB_NAME || "db_gereja",
  charset: "utf8",
};

function sanitizeString(input) {
  let s = String(input == null ? "" : input).trim();
  s = s.replace(/<[^>]*>/g, "");
  s = s.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
  return s.trim();
}

function extractCoords(link) {
  const result = { latitude: null, longitude: null };
  if (!link) return result;
  const s = String(link);
  const patterns = [
    /!3d(-?\d+(?:\.\d+)?)!4d(-?\d+(?:\.\d+)?)/,
    /@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/,
    /[?&]q=(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/,
    /ll=(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/,
  ];
  for (const re of patterns) {
    const m = s.match(re);
    if (m) { result.latitude = parseFloat(m[1]); result.longitude = parseFloat(m[2]); return result; }
  }
  return result;
}

function resolveExcelSources() {
  const dir = path.join(__dirname, "data_mateng");
  if (!fs.existsSync(dir)) return [];
  return fs.readdirSync(dir).filter(f => f.toLowerCase().endsWith(".xlsx")).sort().map(f => path.join(dir, f));
}

async function main() {
  const apply = process.argv.includes("--apply");
  const files = resolveExcelSources();
  console.log(`Mode: ${apply ? "APPLY" : "DRY-RUN"} | files: ${files.length}`);

  const conn = await mysql.createConnection(DB_CONFIG);
  let wouldUpdate = 0, updated = 0, skippedNoCoord = 0, skippedNotFound = 0, errors = 0;

  for (const f of files) {
    const wb = XLSX.readFile(f);
    const ws = wb.Sheets[wb.SheetNames[0]];
    const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: null });
    for (let i = 1; i < rows.length; i++) {
      const row = rows[i];
      const name = row && row[1] ? sanitizeString(row[1]) : "";
      if (!name) continue;
      const coords = extractCoords(row.length > 20 ? row[20] : null);
      if (coords.latitude === null) { skippedNoCoord++; continue; }
      // cek row DB
      const [existing] = await conn.execute("SELECT id, latitude, longitude FROM gereja WHERE nama_gereja = ?", [name]);
      if (!existing || existing.length === 0) { skippedNotFound++; continue; }
      const r = existing[0];
      // hanya update jika masih 0 (belum ada koordinat)
      if (Number(r.latitude) !== 0 || Number(r.longitude) !== 0) continue;
      wouldUpdate++;
      if (apply) {
        try {
          await conn.execute("UPDATE gereja SET latitude = ?, longitude = ? WHERE id = ? AND latitude = 0 AND longitude = 0", [coords.latitude, coords.longitude, r.id]);
          updated++;
        } catch (e) { errors++; console.error("ERR", name, e.message); }
      }
    }
  }
  await conn.end();
  console.log(`wouldUpdate=${wouldUpdate} updated=${updated} skippedNoCoord=${skippedNoCoord} skippedNotFound=${skippedNotFound} errors=${errors}`);
  if (!apply) console.log("Jalankan dengan --apply untuk menulis ke DB.");
}

main().catch(e => { console.error("[FATAL]", e); process.exit(1); });