# Gereja Cleaner — Aplikasi Desktop (Electron + JS)

Aplikasi desktop untuk mencari data gereja yang **provinsi / kabupaten_kota / kecamatan / kelurahan-nya kosong**
atau **alamatnya masih bertanda "(S)"** (belum ada tulisan alamat aslinya),
lalu **memilih 10 / 20 data** dan **membuka banyak tab edit sekaligus** di browser:

```
http://192.168.1.240/Gereja-Katolik-Indonesia/admin/gereja/edit/{id}
```

Folder ini **terpisah** dari kode web utama. Kode web tidak diubah sama sekali.

## Cara menjalankan

**Cara 1 — klik 2x (disarankan):**
1. Klik 2x file `Jalankan.bat`
2. Otomatis `npm install` (hanya pertama kali) lalu aplikasi terbuka.

**Cara 2 — via terminal:**
```bat
cd _DEV\gereja-cleaner-desktop
npm install
npm start
```

## Alur pakai

1. Config DB otomatis terisi dari file `.env` project (`db_gereja`). Klik **Tes Koneksi**.
2. Klik **Ambil Data Kosong** → muncul semua gereja yang minimal 1 kolom wilayahnya kosong (∅ kosong)
   atau alamatnya masih bertanda `(S)` (badge kuning `(S) belum lengkap`).
3. Filter bila perlu: jenis kolom kosong / **Alamat bertanda (S)** / **Alamat tanpa tanda (S)** + pencarian nama/alamat + halaman 25/50/100.
4. Klik **✓ Pilih 10** atau **✓ Pilih 20** (mengambil N pertama dari hasil filter, ID terkecil dulu).
   Bisa juga centang manual / **✓ Halaman ini** / **✕ Bersihkan**.
5. Klik **🚀 Buka di Browser (N tab)** → N tab `.../admin/gereja/edit/{id}` terbuka di browser default.
6. Edit & simpan di browser satu per satu, kembali ke aplikasi, klik **Ambil Data Kosong** lagi —
   data yang sudah lengkap otomatis hilang dari daftar.

## File

| File | Fungsi |
|---|---|
| `main.js` | Electron main: window, query MySQL (`WHERE ... TRIM(col)=''`), `shell.openExternal` banyak URL + jeda 350ms |
| `preload.js` | Bridge aman renderer ↔ main |
| `index.html` / `renderer.js` / `styles.css` | UI: tabel + badge ∅ kosong, select 10/20, pagination, salin URL, buka tab |
| `Jalankan.bat` | Launcher Windows |
