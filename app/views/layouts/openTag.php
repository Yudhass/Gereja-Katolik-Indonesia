<!doctype html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= BASEURL; ?>assets/images/logo/logo.webp" type="image/webp">
    <link rel="alternate icon" href="<?= BASEURL; ?>assets/images/logo/logo.png" type="image/png">
    <link href="<?= BASEURL; ?>assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASEURL; ?>assets/plugins/notifications/css/lobibox.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.17/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/css/pace.min.css" rel="stylesheet" />
    <script src="<?= BASEURL; ?>assets/js/pace.min.js"></script>
    <link href="<?= BASEURL; ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/css/bootstrap-extended.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/sass/app.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/css/icons.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/css/admin.css?v=20261124c" rel="stylesheet">
    <title>Info Gereja - <?= htmlspecialchars($title); ?></title>
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
