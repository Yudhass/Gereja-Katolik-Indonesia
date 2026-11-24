// main.js - Main process Electron: window + IPC database + buka banyak URL ke browser.
const { app, BrowserWindow, ipcMain, shell } = require('electron');
const path = require('path');
const fs = require('fs');
const mysql = require('mysql2/promise');

let win = null;

function parseEnvFile(text) {
  const env = {};
  for (const rawLine of text.split(/\r?\n/)) {
    const line = rawLine.trim();
    if (!line || line.startsWith('#')) continue;
    const eqIdx = line.indexOf('=');
    if (eqIdx === -1) continue;
    const key = line.slice(0, eqIdx).trim();
    let val = line.slice(eqIdx + 1).trim();
    if ((val.startsWith('"') && val.endsWith('"')) || (val.startsWith("'") && val.endsWith("'"))) {
      val = val.slice(1, -1);
    } else {
      const commentIdx = val.indexOf(' #');
      if (commentIdx !== -1) val = val.slice(0, commentIdx).trim();
    }
    env[key] = val;
  }
  return env;
}

function getEnvDefaults() {
  // Cek .env di folder yang sama dengan .exe (saat sudah dibuild), lalu fallback ke ../../.env (mode dev).
  const db = { host: 'localhost', port: 3306, user: 'root', password: '', database: 'db_gereja' };
  let baseUrl = 'http://localhost/Gereja-Katolik-Indonesia/admin/gereja/edit/';
  try {
    const candidates = [
      path.join(path.dirname(process.execPath), '.env'),
      path.join(__dirname, '..', '..', '.env'),
    ];
    const envPath = candidates.find((p) => fs.existsSync(p));
    if (envPath) {
      const env = parseEnvFile(fs.readFileSync(envPath, 'utf8'));
      if (env.DB_HOST) db.host = env.DB_HOST;
      if (env.DB_PORT) db.port = Number(env.DB_PORT) || 3306;
      if (env.DB_USER) db.user = env.DB_USER;
      if (env.DB_PASS !== undefined) db.password = env.DB_PASS;
      if (env.DB_NAME) db.database = env.DB_NAME;
      if (env.BASE_URL) {
        baseUrl = env.BASE_URL.replace(/\/+$/, '') + '/admin/gereja/edit/';
      }
    }
  } catch (_) { /* pakai defaults */ }
  return { db, baseUrl };
}

function createWindow() {
  win = new BrowserWindow({
    width: 1300,
    height: 840,
    minWidth: 1000,
    minHeight: 640,
    autoHideMenuBar: true,
    backgroundColor: '#f1f4f9',
    title: 'Gereja Cleaner - Data Wilayah Kosong',
    webPreferences: {
      preload: path.join(__dirname, 'preload.js'),
      contextIsolation: true,
      nodeIntegration: false,
    },
  });
  win.loadFile(path.join(__dirname, 'index.html'));
  if (process.argv.includes('--dev')) win.webContents.openDevTools();
}

app.whenReady().then(() => {
  createWindow();
  app.on('activate', () => {
    if (BrowserWindow.getAllWindows().length === 0) createWindow();
  });
});

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') app.quit();
});

const EMPTY_COND = [
  "(provinsi IS NULL OR TRIM(provinsi) = '')",
  "(kabupaten_kota IS NULL OR TRIM(kabupaten_kota) = '')",
  "(kecamatan IS NULL OR TRIM(kecamatan) = '')",
  "(kelurahan IS NULL OR TRIM(kelurahan) = '')",
].join(' OR ');

// Alamat dianggap belum lengkap bila kosong ATAU masih bertanda "(S)"
// (hasil import mentah berupa plus-code tanpa nama jalan, mis. "8WR5+JH6, Pulau Sumatera 22476 (S)").
const ADDR_COND = "(alamat IS NULL OR TRIM(alamat) = '' OR alamat LIKE '%(S)%')";

const PROBLEM_COND = `(${EMPTY_COND} OR ${ADDR_COND})`;
const FETCH_LIMIT = 5000;
const MAX_OPEN_URLS = 100;

async function withConn(cfg, fn) {
  const conn = await mysql.createConnection({
    host: cfg.host, port: Number(cfg.port) || 3306,
    user: cfg.user, password: cfg.password || '',
    database: cfg.database, connectTimeout: 8000,
  });
  try {
    return await fn(conn);
  } finally {
    try { await conn.end(); } catch (_) {}
  }
}

function isSafeHttpUrl(raw) {
  try {
    const parsed = new URL(raw);
    return parsed.protocol === 'http:' || parsed.protocol === 'https:';
  } catch (_) {
    return false;
  }
}

ipcMain.handle('app:get-defaults', () => getEnvDefaults());

ipcMain.handle('db:test', async (_e, cfg) => {
  try {
    const out = await withConn(cfg, async (conn) => {
      const [[t]] = await conn.query('SELECT COUNT(*) AS total FROM gereja');
      const [[k]] = await conn.query(`SELECT COUNT(*) AS kosong FROM gereja WHERE ${EMPTY_COND}`);
      const [[a]] = await conn.query(`SELECT COUNT(*) AS n FROM gereja WHERE ${ADDR_COND}`);
      return { total: t.total, kosong: k.kosong, alamatS: a.n };
    });
    return { ok: true, ...out };
  } catch (err) {
    return { ok: false, message: err.message };
  }
});

ipcMain.handle('db:fetch', async (_e, cfg) => {
  try {
    const out = await withConn(cfg, async (conn) => {
      const [[t]] = await conn.query('SELECT COUNT(*) AS total FROM gereja');
      const [[k]] = await conn.query(`SELECT COUNT(*) AS kosong FROM gereja WHERE ${EMPTY_COND}`);
      const [[a]] = await conn.query(`SELECT COUNT(*) AS n FROM gereja WHERE ${ADDR_COND}`);
      const [[p]] = await conn.query(`SELECT COUNT(*) AS n FROM gereja WHERE ${PROBLEM_COND}`);
      const [rows] = await conn.query(
        `SELECT id, nama_gereja, alamat, provinsi, kabupaten_kota, kecamatan, kelurahan,
                (CASE WHEN ${EMPTY_COND} THEN 1 ELSE 0 END) AS wilayah_kosong,
                (CASE WHEN ${ADDR_COND} THEN 1 ELSE 0 END) AS alamat_tidak_lengkap
         FROM gereja WHERE ${PROBLEM_COND} ORDER BY id ASC LIMIT ${FETCH_LIMIT}`
      );
      return {
        total: t.total,
        kosong: k.kosong,
        alamatS: a.n,
        totalProblem: p.n,
        truncated: p.n > rows.length,
        rows,
      };
    });
    return { ok: true, ...out };
  } catch (err) {
    return { ok: false, message: err.message };
  }
});

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

ipcMain.handle('app:open-urls', async (_e, urls) => {
  // Buka banyak URL ke browser default satu per satu dengan jeda
  // agar tidak diblokir popup-blocker dan tidak membanjiri OS.
  const all = Array.isArray(urls) ? urls.filter(Boolean) : [];
  const list = all.slice(0, MAX_OPEN_URLS);
  const skipped = Math.max(0, all.length - list.length);
  let opened = 0;
  const failed = [];
  for (const u of list) {
    if (!win || win.isDestroyed()) break;
    if (!isSafeHttpUrl(u)) {
      failed.push(u);
      continue;
    }
    try {
      await shell.openExternal(u);
      opened++;
    } catch (_) {
      failed.push(u);
    }
    if (list.length > 1) await sleep(350);
  }
  return { ok: failed.length === 0, opened, failed, skipped };
});
