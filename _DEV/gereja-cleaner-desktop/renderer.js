// renderer.js - Logika UI: filter, select 10/20, buka banyak tab.
const $ = (id) => document.getElementById(id);

const STORAGE_BASE_URL_KEY = 'gerejaCleaner.baseUrl';

const state = {
  rows: [],        // semua data bermasalah dari DB
  filtered: [],    // hasil filter jenis + search
  selected: new Set(),
  page: 1,
  perPage: 50,
  baseUrl: '',
};

const isEmpty = (v) => v == null || String(v).trim() === '';
const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

function normalizeBaseUrl(raw) {
  const trimmed = String(raw || '').trim();
  if (!trimmed) return '';
  return trimmed.replace(/\/+$/, '') + '/';
}

function isValidHttpBaseUrl(raw) {
  const normalized = normalizeBaseUrl(raw);
  if (!normalized) return false;
  try {
    const parsed = new URL(normalized);
    return parsed.protocol === 'http:' || parsed.protocol === 'https:';
  } catch (_) {
    return false;
  }
}

function updateUrlPreview() {
  const normalized = normalizeBaseUrl($('baseUrl').value);
  state.baseUrl = normalized;
  $('urlPreview').textContent = normalized ? `${normalized}{id}` : '(isi Base URL dengan http:// atau https://)';
}

function cfg() {
  return {
    host: $('dbHost').value.trim() || 'localhost',
    port: Number($('dbPort').value) || 3306,
    user: $('dbUser').value.trim() || 'root',
    password: $('dbPass').value,
    database: $('dbName').value.trim() || 'db_gereja',
  };
}

function setMsg(text, cls = '') {
  const el = $('cfgMsg');
  el.textContent = text;
  el.className = 'msg ' + cls;
}

function toast(text) {
  $('toast').textContent = text;
  clearTimeout(toast._t);
  toast._t = setTimeout(() => ($('toast').textContent = ''), 4500);
}

function applyFilter() {
  const jenis = $('filterJenis').value;
  const q = $('search').value.trim().toLowerCase();
  state.filtered = state.rows.filter((r) => {
    if (jenis === 'alamat_tidak_lengkap') {
      if (Number(r.alamat_tidak_lengkap) !== 1) return false;
    } else if (jenis === 'alamat_tanpa_tanda_s') {
      if (Number(r.alamat_tidak_lengkap) === 1) return false;
    } else if (jenis !== 'semua' && !isEmpty(r[jenis])) {
      return false;
    }
    if (q) {
      const hay = [
        r.id,
        r.nama_gereja,
        r.alamat,
        r.provinsi,
        r.kabupaten_kota,
        r.kecamatan,
        r.kelurahan,
      ].map((v) => (v == null ? '' : String(v))).join(' ').toLowerCase();
      if (!hay.includes(q)) return false;
    }
    return true;
  });
  state.page = 1;
  render();
}

function cell(val) {
  return isEmpty(val) ? '<span class="badge-empty">∅ kosong</span>' : `<span class="cell-ok">${esc(val)}</span>`;
}

function addrCell(r) {
  if (Number(r.alamat_tidak_lengkap) === 1) {
    if (isEmpty(r.alamat)) {
      return '<div class="addr-full"><i>(kosong)</i></div><span class="badge-warn">Alamat kosong</span>';
    }
    return `<div class="addr-full" title="${esc(r.alamat)}">${esc(r.alamat)}</div><span class="badge-warn">(S) belum lengkap</span>`;
  }
  return `<div class="addr-full" title="${esc(r.alamat)}">${esc(r.alamat)}</div>`;
}

function render() {
  const totalPages = Math.max(1, Math.ceil(state.filtered.length / state.perPage));
  if (state.page > totalPages) state.page = totalPages;
  const start = (state.page - 1) * state.perPage;
  const pageRows = state.filtered.slice(start, start + state.perPage);

  const tb = $('tbody');
  if (!pageRows.length) {
    tb.innerHTML = '<tr><td colspan="9" class="empty">Tidak ada data cocok filter. Coba ubah filter / kata kunci.</td></tr>';
  } else {
    tb.innerHTML = pageRows.map((r) => {
      const sel = state.selected.has(r.id);
      return `<tr class="${sel ? 'selected' : ''}" data-id="${r.id}">
        <td class="c-check"><input type="checkbox" data-check="${r.id}" ${sel ? 'checked' : ''} /></td>
        <td><b>${r.id}</b></td>
        <td>${esc(r.nama_gereja)}</td>
        <td>${addrCell(r)}</td>
        <td>${cell(r.provinsi)}</td>
        <td>${cell(r.kabupaten_kota)}</td>
        <td>${cell(r.kecamatan)}</td>
        <td>${cell(r.kelurahan)}</td>
        <td><button class="link-btn" data-open="${r.id}">Buka ↗</button></td>
      </tr>`;
    }).join('');
  }

  $('pageInfo').textContent = `Halaman ${state.filtered.length ? state.page : 0} / ${state.filtered.length ? totalPages : 0} • ${state.filtered.length} data`;
  $('prevPage').disabled = state.page <= 1;
  $('nextPage').disabled = state.page >= totalPages;
  $('infoDipilih').textContent = `${state.selected.size} dipilih`;
  $('statDipilih').textContent = state.selected.size;
  $('openCount').textContent = state.selected.size;
  $('infoFilter').textContent = `• tampil ${pageRows.length} dari ${state.filtered.length} (total bermasalah ${state.rows.length})`;
  updateUrlPreview();

  const allChecked = pageRows.length > 0 && pageRows.every((r) => state.selected.has(r.id));
  $('checkAll').checked = allChecked;
}

function pickFirst(n) {
  // Pilih n data pertama dari hasil filter yang belum dipilih (urut ID).
  let added = 0;
  for (const r of state.filtered) {
    if (added >= n) break;
    if (!state.selected.has(r.id)) { state.selected.add(r.id); added++; }
  }
  render();
  toast(added ? `${added} data ditambahkan ke pilihan.` : 'Semua data filter sudah dipilih.');
}

function selectedUrls() {
  const base = normalizeBaseUrl($('baseUrl').value);
  state.baseUrl = base;
  if (!isValidHttpBaseUrl(base)) return null;
  return [...state.selected].sort((a, b) => a - b).map((id) => base + id);
}

async function init() {
  try {
    const d = await window.api.getDefaults();
    if (d && d.db) {
      $('dbHost').value = d.db.host; $('dbPort').value = d.db.port;
      $('dbUser').value = d.db.user; $('dbPass').value = d.db.password || '';
      $('dbName').value = d.db.database;
    }
    const savedBaseUrl = localStorage.getItem(STORAGE_BASE_URL_KEY);
    if (savedBaseUrl) $('baseUrl').value = savedBaseUrl;
    else if (d && d.baseUrl) $('baseUrl').value = d.baseUrl;
    updateUrlPreview();
  } catch (_) {}

  $('btnTest').onclick = async () => {
    setMsg('Menghubungkan...');
    $('btnTest').disabled = true;
    try {
      const r = await window.api.testDb(cfg());
      if (r.ok) setMsg(`OK — total ${r.total} gereja, ${r.kosong} wilayah kosong, ${r.alamatS} alamat kosong/(S).`, 'ok');
      else setMsg('Gagal: ' + r.message, 'err');
    } catch (err) {
      setMsg('Gagal: ' + (err && err.message ? err.message : 'Kesalahan tidak diketahui'), 'err');
    } finally {
      $('btnTest').disabled = false;
    }
  };

  $('btnLoad').onclick = async () => {
    setMsg('Mengambil data...');
    $('btnLoad').disabled = true;
    try {
      const r = await window.api.fetchData(cfg());
      if (!r.ok) { setMsg('Gagal: ' + r.message, 'err'); return; }
      state.rows = r.rows || [];
      state.selected.clear();
      $('statTotal').textContent = r.total;
      $('statKosong').textContent = r.kosong;
      $('statAlamat').textContent = r.alamatS;
      const truncNote = r.truncated ? ` (dibatasi 5000 dari ${r.totalProblem} data bermasalah)` : '';
      setMsg(`Ditemukan ${r.kosong} wilayah kosong + ${r.alamatS} alamat kosong/(S) — tampil ${state.rows.length} data bermasalah${truncNote}.`, 'ok');
      toast('Data dimuat. Pilih 10 / 20 lalu klik Buka di Browser.');
      applyFilter();
    } catch (err) {
      setMsg('Gagal: ' + (err && err.message ? err.message : 'Kesalahan tidak diketahui'), 'err');
    } finally {
      $('btnLoad').disabled = false;
    }
  };

  $('filterJenis').onchange = applyFilter;
  $('search').oninput = applyFilter;
  $('perPage').onchange = () => { state.perPage = Number($('perPage').value); state.page = 1; render(); };
  $('prevPage').onclick = () => { if (state.page > 1) { state.page--; render(); } };
  $('nextPage').onclick = () => { state.page++; render(); };

  $('btnPick10').onclick = () => pickFirst(10);
  $('btnPick20').onclick = () => pickFirst(20);
  $('btnPickPage').onclick = () => {
    const start = (state.page - 1) * state.perPage;
    state.filtered.slice(start, start + state.perPage).forEach((r) => state.selected.add(r.id));
    render();
  };
  $('btnClear').onclick = () => { state.selected.clear(); render(); };
  $('checkAll').onchange = (e) => {
    const start = (state.page - 1) * state.perPage;
    const pageRows = state.filtered.slice(start, start + state.perPage);
    if (e.target.checked) pageRows.forEach((r) => state.selected.add(r.id));
    else pageRows.forEach((r) => state.selected.delete(r.id));
    render();
  };

  $('tbody').addEventListener('change', (e) => {
    const id = Number(e.target.getAttribute('data-check'));
    if (!id) return;
    if (e.target.checked) state.selected.add(id);
    else state.selected.delete(id);
    render();
  });

  $('tbody').addEventListener('click', async (e) => {
    const id = e.target.getAttribute('data-open');
    if (!id) return;
    const base = normalizeBaseUrl($('baseUrl').value);
    if (!isValidHttpBaseUrl(base)) {
      toast('Base URL tidak valid. Gunakan format http:// atau https://.');
      return;
    }
    try {
      const r = await window.api.openUrls([base + id]);
      if (r && r.opened > 0) toast(`Tab edit #${id} dibuka di browser.`);
      else toast(`Gagal membuka tab edit #${id}. Periksa Base URL.`);
    } catch (_) {
      toast(`Gagal membuka tab edit #${id}.`);
    }
  });

  $('baseUrl').oninput = () => {
    const val = $('baseUrl').value.trim();
    if (val) localStorage.setItem(STORAGE_BASE_URL_KEY, val);
    else localStorage.removeItem(STORAGE_BASE_URL_KEY);
    updateUrlPreview();
  };

  $('btnCopy').onclick = async () => {
    const urls = selectedUrls();
    if (urls === null) return toast('Base URL tidak valid. Gunakan format http:// atau https://.');
    if (!urls.length) return toast('Belum ada yang dipilih.');
    try {
      await navigator.clipboard.writeText(urls.join('\n'));
      toast(`${urls.length} URL disalin ke clipboard.`);
    } catch (_) { toast('Gagal menyalin.'); }
  };

  $('btnOpen').onclick = async () => {
    const urls = selectedUrls();
    if (urls === null) return toast('Base URL tidak valid. Gunakan format http:// atau https://.');
    if (!urls.length) return toast('Centang dulu minimal 1 data.');
    if (urls.length > 30 && !confirm(`Buka ${urls.length} tab sekaligus? Browser bisa berat. Lanjut?`)) return;
    $('btnOpen').disabled = true;
    toast(`Membuka ${urls.length} tab...`);
    try {
      const r = await window.api.openUrls(urls);
      if (r && r.opened > 0) {
        const parts = [`${r.opened} tab dibuka di browser.`];
        if (r.failed && r.failed.length) parts.push(`${r.failed.length} gagal.`);
        if (r.skipped > 0) parts.push(`${r.skipped} dilewati (maks. 100 tab sekali buka).`);
        toast(parts.join(' '));
      } else {
        toast('Gagal membuka tab. Periksa Base URL atau browser default Anda.');
      }
    } catch (_) {
      toast('Terjadi kesalahan saat membuka tab di browser.');
    } finally {
      $('btnOpen').disabled = false;
      render();
    }
  };

  render();
}

document.addEventListener('DOMContentLoaded', init);
