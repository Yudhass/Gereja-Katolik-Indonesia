// main.js - Main process Electron: window + IPC database + buka banyak URL ke browser.
const { app, BrowserWindow, ipcMain, shell } = require('electron');
const path = require('path');
const fs = require('fs');
const mysql = require('mysql2/promise');

let win = null;

function getEnvDefaults() {
  // Baca ../../.env (project root) agar config DB sama dengan aplikasi web.
  const defaults = { host: 'localhost', port: 3306, user: 'root', password: '', database: 'db_gereja' };
  try {
    const envPath = path.join(__dirname, '..', '..', '.env');
    if (fs.existsSync(envPath)) {
      const text = fs.readFileSync(envPath, 'utf8');
      const get = (k) => {
        const m = text.match(new RegExp('^' + k + '=(.*)$', 'm'));
        return m ? m[1].trim() : undefined;
      };
      if (get('DB_HOST')) defaults.host = get('DB_HOST');
      if (get('DB_PORT')) defaults.port = Number(get('DB_PORT')) || 3306;
      if (get('DB_USER')) defaults.user = get('DB_USER');
      if (get('DB_PASS') !== undefined) defaults.password = get('DB_PASS');
      if (get('DB_NAME')) defaults.database = get('DB_NAME');
    }
  } catch (_) { /* pakai defaults */ }
  return defaults;
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

ipcMain.handle('app:get-defaults', () => ({
  db: getEnvDefaults(),
  baseUrl: 'http://192.168.1.240/Gereja-Katolik-Indonesia/admin/gereja/edit/',
}));

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
      const [rows] = await conn.query(
        `SELECT id, nama_gereja, alamat, provinsi, kabupaten_kota, kecamatan, kelurahan,
                (CASE WHEN ${EMPTY_COND} THEN 1 ELSE 0 END) AS wilayah_kosong,
                (CASE WHEN ${ADDR_COND} THEN 1 ELSE 0 END) AS alamat_tidak_lengkap
         FROM gereja WHERE ${PROBLEM_COND} ORDER BY id ASC LIMIT 5000`
      );
      return { total: t.total, kosong: k.kosong, alamatS: a.n, rows };
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
  const list = Array.isArray(urls) ? urls.filter(Boolean).slice(0, 100) : [];
  let opened = 0;
  const failed = [];
  for (const u of list) {
    try {
      await shell.openExternal(u);
      opened++;
    } catch (err) {
      failed.push(u);
    }
    await sleep(350);
  }
  return { ok: true, opened, failed };
});
