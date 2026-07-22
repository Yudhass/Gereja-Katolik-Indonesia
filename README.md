# SILAU - Sistem Informasi Laundry

Sistem Informasi Laundry yang **terintegrasi dengan RFID** untuk manajemen laundry yang efisien dan modern. Dibangun dengan arsitektur MVC PHP yang **kompatibel dengan PHP 5.2, 7, 8 dan versi lebih tinggi**.

## 🎯 Tentang Proyek

SILAU (Sistem Informasi Laundry) adalah aplikasi manajemen laundry lengkap yang dirancang untuk memudahkan pengelolaan bisnis laundry dengan fitur-fitur modern termasuk integrasi RFID untuk tracking pakaian pelanggan.

## ✨ Fitur Utama

### 🏢 Fitur Bisnis
- 🏷️ **Integrasi RFID**: Tracking pakaian dengan teknologi RFID
- 👥 **Manajemen Pelanggan**: Kelola data pelanggan dengan mudah
- 📦 **Manajemen Transaksi**: Pencatatan order, pembayaran, dan status laundry
- 📊 **Dashboard & Reporting**: Laporan lengkap bisnis laundry
- 💰 **Sistem Pembayaran**: Multi metode pembayaran
- 📱 **Notifikasi**: Update status laundry real-time

### 🔧 Fitur Teknis
- ✅ **Kompatibilitas Multi-Versi**: PHP 5.2, 7, 8 dan lebih tinggi
- ✅ **Auto-Detection**: Otomatis menggunakan PDO atau mysql_* sesuai versi PHP
- ✅ **Environment Configuration**: Semua config terpusat di file .env
- ✅ **CRUD Lengkap**: Insert, Select All, Select One, Select Where, Update, Delete
- ✅ **Query Builder**: Mendukung method chaining untuk query kompleks
- ✅ **MVC Pattern**: Struktur yang clean dan terorganisir
- ✅ **Authentication System**: Login, register, dan role-based access
- ✅ **Middleware System**: AuthMiddleware, GuestMiddleware, RoleMiddleware
- ✅ **10 Layer Security**: CSRF, XSS, SQL Injection, dan lebih banyak lagi
- ✅ **Flash Messages**: Sistem pesan notifikasi
- ✅ **Helper Functions**: Berbagai fungsi helper yang berguna

## 📁 Struktur Folder

```
SILAU/
├── .env                             # Environment configuration
├── .env.example                     # Environment template
├── .gitignore
├── README.md
├── _DEV/
│   ├── CHANGELOG_SECURITY.md        # Changelog security features
│   ├── CONTOH_IMPLEMENTASI.md       # Contoh implementasi CRUD
│   ├── database_secure.sql          # SQL schema dengan security
│   ├── database.sql                 # SQL schema dasar
│   ├── DOKUMENTASI_CRUD.md          # Dokumentasi lengkap CRUD
│   ├── DOKUMENTASI_ENV.md           # Dokumentasi environment config
│   ├── DOKUMENTASI_MIDDLEWARE.md    # Dokumentasi middleware system
│   ├── DOKUMENTASI_SECURITY.md      # Dokumentasi security lengkap
│   ├── ENV_QUICK_START.md           # Quick start environment
│   ├── ERROR_HANDLING_DOCUMENTATION.md
│   ├── FIX_ERROR_LOG.md            # Log perbaikan error
│   ├── PASSWORD_HASHING_FIX.md     # Dokumentasi password hashing
│   ├── SECURITY_FEATURES.md        # 10 Layer security features
│   └── target.md                    # Target development
├── app/
│   ├── init.php
│   ├── controllers/
│   │   ├── AuthController.php       # Controller authentication
│   │   └── HomeController.php       # Controller home/dashboard
│   ├── core/
│   │   ├── App.php                  # Application core
│   │   ├── Autoloader.php           # Class autoloader
│   │   ├── Config.php               # Konfigurasi (baca dari .env)
│   │   ├── Controller.php           # Base controller
│   │   ├── Database.php             # Support PHP 5.2 - 8+
│   │   ├── Env.php                  # Environment loader
│   │   ├── Helper.php               # Helper functions
│   │   ├── Middleware.php           # Base middleware
│   │   ├── Model.php                # CRUD methods lengkap
│   │   ├── Router.php               # Router dengan middleware
│   │   └── Security.php             # Security functions
│   ├── middlewares/
│   │   ├── AuthMiddleware.php       # Cek user login
│   │   ├── GuestMiddleware.php      # Cek user belum login
│   │   └── RoleMiddleware.php       # Cek role user
│   ├── models/
│   │   └── User.php                 # Model User
│   ├── routes/
│   │   └── routes.php               # Definisi routes
│   └── views/
│       ├── home.php
│       ├── admin/                   # Views admin panel
│       ├── auth/                    # Views login, register
│       └── errors/                  # Error pages
├── public/
│   ├── index.php                    # Entry point
│   └── assets/                      # CSS, JS, Images
│       ├── css/
│       ├── js/
│       ├── images/
│       └── plugins/
└── storage/
    ├── cache/                       # Cache files
    └── logs/                        # Log files
```

## 🚀 Quick Start

### 1. Clone Repository

```bash
git clone https://github.com/Yudhass/MVC-PHP-5-TEMPLATE.git SILAU
cd SILAU
```

### 2. Setup Environment Configuration

```bash
# Copy .env.example ke .env
copy .env.example .env

# Edit .env dan sesuaikan dengan environment Anda
notepad .env
```

**Konfigurasi .env:**

```env
# Database Configuration
DB_HOST=localhost
DB_NAME=silau_db
DB_USER=root
DB_PASS=
DB_PORT=3306

# Application Settings
APP_NAME=SILAU
APP_ENV=development
BASE_URL=http://localhost/SILAU/
SESSION_NAME=silau_session
SESSION_TIMEOUT=3600

# Security Settings
CSRF_TOKEN_NAME=csrf_token
RATE_LIMIT_MAX_ATTEMPTS=5
RATE_LIMIT_TIME_WINDOW=300
```

### 3. Import Database

```sql
CREATE DATABASE silau_db;
```

Import dari file SQL:

```bash
mysql -u root -p silau_db < _DEV/database_secure.sql
```

Atau gunakan phpMyAdmin untuk import file `database_secure.sql`.

### 4. Jalankan Aplikasi

**Untuk PHP built-in server:**
```bash
php -S localhost:8000 -t public
```

**Untuk XAMPP/WAMP:**
- Letakkan folder SILAU di `htdocs/` atau `www/`
- Akses: `http://localhost/SILAU/`

### 5. Login Default

Setelah database diimport, gunakan kredensial berikut untuk login:

```
Username: admin
Password: admin123
```

⚠️ **Penting**: Segera ubah password default setelah login pertama!

## 📚 Dokumentasi CRUD

### Method yang Tersedia

|             Method             |        Fungsi       | Return  |
|--------------------------------|---------------------|---------|
| `insert($data)`                | Tambah data baru    | Object  |
| `selectAll()`                  | Ambil semua data    | Array   |
| `selectOne($id)`               | Ambil satu data     | Object  |
| `selectWhere($col, $val, $op)` | Select dengan WHERE | Array   |
| `update($data)`                | Update data         | Integer |
| `updateById($id, $data)`       | Update by ID        | Integer |
| `delete($id)`                  | Hapus data          | Integer |
| `deleteById($id)`              | Alias delete        | Integer |

### Contoh Penggunaan CRUD

```php
// Model User
$user = new User();

// INSERT - Tambah pelanggan baru
$user->insert(array(
    'nama' => 'John Doe',
    'email' => 'john@example.com',
    'telepon' => '081234567890'
));

// SELECT ALL - Ambil semua pelanggan
$allUsers = $user->selectAll();

// SELECT ONE - Ambil data pelanggan berdasarkan ID
$oneUser = $user->selectOne(1);

// SELECT WHERE - Cari pelanggan berdasarkan nama
$users = $user->selectWhere('nama', '%John%', 'LIKE');

// UPDATE BY ID - Update data pelanggan
$user->updateById(1, array(
    'nama' => 'Jane Doe',
    'email' => 'jane@example.com'
));

// DELETE BY ID - Hapus pelanggan
$user->deleteById(1);
```

### Query Builder

```php
$user = new User();

// Single WHERE
$users = $user->where('nama', 'John')->get();

// Multiple WHERE
$users = $user->where('status', 'active')
              ->where('role', 'customer')
              ->get();

// Get first result
$user = $user->where('email', 'john@example.com')->first();

// ORDER BY
$users = $user->orderBy('created_at', 'DESC')->get();

// LIMIT
$users = $user->limit(10)->get();
```

**Dokumentasi lengkap:** [DOKUMENTASI_CRUD.md](_DEV/DOKUMENTASI_CRUD.md)

## �️ Middleware System

Middleware adalah lapisan keamanan yang berjalan **sebelum** controller untuk memfilter HTTP requests.

### Middleware yang Tersedia

#### 1. AuthMiddleware
Memastikan user sudah login sebelum mengakses halaman.

```php
// Di routes.php
$router->get('/dashboard', 'HomeController@index', array('auth'));
```

#### 2. GuestMiddleware
Memastikan user belum login (untuk halaman login/register).

```php
// Di routes.php
$router->get('/login', 'AuthController@login', array('guest'));
```

#### 3. RoleMiddleware
Memastikan user memiliki role tertentu.

```php
// Di routes.php
$router->get('/admin', 'AdminController@index', array('role:admin'));
$router->get('/staff', 'StaffController@index', array('role:staff,admin'));
```

### Membuat Middleware Custom

```php
<?php
require_once dirname(__FILE__) . '/../core/Middleware.php';

class CustomMiddleware extends Middleware
{
    public function handle()
    {
        // Logic middleware Anda
        if (!$condition) {
            $this->redirectTo('/error');
            return false;
        }
        return true;
    }
}
```

**Dokumentasi lengkap:** [DOKUMENTASI_MIDDLEWARE.md](_DEV/DOKUMENTASI_MIDDLEWARE.md)

## 🛠️ Membuat Model Baru

Contoh membuat model untuk tabel transaksi laundry:

```php
<?php 
require_once dirname(__FILE__) . '/../core/Model.php';

class Transaction extends Model
{
    protected $table = 'tbl_transaction';
    protected $fields = array('id', 'customer_id', 'rfid_tag', 'status', 'total', 'created_at');
    
    // Custom method untuk mendapatkan transaksi berdasarkan RFID
    public function getByRFID($rfid_tag)
    {
        return $this->selectWhere('rfid_tag', $rfid_tag, '=');
    }
    
    // Custom method untuk update status
    public function updateStatus($id, $status)
    {
        return $this->updateById($id, array('status' => $status));
    }
}
```

## 🎯 Routing

Edit file [app/routes/routes.php](app/routes/routes.php):

```php
return array(
    // Public Routes
    array('GET', '/', 'HomeController@index', array('auth')),
    
    // Authentication Routes (Guest only)
    array('GET', '/login', 'AuthController@login', array('guest')),
    array('POST', '/login', 'AuthController@doLogin', array('guest')),
    array('GET', '/register', 'AuthController@register', array('guest')),
    array('POST', '/register', 'AuthController@doRegister', array('guest')),
    array('GET', '/logout', 'AuthController@logout', array('auth')),
    
    // Customer Management (Auth required)
    array('GET', '/customers', 'CustomerController@index', array('auth')),
    array('POST', '/customers', 'CustomerController@store', array('auth')),
    array('GET', '/customers/{id}', 'CustomerController@show', array('auth')),
    array('POST', '/customers/update/{id}', 'CustomerController@update', array('auth')),
    array('POST', '/customers/delete/{id}', 'CustomerController@destroy', array('auth')),
    
    // Transaction Management (Auth + Role)
    array('GET', '/transactions', 'TransactionController@index', array('auth', 'role:admin,staff')),
    array('POST', '/transactions', 'TransactionController@store', array('auth', 'role:admin,staff')),
    
    // RFID Integration
    array('GET', '/rfid/scan', 'RFIDController@scan', array('auth')),
    array('POST', '/rfid/register', 'RFIDController@register', array('auth')),
    
    // Admin Routes (Admin only)
    array('GET', '/admin', 'AdminController@index', array('auth', 'role:admin')),
    array('GET', '/admin/reports', 'AdminController@reports', array('auth', 'role:admin')),
);
```

## 💡 Helper Functions

### URL & Navigation
```php
// Base URL
echo base_url('customers'); 
// Output: http://localhost/SILAU/customers

// Redirect
redirect('/login');
redirectBack('Data berhasil disimpan', 'success');
```

### Environment Variables
```php
// Mengakses environment variables
$dbHost = env('DB_HOST', 'localhost');
$appName = env('APP_NAME');
$sessionTimeout = env('SESSION_TIMEOUT', 3600);
```

### Input Sanitization
```php
// Clean user input
$nama = clean_input($_POST['nama']);
$email = sanitize($_POST['email'], 'email');
$url = sanitize($_POST['website'], 'url');
```

### Security
```php
// CSRF Protection
<form method="POST">
    <?php echo csrf_field(); ?>
    <!-- form fields -->
</form>

// Verify CSRF in controller
if (verify_csrf()) {
    // Process form
}

// Password Hashing
$hashedPassword = hash_password($password);
if (verify_password($password, $hashedPassword)) {
    // Password valid
}

// Rate Limiting
if (rate_limit('login', 5, 300)) {
    // Allow action
} else {
    // Too many attempts
}
```

### Flash Messages
```php
// Set flash message
set_flash('success', 'Data berhasil disimpan');
set_flash('error', 'Terjadi kesalahan');

// Display flash message (in view)
if (has_flash('success')) {
    echo '<div class="alert alert-success">' . get_flash('success') . '</div>';
}
```

### Validation
```php
// Validasi input
$errors = validate($_POST, array(
    'nama' => 'required|min:3|max:100',
    'email' => 'required|email',
    'telepon' => 'required|numeric',
    'password' => 'required|min:6'
));

if (!empty($errors)) {
    // Ada error validasi
    foreach ($errors as $field => $error) {
        echo $error . '<br>';
    }
}
```

## 🌍 Environment Configuration

Semua konfigurasi aplikasi tersentralisasi di file `.env`:

```env
# ========================================
# DATABASE CONFIGURATION
# ========================================
DB_HOST=localhost
DB_NAME=silau_db
DB_USER=root
DB_PASS=
DB_PORT=3306

# ========================================
# APPLICATION SETTINGS
# ========================================
APP_NAME=SILAU
APP_ENV=development
APP_DEBUG=true
BASE_URL=http://localhost/SILAU/
TIMEZONE=Asia/Jakarta

# ========================================
# SESSION CONFIGURATION
# ========================================
SESSION_NAME=silau_session
SESSION_TIMEOUT=3600
SESSION_SECURE=false
SESSION_HTTPONLY=true
SESSION_SAMESITE=Strict

# ========================================
# SECURITY SETTINGS
# ========================================
CSRF_TOKEN_NAME=csrf_token
CSRF_TIMEOUT=7200
RATE_LIMIT_MAX_ATTEMPTS=5
RATE_LIMIT_TIME_WINDOW=300
PASSWORD_MIN_LENGTH=6

# ========================================
# FILE UPLOAD SETTINGS
# ========================================
UPLOAD_MAX_SIZE=2097152
UPLOAD_ALLOWED_TYPES=jpg,jpeg,png,pdf
UPLOAD_PATH=public/uploads/

# ========================================
# RFID SETTINGS
# ========================================
RFID_ENABLED=true
RFID_READER_PORT=COM3
RFID_BAUD_RATE=9600

# ========================================
# EMAIL SETTINGS (Optional)
# ========================================
MAIL_ENABLED=false
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
MAIL_FROM_NAME=SILAU System
```

**Dokumentasi lengkap:** [DOKUMENTASI_ENV.md](_DEV/DOKUMENTASI_ENV.md)

## 🔒 Fitur Keamanan (10 Layer Security)

SILAU dilengkapi dengan **10 layer keamanan** komprehensif:

### 1. 🛡️ CSRF Protection
- Token unik untuk setiap form submission
- Mencegah Cross-Site Request Forgery attacks
- Helper: `csrf_field()`, `verify_csrf()`

### 2. 🔐 XSS Protection  
- Output escaping otomatis
- Sanitasi HTML content
- Helper: `esc()`, `e()`, `sanitize()`

### 3. ✅ Input Validation & Sanitization
- Validasi email, URL, numeric, string
- Sanitasi semua user input
- Helper: `validate()`, `clean_input()`

### 4. 🔑 Password Security
- Password hashing dengan bcrypt/argon2
- Kompatibel PHP 5.2+ (auto-fallback)
- Helper: `hash_password()`, `verify_password()`

### 5. 🍪 Session Security
- Secure session configuration
- Session regeneration & timeout
- HTTPOnly & SameSite cookies

### 6. 🚦 Rate Limiting
- Mencegah brute force attacks
- Configurable max attempts
- Helper: `rate_limit()`

### 7. 📁 File Upload Security
- File type validation (MIME type)
- File size limit
- Safe filename generation

### 8. 🔒 Security Headers
- X-Frame-Options
- X-Content-Type-Options
- Content-Security-Policy

### 9. 🗃️ SQL Injection Protection
- Prepared statements
- Parameter binding
- Query sanitization

### 10. 🔐 Authentication & Authorization
- Role-based access control (RBAC)
- Middleware system
- Session management

**Dokumentasi lengkap:** [SECURITY_FEATURES.md](_DEV/SECURITY_FEATURES.md)

## � Kompatibilitas PHP

### PHP 5.2
- Menggunakan `mysql_*` functions
- Tidak memerlukan PDO extension
- Full support untuk semua CRUD operations
- Password hashing menggunakan MD5 (fallback)

### PHP 7+
- Menggunakan PDO dengan prepared statements
- Password hashing dengan `password_hash()` dan `password_verify()`
- Error handling lebih baik
- Performance optimal

### PHP 8+
- Full PDO support
- Password hashing dengan Argon2
- Named arguments ready
- Modern PHP features

**Auto-detection:** Sistem akan otomatis mendeteksi versi PHP dan menggunakan metode yang sesuai.

## 📖 Dokumentasi Lengkap

Untuk dokumentasi lebih detail, lihat:

- [DOKUMENTASI_CRUD.md](_DEV/DOKUMENTASI_CRUD.md) - Dokumentasi lengkap semua method CRUD
- [DOKUMENTASI_ENV.md](_DEV/DOKUMENTASI_ENV.md) - Environment configuration lengkap
- [DOKUMENTASI_MIDDLEWARE.md](_DEV/DOKUMENTASI_MIDDLEWARE.md) - Middleware system
- [DOKUMENTASI_SECURITY.md](_DEV/DOKUMENTASI_SECURITY.md) - Security implementation
- [SECURITY_FEATURES.md](_DEV/SECURITY_FEATURES.md) - 10 Layer security features
- [CONTOH_IMPLEMENTASI.md](_DEV/CONTOH_IMPLEMENTASI.md) - Contoh implementasi lengkap
- [PASSWORD_HASHING_FIX.md](_DEV/PASSWORD_HASHING_FIX.md) - Password hashing guide
- [ERROR_HANDLING_DOCUMENTATION.md](_DEV/ERROR_HANDLING_DOCUMENTATION.md) - Error handling

## 📝 Changelog

### Version 3.0 (Latest - SILAU)
- ✅ Integrasi sistem RFID untuk tracking laundry
- ✅ Dashboard & reporting untuk bisnis laundry
- ✅ Manajemen pelanggan dan transaksi
- ✅ Authentication system dengan role-based access
- ✅ 10 Layer security implementation
- ✅ Middleware system (Auth, Guest, Role)
- ✅ Advanced UI templates
- ✅ Session management & timeout handling
- ✅ Rate limiting untuk brute force protection
- ✅ Comprehensive documentation

### Version 2.1
- ✅ Environment configuration system (.env)
- ✅ Env class untuk load environment variables
- ✅ Semua config terpusat di .env
- ✅ Storage directory untuk logs & cache
- ✅ .gitignore untuk keamanan
- ✅ Dokumentasi lengkap environment config

## 📝 Changelog

### Version 2.0
- Kompatibilitas PHP 5.2 - 8+
- Auto-detect PDO/mysql_*
- Method CRUD lengkap dengan alias
- Query builder support
- Dokumentasi lengkap

### Version 1.0
- Basic MVC structure
- PDO only support

## 🎓 Teknologi yang Digunakan

- **Backend:** PHP (5.2 - 8+)
- **Database:** MySQL/MariaDB
- **Frontend:** HTML5, CSS3, JavaScript
- **UI Framework:** Bootstrap-based custom template
- **Architecture:** MVC Pattern
- **Security:** CSRF, XSS Protection, Rate Limiting, dll
- **Hardware:** RFID Reader Integration

## 📋 Requirement

- PHP 5.2+ / 7.x / 8.x
- MySQL 5.x / MariaDB
- Web Server (Apache/Nginx) atau PHP Built-in Server
- RFID Reader (untuk fitur RFID)

## 🚧 Roadmap

- [ ] API REST untuk mobile application
- [ ] Real-time notification dengan WebSocket
- [ ] Export laporan ke PDF/Excel
- [ ] Multi-branch support
- [ ] Payment gateway integration
- [ ] SMS/WhatsApp notification
- [ ] Customer mobile app

## 🤝 Contributing

Contributions are welcome! Silakan submit pull requests atau buat issue untuk bug report dan feature requests.

## 📄 License

MIT License - Feel free to use this project for your laundry business or learning purposes.

## 👨‍💻 Credits

- **Base Template:** [MVC-PHP-5-TEMPLATE](https://github.com/Yudhass/MVC-PHP-5-TEMPLATE.git)
- **Developed by:** SILAU Development Team
- **Inspired by:** Laravel, CodeIgniter

## 📞 Support

Jika Anda memiliki pertanyaan atau butuh bantuan:

- 📧 Email: support@silau.local
- 📖 Documentation: Lihat folder `_DEV/`
- 🐛 Issues: Create issue di repository

---

**Happy Laundry Management!** 🧺✨

