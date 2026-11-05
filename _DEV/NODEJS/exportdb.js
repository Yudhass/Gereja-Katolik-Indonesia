/**
 * exportdb.js - Export database db_gereja (schema + data) ke file .sql
 *
 * Usage:
 *   node exportdb.js                      # export ke _DEV/NODEJS/db_gereja.sql
 *   node exportdb.js --output backup.sql   # custom output
 *   node exportdb.js --database db_gereja  # custom db
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
};

function parseArgs() {
  const args = process.argv.slice(2);
  const opts = { output: null, database: process.env.DB_NAME || "db_gereja" };
  for (let i = 0; i < args.length; i++) {
    const a = args[i];
    if (a === "--output" && args[i + 1]) opts.output = args[++i];
    else if (a === "--database" && args[i + 1]) opts.database = args[++i];
    else if (a === "--help" || a === "-h") opts.help = true;
    else if (!a.startsWith("--")) opts.output = a;
  }
  return opts;
}

function sqlEscape(val) {
  if (val === null || val === undefined) return "NULL";
  if (typeof val === "boolean") return val ? "1" : "0";
  if (typeof val === "number") {
    if (!isFinite(val)) return "NULL";
    return String(val);
  }
  if (Buffer.isBuffer(val)) {
    return "X'" + val.toString("hex") + "'";
  }
  if (val instanceof Date) {
    const pad = (n) => String(n).padStart(2, "0");
    const s = `${val.getFullYear()}-${pad(val.getMonth() + 1)}-${pad(val.getDate())} ${pad(val.getHours())}:${pad(val.getMinutes())}:${pad(val.getSeconds())}`;
    return "'" + s.replace(/\\/g, "\\\\").replace(/'/g, "\\'") + "'";
  }
  let s = String(val);
  s = s.replace(/\\/g, "\\\\");
  s = s.replace(/'/g, "\\'");
  s = s.replace(/\u0000/g, "\\0");
  s = s.replace(/\n/g, "\\n");
  s = s.replace(/\r/g, "\\r");
  s = s.replace(/\x1a/g, "\\Z");
  return "'" + s + "'";
}

async function main() {
  const opts = parseArgs();
  if (opts.help) {
    console.log(`
Usage:
  node exportdb.js [options]

Options:
  --output <file>     Output .sql file (default: db_gereja.sql)
  --database <name>   Database name (default: db_gereja / DB_NAME)
  --help              Tampilkan bantuan
`);
    return;
  }

  const dbName = opts.database;
  const outPath = path.resolve(opts.output || path.join(__dirname, `${dbName}.sql`));

  const conn = await mysql.createConnection({ ...DB_CONFIG_BASE, database: dbName, dateStrings: true });
  console.log(`[EXPORT] Connected to ${DB_CONFIG_BASE.host}/${dbName}`);

  // list tables
  const [tablesRows] = await conn.query("SHOW TABLES");
  const tableNames = tablesRows.map((r) => Object.values(r)[0]);
  console.log(`[EXPORT] Found ${tableNames.length} tables`);

  let out = "";
  out += `-- Export database: ${dbName}\n`;
  out += `-- Generated: ${new Date().toISOString()}\n`;
  out += `SET NAMES utf8mb4;\n`;
  out += `SET FOREIGN_KEY_CHECKS=0;\n\n`;

  for (const table of tableNames) {
    console.log(`[EXPORT] Table: ${table}`);
    // create table
    const [createRows] = await conn.query(`SHOW CREATE TABLE \`${table}\``);
    const createSql = createRows[0]["Create Table"];
    out += `-- ----------------------------\n`;
    out += `-- Table structure for \`${table}\`\n`;
    out += `-- ----------------------------\n`;
    out += `DROP TABLE IF EXISTS \`${table}\`;\n`;
    out += createSql + ";\n\n";

    // columns
    const [colRows] = await conn.query(`SHOW COLUMNS FROM \`${table}\``);
    const columns = colRows.map((c) => c.Field);

    // data
    const [dataRows] = await conn.query(`SELECT * FROM \`${table}\``);
    if (dataRows.length === 0) {
      out += `-- no data for \`${table}\`\n\n`;
      continue;
    }
    out += `-- ----------------------------\n`;
    out += `-- Data for \`${table}\` (${dataRows.length} rows)\n`;
    out += `-- ----------------------------\n`;

    const BATCH = 100;
    for (let i = 0; i < dataRows.length; i += BATCH) {
      const batch = dataRows.slice(i, i + BATCH);
      const vals = batch.map((row) => {
        const v = columns.map((col) => sqlEscape(row[col]));
        return "(" + v.join(",") + ")";
      }).join(",\n");
      out += `INSERT INTO \`${table}\` (\`${columns.join("`,`")}\`) VALUES\n${vals};\n`;
    }
    out += "\n";
  }

  out += `SET FOREIGN_KEY_CHECKS=1;\n`;

  fs.writeFileSync(outPath, out, "utf-8");
  const stat = fs.statSync(outPath);
  console.log(`[EXPORT] Done -> ${outPath} (${(stat.size / 1024).toFixed(1)} KB)`);
  await conn.end();
}

main().catch((e) => {
  console.error("[FATAL]", e);
  process.exit(1);
});
