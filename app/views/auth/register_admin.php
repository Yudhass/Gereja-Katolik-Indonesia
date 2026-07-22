<!doctype html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= BASEURL; ?>assets/images/logo/logo.webp" type="image/webp">
    <link rel="alternate icon" href="<?= BASEURL; ?>assets/images/logo/logo.png" type="image/png">
    <link href="<?= BASEURL; ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/css/bootstrap-extended.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/css/icons.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/plugins/notifications/css/lobibox.min.css" rel="stylesheet" />
    <title>Daftar Admin - Info Gereja</title>
    <style>
        body { background: linear-gradient(135deg, #2C4463 0%, #1A2D47 100%); min-height: 100vh; display: flex; align-items: center; }
        .login-card { border: none; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); overflow: hidden; }
        .login-header { background: #2C4463; color: white; padding: 2rem 1.5rem; text-align: center; }
        .login-header h4 { font-weight: 700; color: #fff; }
        .login-body { padding: 2rem; }
        .form-control-custom { border-radius: 12px; border: 1px solid #ddd; padding: 0.7rem 1rem; font-size: 0.9rem; }
        .form-control-custom:focus { border-color: #2C4463; box-shadow: 0 0 0 0.2rem rgba(44,68,99,0.1); }
        .btn-login { background: #2C4463; color: white; border: none; border-radius: 12px; padding: 0.7rem; font-weight: 600; font-size: 1rem; }
        .btn-login:hover { background: #1A2D47; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="login-header">
                        <i class="bx bx-user-plus" style="font-size: 3rem;"></i>
                        <h4 class="mt-2">Daftar Admin</h4>
                        <p class="mb-0 small">Buat admin pertama</p>
                    </div>
                    <div class="login-body">
                        <form method="POST" action="<?= BASEURL; ?>register">
                            <?= csrf_field(); ?>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control form-control-custom" placeholder="Nama lengkap" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Email</label>
                                <input type="email" name="email" class="form-control form-control-custom" placeholder="admin@gereja.com" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Password</label>
                                <input type="password" name="password" class="form-control form-control-custom" placeholder="Minimal 6 karakter" minlength="6" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold small">Konfirmasi Password</label>
                                <input type="password" name="password_confirm" class="form-control form-control-custom" placeholder="Ulangi password" minlength="6" required>
                            </div>
                            <button type="submit" class="btn btn-login w-100"><i class="bx bx-check-circle me-1"></i>Daftar</button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="<?= BASEURL; ?>login" class="small text-decoration-none" style="color:#2C4463;">
                                <i class="bx bx-log-in me-1"></i>Sudah punya akun? Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?= BASEURL; ?>assets/js/jquery.min.js"></script>
    <script src="<?= BASEURL; ?>assets/plugins/notifications/js/lobibox.min.js"></script>
    <script src="<?= BASEURL; ?>assets/plugins/notifications/js/notifications.min.js"></script>
    <script>
        $(document).ready(function() {
            <?php
            $flash = getFlashMessage();
            if ($flash):
                $alertType = $flash['type'] == 'error' ? 'error' : ($flash['type'] == 'warning' ? 'warning' : ($flash['type'] == 'info' ? 'info' : 'success'));
                $icon = $flash['type'] == 'error' ? 'bx bx-x-circle' : ($flash['type'] == 'warning' ? 'bx bx-error' : ($flash['type'] == 'info' ? 'bx bx-info-circle' : 'bx bx-check-circle'));
            ?>
            Lobibox.notify('<?= $alertType; ?>', {
                pauseDelayOnHover: true,
                continueDelayOnInactiveTab: false,
                position: 'center top',
                size: 'mini',
                icon: '<?= $icon; ?>',
                msg: '<?= htmlspecialchars($flash['message'], ENT_QUOTES); ?>',
                sound: false
            });
            <?php endif; ?>
        });
    </script>
</body>
</html>
