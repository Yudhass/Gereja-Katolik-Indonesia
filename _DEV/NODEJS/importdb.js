/**
 * importdb.js - Import file .sql ke database db_gereja
 *
 * Usage:
 *   node importdb.js                       # import db_gereja.sql dari folder yang sama
 *   node importdb.js --file backup.sql     # custom file
 *   node importdb.js --database db_gereja  # custom db
 *
 * Env: DB_HOST, DB_USER, DB_PASS, DB_NAME
 */

const fs = require("fs");
const path = require("path");
const mysql = require("mysql2/promise");

const DB_CONFIG_BASE = {
  host: process.env.DB_HOST || "localhost",
  user: process.env.DB_USER || "root",
  password: process.env.DB_PASS || "",
  charset: "utf8mb4",
  multipleStatements: true,
};

function parseArgs() {
  const args = process.argv.slice(2);
  const opts = { file: null, database: process.env.DB_NAME || "db_gereja" };
  for (let i = 0; i < args.length; i++) {
    const a = args[i];
    if (a === "--file" && args[i + 1]) opts.file = args[++i];
    else if (a === "--database" && args[i + 1]) opts.database = args[++i];
    else if (a === "--help" || a === "-h") opts.help = true;
    else if (!a.startsWith("--")) opts.file = a;
  }
  return opts;
}

async function main() {
  const opts = parseArgs();
  if (opts.help) {
    console.log(`
Usage:
  node importdb.js [options]

Options:
  --file <path>       File .sql yang akan diimport (default: db_gereja.sql)
  --database <name>   Database target (default: db_gereja / DB_NAME)
  --help              Tampilkan bantuan
`);
    return;
  }

  const dbName = opts.database;
  const filePath = path.resolve(opts.file || path.join(__dirname, `${dbName}.sql`));
  if (!fs.existsSync(filePath)) {
    console.error(`[IMPORT] File tidak ditemukan: ${filePath}`);
    process.exit(1);
  }
  const sql = fs.readFileSync(filePath, "utf-8");
  console.log(`[IMPORT] File: ${filePath} (${(Buffer.byteLength(sql, "utf-8") / 1024).toFixed(1)} KB)`);

  // Pastikan database ada
  const adminConn = await mysql.createConnection(DB_CONFIG_BASE);
  await adminConn.query(`CREATE DATABASE IF NOT EXISTS \`${dbName}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci`);
  await adminConn.end();

  const conn = await mysql.createConnection({ ...DB_CONFIG_BASE, database: dbName });
  console.log(`[IMPORT] Connected to ${DB_CONFIG_BASE.host}/${dbName}`);
  console.log(`[IMPORT] Menjalankan SQL...`);
  const t0 = Date.now();
  await conn.query(sql);
  const elapsed = ((Date.now() - t0) / 1000).toFixed(2);
  console.log(`[IMPORT] Selesai dalam ${elapsed}s`);
  await conn.end();
}

main().catch((e) => {
  console.error("[FATAL]", e.message);
  process.exit(1);
});
