Rencana Implementasi Aplikasi Gereja
Ringkasan
Proyek saat ini (SILAU - Laundry System) akan di-rewrite menjadi aplikasi Gereja menggunakan framework PHP yang sudah ada. Framework inti (core/) solid dan bisa dipakai ulang.
Fase 1: Database & Migrasi
#	Task	Detail
1.1	Migration admins	Buat migration tabel admins (id, nama_lengkap, email, password_hash, created_at)
1.2	Migration gereja	Buat migration tabel gereja (id, nama_gereja, alamat, latitude DECIMAL(10,8), longitude DECIMAL(11,8), kontak_telepon, deskripsi, foto_url, created_at, updated_at)
1.3	Migration jadwal_misa	Buat migration tabel jadwal_misa (id, gereja_id FK, hari ENUM, waktu_mulai TIME, kategori ENUM, keterangan, created_at)
1.4	Migration saran_jadwal	Buat migration tabel saran_jadwal (id, gereja_id FK, jadwal_id FK nullable, nama_pengunjung, saran_hari, saran_waktu, catatan, status ENUM, created_at)
1.5	Seeder admins	Seeder untuk akun admin default
1.6	Seeder gereja	Seeder data contoh beberapa gereja (3-5 data dummy)
1.7	Seeder jadwal_misa	Seeder jadwal contoh untuk setiap gereja
Fase 2: Models
#	Task	Detail
2.1	ModelAdmin	extends Model, table admins
2.2	ModelGereja	extends Model, table gereja
2.3	ModelJadwalMisa	extends Model, table jadwal_misa, relasi ke gereja
2.4	ModelSaranJadwal	extends Model, table saran_jadwal
Fase 3: Controllers - Guest (Public)
#	Task	Detail
3.1	HomeController	Landing page - daftar gereja + search + map
3.2	GerejaController	Detail gereja + jadwal misa + tombol arahkan ke lokasi
3.3	CariController	Pencarian & filter (nama, kota, hari, waktu)
3.4	SaranController	Form kirim saran koreksi jadwal + simpan ke saran_jadwal
Fase 4: Controllers - Admin
#	Task	Detail
4.1	AdminDashboardController	Dashboard admin (statistik: total gereja, jadwal, saran pending)
4.2	AdminGerejaController	CRUD data gereja (nama, koordinat, alamat, foto upload)
4.3	AdminJadwalController	CRUD jadwal misa per gereja
4.4	AdminSaranController	Inbox saran: list pending, approve (update jadwal), reject
Fase 5: Views - Guest (Public)
#	Task	Detail
5.1	Template guest	Layout berbeda dari admin, tanpa sidebar, dengan bottom nav di mobile
5.2	Halaman home	Hero + search bar + daftar gereja + map interaktif (Google Maps API)
5.3	Halaman detail gereja	Info lengkap + jadwal misa + tombol laporkan kesalahan + tombol arahkan
5.4	Halaman hasil pencarian	List filterable berdasarkan hari, waktu, lokasi
5.5	Form saran	Modal atau halaman terpisah untuk kirim koreksi
Fase 6: Views - Admin (CMS)
#	Task	Detail
6.1	Sidebar admin baru	Menu: Dashboard, Data Gereja, Jadwal Misa, Kotak Saran
6.2	Dashboard admin	Kartu statistik (total gereja, total jadwal, saran pending) + tabel recent
6.3	CRUD Gereja	List table + form modal untuk add/edit gereja (dengan map picker koordinat)
6.4	CRUD Jadwal	List jadwal per gereja + form add/edit dengan dropdown hari, kategori, waktu
6.5	Kotak Saran	Tabel saran dengan status Pending/Approved/Rejected + tombol approve/reject
Fase 7: Routes
#	Task	Detail
7.1	Guest routes	/, /gereja/{id}, /cari, /saran/kirim
7.2	Auth routes	/login, /login POST, /logout (gunakan framework yang sudah ada)
7.3	Admin routes	/admin/dashboard, /admin/gereja/*, /admin/jadwal/*, /admin/saran/*
Fase 8: Integrasi Peta (Maps)
#	Task	Detail
8.1	Google Maps API key	Konfigurasi API key di .env
8.2	Map component	Menampilkan pin gereja di peta interaktif (leaflet.js gratis atau Google Maps)
8.3	Map picker admin	Form input latitude/longitude dengan click-to-select di peta
8.4	Navigate button	Link https://www.google.com/maps/dir/?api=1&destination={lat},{lng}
Fase 9: Responsive UI
#	Task	Detail
9.1	Mobile (<768px)	List view vertikal + bottom navigation (5 menu: Beranda, Cari, Peta, Tentang)
9.2	Tablet (768-1024px)	Split screen: kiri daftar gereja, kanan detail/map
9.3	Desktop (>1024px)	Grid layout: sidebar kiri, konten utama, map besar di area utama
Fase 10: Tambahan & Polish
#	Task	Detail
10.1	Rebrand layout	Update title, favicon, logo dari laundry -> gereja
10.2	Auth Admin	Update AuthController untuk login admin gereja (pake email dari tabel admins)
10.3	Search engine	Pencarian berdasarkan nama gereja, kota, atau wilayah
10.4	Filter jadwal	Filter misa berdasarkan hari (Senin-Minggu) atau kategori (Harian/Mingguan/Hari Raya)
10.5	Validasi saran	Approve otomatis update jadwal_misa, Reject ubah status jadi Rejected
10.6	Upload foto gereja	Fitur upload foto dengan validasi ukuran/tipe
Catatan Penting:
- Framework yang sudah ada (core/) cukup matang dengan fitur routing, middleware, model query builder, CSRF, security headers, dll — akan dipakai ulang sepenuhnya
- Auth system sudah ada tinggal disesuaikan untuk login admin via email
- Tema Bootstrap sudah ada, tinggal diganti warna dan kontennya
- Gunakan Leaflet.js (gratis, tanpa API key) untuk peta agar lebih mudah, atau Google Maps jika diperlukan
Estimasi Prioritas:
- P1 (wajib rilis): Fase 1 → 2 → 3 → 4 → 5 → 6 → 7
- P2 (peningkatan): Fase 8 → 9
- P3 (nice to have): Fase 10
Apakah Anda ingin menambahkan/mengubah sesuatu dalam rencana ini, atau langsung saya mulai implementasi Fase 1?