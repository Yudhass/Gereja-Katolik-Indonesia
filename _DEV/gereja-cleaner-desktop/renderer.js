// renderer.js - Logika UI: filter, select 10/20, buka banyak tab.
const $ = (id) => document.getElementById(id);

const state = {
  rows: [],        // semua data kosong dari DB
  filtered: [],    // hasil filter jenis + search
  selected: new Set(),
  page: 1,
  perPage: 50,
  baseUrl: '',
};

const isEmpty = (v) => v == null || String(v).trim() === '';
const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

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
  toast._t = setTimeout(() => ($('toast').textContent = ''), 4000);
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
      const hay = `${r.id} ${r.nama_gereja} ${r.alamat} ${r.provinsi} ${r.kabupaten_kota} ${r.kecamatan} ${r.kelurahan}`.toLowerCase();
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
  // Alamat bertanda "(S)" = belum ada tulisan alamat aslinya: tampilkan badge kuning.
  if (Number(r.alamat_tidak_lengkap) === 1) {
    const txt = isEmpty(r.alamat) ? '<i>(kosong)</i>' : esc(r.alamat);
    return `<div class="addr-full" title="${esc(r.alamat)}">${txt}</div><span class="badge-warn">(S) belum lengkap</span>`;
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
  $('urlPreview').textContent = state.baseUrl + '{id}';

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
  const base = $('baseUrl').value.trim().replace(/\/?$/, '/');
  state.baseUrl = base;
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
    if (d && d.baseUrl) $('baseUrl').value = d.baseUrl;
    state.baseUrl = $('baseUrl').value.trim();
  } catch (_) {}

  $('btnTest').onclick = async () => {
    setMsg('Menghubungkan...');
    const r = await window.api.testDb(cfg());
    if (r.ok) setMsg(`OK — total ${r.total} gereja, ${r.kosong} wilayah kosong, ${r.alamatS} alamat bertanda (S).`, 'ok');
    else setMsg('Gagal: ' + r.message, 'err');
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
      setMsg(`Ditemukan ${r.kosong} wilayah kosong + ${r.alamatS} alamat bertanda (S) (ditampilkan ${state.rows.length} data bermasalah).`, 'ok');
      toast('Data dimuat. Pilih 10 / 20 lalu klik Buka di Browser.');
      applyFilter();
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
    const base = $('baseUrl').value.trim().replace(/\/?$/, '/');
    state.baseUrl = base;
    await window.api.openUrls([base + id]);
    toast(`Tab edit #${id} dibuka di browser.`);
  });

  $('baseUrl').oninput = () => { state.baseUrl = $('baseUrl').value.trim(); render(); };

  $('btnCopy').onclick = async () => {
    const urls = selectedUrls();
    if (!urls.length) return toast('Belum ada yang dipilih.');
    try {
      await navigator.clipboard.writeText(urls.join('\n'));
      toast(`${urls.length} URL disalin ke clipboard.`);
    } catch { toast('Gagal menyalin.'); }
    render();
  };

  $('btnOpen').onclick = async () => {
    const urls = selectedUrls();
    if (!urls.length) return toast('Centang dulu minimal 1 data.');
    if (urls.length > 30 && !confirm(`Buka ${urls.length} tab sekaligus? Browser bisa berat. Lanjut?`)) return;
    $('btnOpen').disabled = true;
    toast(`Membuka ${urls.length} tab...`);
    const r = await window.api.openUrls(urls);
    $('btnOpen').disabled = false;
    render();
    if (r && r.opened) toast(`${r.opened} tab dibuka di browser. Silakan edit satu per satu, lalu klik Ambil Data lagi.`);
  };

  render();
}

document.addEventListener('DOMContentLoaded', init);
