<?php
require_once dirname(__FILE__) . '/../models/ModelAdmin.php';

class AuthController extends Controller
{
    public function index()
    {
        return redirect('login');
    }

    public function login()
    {
        if (Auth()) {
            return redirect('admin/dashboard');
        }

        $modelAdmin = new ModelAdmin();
        $admins = $modelAdmin->selectAll();

        if (!$admins || count($admins) === 0) {
            return $this->redirect('register', 'Belum ada admin terdaftar. Silakan daftar sebagai admin pertama.', 'info');
        }

        $data = array('title' => 'Login Admin');
        $this->view('auth/login_admin', $data);
    }

    public function login_process()
    {
        if (CSRF_ENABLED && !verify_csrf()) {
            return $this->redirectBack('Token CSRF tidak valid. Silakan coba lagi.', 'error');
        }

        $email = isset($_POST['email']) ? sanitize($_POST['email'], 'email') : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if (empty($email)) {
            return $this->redirectBack('Email wajib diisi.', 'error');
        }

        if (empty($password)) {
            return $this->redirectBack('Password wajib diisi.', 'error');
        }

        $modelAdmin = new ModelAdmin();
        $adminList = $modelAdmin->selectWhere('email', $email);
        $admin = ($adminList && count($adminList) > 0) ? $adminList[0] : null;

        if ($admin && verify_password($password, $admin->password_hash)) {
            session_regenerate_id(true);

            $_SESSION['user'] = array(
                'id' => $admin->id,
                'nama' => $admin->nama_lengkap,
                'email' => $admin->email,
                'username' => $admin->email,
                'role_nama' => 'admin'
            );
            $_SESSION['LAST_ACTIVITY'] = time();

            SessionManager::create($_SESSION['user'], session_id());

            return $this->redirect('admin/dashboard', 'Login berhasil. Selamat datang, ' . htmlspecialchars($admin->nama_lengkap) . '!', 'success');
        } else {
            return $this->redirectBack('Email atau password tidak valid.', 'error');
        }
    }

    public function register()
    {
        if (Auth()) {
            return redirect('admin/dashboard');
        }

        $modelAdmin = new ModelAdmin();
        $admins = $modelAdmin->selectAll();

        if ($admins && count($admins) > 0) {
            return $this->redirect('login', 'Admin sudah terdaftar. Silakan login.', 'info');
        }

        $data = array('title' => 'Daftar Admin');
        $this->view('auth/register_admin', $data);
    }

    public function register_process()
    {
        if (CSRF_ENABLED && !verify_csrf()) {
            return $this->redirectBack('Token CSRF tidak valid. Silakan coba lagi.', 'error');
        }

        $nama = isset($_POST['nama_lengkap']) ? trim($_POST['nama_lengkap']) : '';
        $email = isset($_POST['email']) ? sanitize($_POST['email'], 'email') : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

        if (empty($nama)) {
            return $this->redirectBack('Nama lengkap wajib diisi.', 'error');
        }

        if (empty($email)) {
            return $this->redirectBack('Email wajib diisi.', 'error');
        }

        if (empty($password) || strlen($password) < 6) {
            return $this->redirectBack('Password minimal 6 karakter.', 'error');
        }

        if ($password !== $confirm) {
            return $this->redirectBack('Konfirmasi password tidak cocok.', 'error');
        }

        $modelAdmin = new ModelAdmin();
        $existing = $modelAdmin->selectWhere('email', $email);

        if ($existing && count($existing) > 0) {
            return $this->redirectBack('Email sudah terdaftar.', 'error');
        }

        $data = array(
            'nama_lengkap' => $nama,
            'email' => $email,
            'password_hash' => Security::hashPassword($password),
            'created_at' => date('Y-m-d H:i:s')
        );

        $result = $modelAdmin->insert($data);

        if ($result) {
            return $this->redirect('login', 'Registrasi berhasil. Silakan login.', 'success');
        } else {
            return $this->redirectBack('Gagal mendaftarkan admin. Silakan coba lagi.', 'error');
        }
    }

    public function logout()
    {
        $_SESSION = array();
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();

        return $this->redirect('login', 'Anda telah berhasil logout.', 'success');
    }
}
