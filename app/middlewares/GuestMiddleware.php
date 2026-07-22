<?php

require_once dirname(__FILE__) . '/../core/Middleware.php';

/**
 * Guest Middleware
 * Cek apakah user belum login (untuk halaman login/register)
 */
class GuestMiddleware extends Middleware
{
    public function handle()
    {
        // Jika user sudah login, redirect ke home
        if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
            $this->redirect('home', 'Anda sudah login.', 'info');
            return false;
        }
        
        return true;
    }
}
