<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="<?= BASEURL; ?>assets/images/logo/logo.webp" type="image/webp">
    <link rel="alternate icon" href="<?= BASEURL; ?>assets/images/logo/logo.png" type="image/png">
    <!--plugins-->
    <link href="<?= BASEURL; ?>assets/plugins/vectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/plugins/simplebar/css/simplebar.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASEURL; ?>assets/plugins/notifications/css/lobibox.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.17/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- loader-->
    <link href="<?= BASEURL; ?>assets/css/pace.min.css" rel="stylesheet" />
    <script src="<?= BASEURL; ?>assets/js/pace.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="<?= BASEURL; ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/css/bootstrap-extended.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <link href="<?= BASEURL; ?>assets/sass/app.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/css/icons.css" rel="stylesheet">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css' rel='stylesheet'>
    <!-- Theme Style CSS -->
    <link rel="stylesheet" href="<?= BASEURL; ?>assets/sass/dark-theme.css">
    <link rel="stylesheet" href="<?= BASEURL; ?>assets/sass/semi-dark.css">
    <link rel="stylesheet" href="<?= BASEURL; ?>assets/sass/bordered-theme.css">
    <link href="<?= BASEURL; ?>assets/css/admin.css" rel="stylesheet">
    <title>Info Gereja - <?= $title; ?></title>
    <?php if (isset($css)): ?>
        <?php foreach ($css as $csss): ?>
            <link rel="stylesheet" href="<?= BASEURL; ?><?= $csss; ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (isset($css2)): ?>
        <?php foreach ($css2 as $csss): ?>
            <link rel="stylesheet" href="<?= $csss; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>