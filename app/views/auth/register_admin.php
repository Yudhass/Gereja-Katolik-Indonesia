<!doctype html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= BASEURL; ?>assets/images/logo/logo.webp" type="image/webp">
    <link rel="alternate icon" href="<?= BASEURL; ?>assets/images/logo/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/css/icons.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/plugins/notifications/css/lobibox.min.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/css/guest.css?v=20261124" rel="stylesheet">
    <title>Daftar Admin - Info Gereja</title>
    <style>
        body {
            background: var(--bg-canvas);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
        }
        .auth-shell {
            width: 100%;
            max-width: 430px;
        }
        .auth-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            padding: 2rem 1.75rem;
        }
        .auth-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .auth-logo-wrap {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: var(--primary-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.85rem;
        }
        .auth-logo-wrap img {
            width: 34px;
            height: 34px;
            object-fit: contain;
        }
        .auth-brand h1 {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--ink);
            letter-spacing: -0.02em;
            margin: 0 0 0.25rem;
        }
        .auth-brand p {
            font-size: 0.85rem;
            color: var(--ink-secondary);
            margin: 0;
        }
        .auth-password-wrap {
            position: relative;
        }
        .auth-password-wrap .gki-input {
            padding-right: 2.75rem;
        }
        .auth-password-toggle {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: var(--ink-muted);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .auth-password-toggle:hover {
            background: var(--surface-subtle);
            color: var(--ink);
        }
        .auth-back {
            text-align: center;
            margin-top: 1.25rem;
        }
        .auth-back a {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.84rem;
            font-weight: 600;
            color: var(--ink-secondary);
        }
        .auth-back a:hover {
            color: var(--primary);
        }
    </style>
</head>
<body>
    <main class="auth-shell">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="auth-logo-wrap">
                    <img src="<?= BASEURL; ?>assets/images/logo/logo.webp" alt="Logo Info Gereja" width="34" height="34">
                </div>
                <h1>Daftar Admin</h1>
                <p>Buat akun administrator pertama Info Gereja</p>
            </div>

            <form method="POST" action="<?= BASEURL; ?>register">
                <?= csrf_field(); ?>
                <div class="gki-field mb-3">
                    <label for="namaLengkap">Nama Lengkap</label>
                    <input type="text" id="namaLengkap" name="nama_lengkap" class="gki-input" placeholder="Contoh: Yohanes Pratama" required autofocus>
                </div>
                <div class="gki-field mb-3">
                    <label for="emailInput">Email</label>
                    <input type="email" id="emailInput" name="email" class="gki-input" placeholder="admin@infogereja.id" required>
                </div>
                <div class="gki-field mb-3">
                    <label for="passwordInput1">Password</label>
                    <div class="auth-password-wrap">
                        <input type="password" name="password" class="gki-input" placeholder="Minimal 6 karakter" minlength="6" id="passwordInput1" required>
                        <button type="button" class="auth-password-toggle" id="togglePassword1" aria-label="Tampilkan atau sembunyikan password">
                            <i class="bx bx-show" id="toggleIcon1" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="gki-field mb-4">
                    <label for="passwordInput2">Konfirmasi Password</label>
                    <div class="auth-password-wrap">
                        <input type="password" name="password_confirm" class="gki-input" placeholder="Ulangi password" minlength="6" id="passwordInput2" required>
                        <button type="button" class="auth-password-toggle" id="togglePassword2" aria-label="Tampilkan atau sembunyikan konfirmasi password">
                            <i class="bx bx-show" id="toggleIcon2" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="gki-btn gki-btn-primary w-100">
                    <i class="bx bx-check-circle" aria-hidden="true"></i>
                    <span>Buat Akun Admin</span>
                </button>
            </form>
        </div>

        <div class="auth-back">
            <a href="<?= BASEURL; ?>login">
                <i class="bx bx-log-in" aria-hidden="true"></i>Sudah punya akun? Masuk di sini
            </a>
        </div>
    </main>

    <script src="<?= BASEURL; ?>assets/js/jquery.min.js"></script>
    <script src="<?= BASEURL; ?>assets/plugins/notifications/js/lobibox.min.js"></script>
    <script src="<?= BASEURL; ?>assets/plugins/notifications/js/notifications.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#togglePassword1').on('click', function() {
                var input = $('#passwordInput1');
                var icon = $('#toggleIcon1');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('bx-show').addClass('bx-hide');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('bx-hide').addClass('bx-show');
                }
            });
            $('#togglePassword2').on('click', function() {
                var input = $('#passwordInput2');
                var icon = $('#toggleIcon2');
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
