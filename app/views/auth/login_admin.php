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
    <title>Login Admin - Info Gereja</title>
    <style>
        body { background: linear-gradient(135deg, #2C4463 0%, #1A2D47 100%); min-height: 100vh; display: flex; align-items: center; }
        .login-card { border: none; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); overflow: hidden; }
        .login-header { background: #2C4463; color: white; padding: 2rem 1.5rem; text-align: center; }
        .login-header h4 { font-weight: 700; color: #fff; }
        .login-body { padding: 2rem; }
        .form-control-custom { border-radius: 12px; border: 1px solid #ddd; padding: 0.7rem 1rem; font-size: 0.9rem; }
        .form-control-custom:focus { border-color: #2C4463; box-shadow: 0 0 0 0.2rem rgba(44,68,99,0.1); }
        .input-group .form-control-custom { border-radius: 12px 0 0 12px; }
        #togglePassword { border-color: #ddd; background: #fff; }
        #togglePassword:hover { background: #f0f0f0; }
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
                        <i class="bx bx-church" style="font-size: 3rem;"></i>
                        <h4 class="mt-2">Info Gereja</h4>
                        <p class="mb-0 small">Panel Admin</p>
                    </div>
                    <div class="login-body">
                        <form method="POST" action="<?= BASEURL; ?>login">
                            <?= csrf_field(); ?>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Email</label>
                                <input type="email" name="email" class="form-control form-control-custom" placeholder="admin@gereja.com" required autofocus>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold small">Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control form-control-custom" placeholder="Masukkan password" id="passwordInput" required>
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword" style="border-radius:0 12px 12px 0; border-color:#ddd;">
                                        <i class="bx bx-show" id="toggleIcon"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-login w-100"><i class="bx bx-log-in me-1"></i>Login</button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="<?= BASEURL; ?>" class="small text-decoration-none" style="color:#2C4463;">
                                <i class="bx bx-arrow-back me-1"></i>Kembali ke Beranda
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
            $('#togglePassword').on('click', function() {
                var input = $('#passwordInput');
                var icon = $('#toggleIcon');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('bx-show').addClass('bx-hide');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('bx-hide').addClass('bx-show');
                }
            });
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
