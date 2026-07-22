<?php

require_once dirname(__FILE__) . '/../core/Middleware.php';
require_once dirname(__FILE__) . '/../core/SessionManager.php';

/**
 * Auth Middleware
 * Cek apakah user sudah login DAN session-nya terdaftar di database.
 *
 * Alur validasi (dijalankan setiap request ke halaman terproteksi):
 *  1. Cek $_SESSION['user'] ada di browser cookie
 *  2. Validasi session_id() ke tabel user_sessions di database
 *     → Jika tidak ada record  → paksa logout
 *     → Jika record kadaluarsa  → paksa logout
 *  3. Cek timeout sisi PHP (SESSION_LIFETIME)
 *  4. Perbarui last_activity di DB (touch)
 *  5. Regenerate session ID secara berkala & sinkronkan ke DB
 */
class AuthMiddleware extends Middleware
{
    public function handle()
    {
        // --- 1. Cek session PHP ada ---
        if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            $this->redirect('login', 'Silakan login terlebih dahulu untuk mengakses halaman ini.', 'warning');
            return false;
        }

        $currentSessionId = session_id();

        // --- 2. Validasi session ke database ---
        if (!SessionManager::validate($currentSessionId)) {
            // Session tidak ditemukan / sudah kadaluarsa di sisi server
            // Hancurkan session PHP di browser dan paksa logout
            $message = 'Sesi Anda sudah berakhir atau tidak valid. Silakan login kembali.';
            $type = 'warning';

            $_SESSION = array();

            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }

            session_destroy();

            // Mulai session baru untuk menyimpan flash message
            session_start();
            $_SESSION['flash_message'] = $message;
            $_SESSION['flash_message_type'] = $type;

            header('Location: ' . BASEURL . 'login');
            exit;
            return false;
        }

        // --- 3. Cek timeout sisi PHP (double-check) ---
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > SESSION_LIFETIME) {
            SessionManager::destroy($currentSessionId);

            $message = 'Sesi habis. Silakan login kembali.';
            $type = 'error';

            session_destroy();

            // Mulai session baru untuk menyimpan flash message
            session_start();
            $_SESSION['flash_message'] = $message;
            $_SESSION['flash_message_type'] = $type;

            header('Location: ' . BASEURL . 'login');
            exit;
            return false;
        }

        // --- 4. Perbarui last_activity di DB dan PHP session ---
        $_SESSION['LAST_ACTIVITY'] = time();
        SessionManager::touch($currentSessionId);

        // --- 5. Regenerate session ID secara berkala ---
        if (!isset($_SESSION['LAST_REGENERATE'])) {
            $_SESSION['LAST_REGENERATE'] = time();
        }

        if ((time() - $_SESSION['LAST_REGENERATE']) > SESSION_REGENERATE_INTERVAL) {
            $oldSessionId = session_id();
            session_regenerate_id(true);
            $newSessionId = session_id();

            // Sinkronkan session_id baru ke database
            SessionManager::regenerate($oldSessionId, $newSessionId);

            $_SESSION['LAST_REGENERATE'] = time();
        }

        // --- 6. Probabilistic cleanup expired sessions (1% chance per request) ---
        if (mt_rand(1, 100) === 1) {
            SessionManager::cleanupExpired();
        }

        return true;
    }
}
