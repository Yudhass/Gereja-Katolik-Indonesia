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
    <link href="<?= BASEURL; ?>assets/sass/app.css" rel="stylesheet">    
    <title><?= $title; ?> - Info Gereja</title>
    <style>
        :root {
            --primary: #2C4463;
            --primary-light: #7FA8C9;
            --primary-dark: #1A2D47;
            --accent: #CFA969;
            --bg-light: #F2F5F8;
        }
        body { background: var(--bg-light); font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
        .text-primary-church { color: var(--primary) !important; }
        .text-accent-church { color: var(--accent) !important; }
        .bg-primary-church { background: var(--primary) !important; }
        .btn-primary-church { background: var(--primary); color: #fff; border: none; }
        .btn-primary-church:hover { background: var(--primary-dark); color: #fff; }
        .btn-outline-church { border: 2px solid var(--primary); color: var(--primary); background: transparent; }
        .btn-outline-church:hover { background: var(--primary); color: #fff; }
        @media (max-width: 767px) {
            body { padding-bottom: 76px; }
        }
    </style>
    <?php if (isset($css)): foreach ($css as $csss): ?>
        <link rel="stylesheet" href="<?= (strpos($csss, 'http://') === 0 || strpos($csss, 'https://') === 0) ? $csss : (BASEURL . $csss); ?>">
    <?php endforeach; endif; ?>
</head>
<body>
