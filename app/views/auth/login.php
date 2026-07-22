<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="<?= BASEURL; ?>assets/images/logo.png" type="image/png">
    <!--plugins-->
    <link href="<?= BASEURL; ?>assets/plugins/simplebar/css/simplebar.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet">
    <!-- loader-->
    <link href="<?= BASEURL; ?>assets/css/pace.min.css" rel="stylesheet">
    <script src="<?= BASEURL; ?>assets/js/pace.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="<?= BASEURL; ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/css/bootstrap-extended.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/sass/app.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASEURL; ?>assets/sass/dark-theme.css">
    <link href="<?= BASEURL; ?>assets/css/icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASEURL; ?>assets/plugins/notifications/css/lobibox.min.css" />
    <title>Login App - System Laundry</title>
</head>

<body class="">
    <!--wrapper-->
    <div class="wrapper">
        <div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-0">
            <div class="container">
                <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
                    <div class="col mx-auto">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="p-4">
                                    <div class="mb-3 text-center">
                                        <img src="<?= BASEURL; ?>assets/images/logo.png" width="150" alt="" />
                                    </div>
                                    <div class="text-center mb-4">
                                        <h5 class="">System Laundry</h5>
                                        <p class="mb-0">Silahkan Login Menggunakan Akun Anda</p>
                                    </div>
                                    <div class="form-body">
                                        <form class="row g-3" method="POST" action="<?= BASEURL; ?>login">
                                            <?= csrf_field(); ?>
                                            <div class="col-12">
                                                <label for="username" class="form-label">Username</label>
                                                <input type="text" name="username" class="form-control" id="username" placeholder="Masukkan Username">
                                            </div>
                                            <div class="col-12">
                                                <label for="password" class="form-label">Password</label>
                                                <div class="input-group" id="show_hide_password">
                                                    <input type="password" name="password" class="form-control border-end-0" id="password" value="" placeholder="Masukkan Password"> <a href="javascript:;" class="input-group-text bg-transparent"><i class='bx bx-hide'></i></a>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-success">Login</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end row-->
            </div>
        </div>
    </div>
    <!--end wrapper-->
    <!-- Bootstrap JS -->
    <script src="<?= BASEURL; ?>assets/js/bootstrap.bundle.min.js"></script>
    <!--plugins-->
    <script src="<?= BASEURL; ?>assets/js/jquery.min.js"></script>
    <script src="<?= BASEURL; ?>assets/plugins/simplebar/js/simplebar.min.js"></script>
    <script src="<?= BASEURL; ?>assets/plugins/metismenu/js/metisMenu.min.js"></script>
    <script src="<?= BASEURL; ?>assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
    <script src="<?= BASEURL; ?>assets/plugins/notifications/js/lobibox.min.js"></script>
    <script src="<?= BASEURL; ?>assets/plugins/notifications/js/notifications.min.js"></script>
    <!--Password show & hide js -->
    <script>
        $(document).ready(function() {
            $("#show_hide_password a").on('click', function(event) {
                event.preventDefault();
                if ($('#show_hide_password input').attr("type") == "text") {
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass("bx-hide");
                    $('#show_hide_password i').removeClass("bx-show");
                } else if ($('#show_hide_password input').attr("type") == "password") {
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass("bx-hide");
                    $('#show_hide_password i').addClass("bx-show");
                }
            });

            <?php
            $flash = getFlashMessage();
            if ($flash):
                $alertType = 'default';
                $icon = 'bx bx-info-circle';

                // Map flash type to Lobibox type
                if ($flash['type'] == 'success') {
                    $alertType = 'success';
                    $icon = 'bx bx-check-circle';
                } elseif ($flash['type'] == 'error') {
                    $alertType = 'error';
                    $icon = 'bx bx-x-circle';
                } elseif ($flash['type'] == 'warning') {
                    $alertType = 'warning';
                    $icon = 'bx bx-error';
                } elseif ($flash['type'] == 'info') {
                    $alertType = 'info';
                    $icon = 'bx bx-info-circle';
                }
            ?>
                Lobibox.notify('<?php echo $alertType; ?>', {
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'center top',
                    size: 'mini',
                    icon: '<?php echo $icon; ?>',
                    msg: '<?php echo htmlspecialchars($flash['message'], ENT_QUOTES); ?>',
                    sound: false // Disable sound notification
                });
            <?php endif; ?>
        });
    </script>
    <!--app JS-->
    <script src="<?= BASEURL; ?>assets/js/app.js"></script>
</body>

</html>