Product Requirements Document (PRD): Aplikasi Informasi & Jadwal Gereja
1. Ringkasan Proyek
Aplikasi ini adalah platform direktori informasi gereja yang memudahkan umat untuk menemukan lokasi gereja terdekat, melihat detail fasilitas, dan mengecek jadwal ibadah/misa secara akurat. Aplikasi ini mengandalkan pendekatan crowdsourcing ringan di mana pengunjung dapat memberikan saran koreksi jadwal, yang nantinya diverifikasi oleh Admin.

2. Tujuan & Sasaran
Untuk Pengguna: Memberikan informasi lokasi dan jadwal misa yang akurat, cepat, dan mudah diakses dari perangkat apa pun.

Untuk Pengelola (Admin): Memiliki sistem manajemen konten (CMS) yang terpusat untuk memperbarui data gereja dan meninjau masukan dari umat.

3. Target Pengguna (User Personas)
| Peran              | Deskripsi & Hak Akses                                                                                                                                    |
| ------------------ | -------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Pengunjung (Guest) | Umat umum yang menggunakan aplikasi tanpa perlu login. Bisa mencari gereja, melihat rute peta, dan mengirimkan formulir saran koreksi jadwal.            |
| Admin Sistem       | Pengelola aplikasi yang memiliki akses penuh untuk menambah, mengubah, atau menghapus (CRUD) data gereja, jadwal, dan memvalidasi saran dari pengunjung. |

4. Fitur Utama (Core Features)
Sisi Pengunjung (Guest User)
Pencarian & Filter:

Pencarian berdasarkan nama gereja atau kota/wilayah.
Filter berdasarkan hari atau waktu misa (misal: "Misa Minggu Pagi").
Detail Gereja:

Nama, alamat lengkap, nomor kontak, dan deskripsi singkat.
Jadwal misa rutin (Harian, Mingguan) dan jadwal khusus (Paskah, Natal).
Integrasi Peta (Maps):
Menampilkan pin lokasi gereja di dalam peta interaktif.
Tombol "Arahkan ke Lokasi" (membuka Google Maps / Apple Maps di perangkat pengguna).
Fitur Saran/Koreksi Jadwal:
Tombol "Laporkan Kesalahan Jadwal" di halaman detail gereja.
Formulir singkat (Nama pengunjung, waktu jadwal yang disarankan, dan catatan).
Sisi Admin (CMS / Dasbor)

Manajemen Data Gereja:
Menambahkan gereja baru (Nama, Titik Koordinat Latitude/Longitude, Alamat, Foto).
Mengedit atau menonaktifkan data gereja.

Manajemen Jadwal:
Menambah, mengubah, dan menghapus jadwal misa untuk setiap gereja.

Kotak Masuk Saran (Suggestion Inbox):
Melihat daftar saran koreksi jadwal dari pengunjung.

Tombol Approve (langsung memperbarui jadwal utama) atau Reject (mengabaikan saran).

5. Desain & Antarmuka (UI/UX)
Tampilan harus berkonsep Modern, Bersih (Clean), dan Inklusif, mengingat rentang usia pengguna yang bervariasi (dari anak muda hingga lansia). Desain harus sepenuhnya Responsive dengan aturan berikut:

Mobile (Layar Kecil - < 768px): Tampilan berbentuk daftar vertikal (list view), menu navigasi berada di bawah (bottom navigation), dan peta menggunakan format layar penuh saat diklik.

Tablet (Layar Sedang - 768px hingga 1024px): Layout split-screen ringan. Daftar gereja di sebelah kiri, dan detail gereja (atau peta) di sebelah kanan.

Desktop (Layar Besar - > 1024px): Pemanfaatan grid system. Menampilkan dasbor peta interaktif berukuran besar dengan kartu-kartu informasi gereja yang luas dan mudah dibaca. Sidebar navigasi di sebelah kiri.

6. Kebutuhan Teknis (Technical Requirements)
Komponen | Rekomendasi / Deskripsi
Frontend (Web/App) | React.js / Next.js (untuk Web) atau Flutter (jika ingin dirilis ke Android/iOS). Framework UI seperti TailwindCSS untuk kemudahan responsive design.
Backend & API | Node.js, Python, atau PHP (Laravel). Menyediakan REST API untuk komunikasi data antara frontend dan database.
Database | PostgreSQL atau MySQL untuk menyimpan relasi data antara Gereja, Jadwal, dan Saran.
Layanan Pihak Ketiga | Google Maps API atau Mapbox API untuk rendering peta interaktif dan titik koordinat.

7. Metrik Keberhasilan (Success Metrics)
Untuk mengukur apakah aplikasi ini berjalan dengan baik setelah rilis, Anda bisa memantau:
Jumlah gereja yang berhasil diinput ke dalam sistem.
Jumlah pencarian (search queries) yang dilakukan oleh pengunjung per bulan.
Jumlah saran koreksi yang masuk dan dieksekusi oleh Admin (menandakan fitur partisipasi berjalan).

Tentu, ini adalah rancangan struktur database relasional (SQL) yang ideal untuk aplikasi informasi dan jadwal gereja Anda. Skema ini dirancang agar efisien, mudah dikembangkan, dan mencakup semua fitur yang Anda butuhkan.

Kita akan membaginya menjadi 4 tabel utama: admins, gereja, jadwal_misa, dan saran_jadwal.

Skema Database (Entity Relationship)
1. Tabel admins
Tabel ini digunakan untuk menyimpan data login pengelola aplikasi (CMS).
Nama Kolom | Tipe Data | Keterangan
id | INT (Primary Key, Auto Increment) | ID unik admin
nama_lengkap | VARCHAR(100) | Nama pengelola
email | VARCHAR(100) (Unique) | Email untuk login
password_hash | VARCHAR(255) | Password yang sudah dienkripsi
created_at | TIMESTAMP | Waktu akun dibuat

2. Tabel gereja
Tabel master untuk menyimpan semua informasi lokasi dan profil gereja.
Nama Kolom | Tipe Data | Keterangan
id | INT (Primary Key, Auto Increment) | ID unik gereja
nama_gereja | VARCHAR(150) | Contoh: "Gereja Katedral Jakarta"
alamat | TEXT | Alamat lengkap gereja
latitude | DECIMAL(10, 8) | Titik koordinat peta (Garis Lintang)
longitude | DECIMAL(11, 8) | Titik koordinat peta (Garis Bujur)
kontak_telepon | VARCHAR(20) | Nomor telepon/WhatsApp (opsional)
deskripsi | TEXT | Sejarah singkat atau info tambahan (opsional)
foto_url | VARCHAR(255) | Link URL foto utama gereja
created_at | TIMESTAMP | Waktu data ditambahkan
updated_at | TIMESTAMP | Waktu data terakhir diubah

Catatan Teknis: Penggunaan DECIMAL(10,8) dan (11,8) sangat penting agar akurasi pin Google Maps / Mapbox sangat presisi (bisa mendeteksi hingga ukuran meter).

3. Tabel jadwal_misa
Tabel ini menyimpan daftar waktu ibadah. Memiliki relasi (Foreign Key) ke tabel gereja.
Nama Kolom | Tipe Data | Keterangan
id | "INT (Primary Key |  Auto Increment)" | ID unik jadwal
gereja_id | INT (Foreign Key) | Merujuk ke gereja.id
hari | ENUM | "Pilihan: 'Senin' |  'Selasa' |  ... 'Minggu' |  'Spesial'"
waktu_mulai | TIME | Jam mulai (contoh: 07:30:00)
kategori | ENUM | "Pilihan: 'Harian' |  'Mingguan' |  'Hari Raya'"
keterangan | VARCHAR(100) | "Contoh: ""Misa Bahasa Inggris"" |  ""Misa Anak"""
created_at | TIMESTAMP | Waktu jadwal ditambahkan

4. Tabel saran_jadwal
Tabel ini menampung feedback dari pengunjung (Guest). Memiliki relasi ke gereja dan (opsional) ke jadwal_misa.
Nama Kolom | Tipe Data | Keterangan
id | "INT (Primary Key |  Auto Increment)" | ID unik saran
gereja_id | INT (Foreign Key) | Merujuk ke gereja.id
jadwal_id | INT (Foreign Key) | Merujuk ke jadwal_misa.id (Bisa NULL jika user menyarankan jadwal baru yang belum ada)
nama_pengunjung | VARCHAR(100) | Nama guest yang memberi saran (opsional)
saran_hari | VARCHAR(50) | Hari yang disarankan (jika ada perubahan)
saran_waktu | TIME | Jam yang disarankan (jika ada perubahan)
catatan | TEXT | "Alasan/Pesan user (misal: ""Sekarang misa jam 8 |  bukan jam 7"")"
status | ENUM | "Pilihan: 'Pending' |  'Approved' |  'Rejected'"
created_at | TIMESTAMP | Waktu saran dikirim oleh pengunjung

Penjelasan Alur Relasi (Cara Kerjanya)
One-to-Many (Gereja ke Jadwal): Satu gereja (misal: Gereja A) bisa memiliki banyak baris data di tabel jadwal_misa (Misa Minggu Pagi, Misa Minggu Sore, Misa Jumat Pertama).

Crowdsourcing (Saran Pengunjung): Saat guest melihat jadwal yang salah, mereka mengisi form. Data masuk ke tabel saran_jadwal dengan status Pending.

Validasi Admin:
Admin melihat daftar saran_jadwal yang masih Pending.
Jika admin klik Approve, sistem (backend) akan otomatis meng-update/menambah data di tabel jadwal_misa, lalu mengubah status saran menjadi Approved.
Jika ternyata saran itu spam atau salah, admin klik Reject, dan jadwal utama tidak berubah.