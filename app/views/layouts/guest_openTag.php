<?php
$seo = isset($seo) && is_array($seo) ? $seo : array();
$siteName = 'Info Gereja';
$defaultDesc = 'Direktori lengkap Gereja Katolik di Indonesia: alamat paroki, peta lokasi interaktif, dan jadwal misa harian maupun mingguan terbaru.';
$defaultImage = BASEURL . 'assets/images/logo/logo.png';

$seoTitle = !empty($seo['title']) ? $seo['title'] : (decode_text($title) . ' - ' . $siteName);
$seoDesc = !empty($seo['description']) ? $seo['description'] : $defaultDesc;
$seoKeywords = !empty($seo['keywords']) ? $seo['keywords'] : 'gereja katolik indonesia, jadwal misa, direktori gereja katolik, paroki katolik, peta gereja katolik, info gereja';
$seoRobots = !empty($seo['robots']) ? $seo['robots'] : 'index, follow';
$seoCanonical = !empty($seo['canonical']) ? $seo['canonical'] : BASEURL;
$seoOgType = !empty($seo['og_type']) ? $seo['og_type'] : 'website';
$seoImage = !empty($seo['image']) ? $seo['image'] : $defaultImage;
$seoImageAlt = !empty($seo['image_alt']) ? $seo['image_alt'] : $seoTitle;
$seoTwitterCard = !empty($seo['twitter_card']) ? $seo['twitter_card'] : (!empty($seo['image']) ? 'summary_large_image' : 'summary');
$seoPrev = !empty($seo['prev']) ? $seo['prev'] : '';
$seoNext = !empty($seo['next']) ? $seo['next'] : '';
$seoSchema = !empty($seo['schema']) && is_array($seo['schema']) ? $seo['schema'] : array();
?>
<!doctype html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= e_text($seoTitle); ?></title>
    <meta name="description" content="<?= e_text($seoDesc); ?>">
    <meta name="keywords" content="<?= e_text($seoKeywords); ?>">
    <meta name="robots" content="<?= htmlspecialchars($seoRobots, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="author" content="<?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="theme-color" content="#14533C">
    <link rel="canonical" href="<?= htmlspecialchars($seoCanonical, ENT_QUOTES, 'UTF-8'); ?>">
    <?php if ($seoPrev): ?>
    <link rel="prev" href="<?= htmlspecialchars($seoPrev, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>
    <?php if ($seoNext): ?>
    <link rel="next" href="<?= htmlspecialchars($seoNext, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>

    <!-- Open Graph -->
    <meta property="og:site_name" content="<?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:locale" content="id_ID">
    <meta property="og:type" content="<?= htmlspecialchars($seoOgType, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:title" content="<?= e_text($seoTitle); ?>">
    <meta property="og:description" content="<?= e_text($seoDesc); ?>">
    <meta property="og:url" content="<?= htmlspecialchars($seoCanonical, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:image" content="<?= htmlspecialchars($seoImage, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:image:alt" content="<?= e_text($seoImageAlt); ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="<?= htmlspecialchars($seoTwitterCard, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:title" content="<?= e_text($seoTitle); ?>">
    <meta name="twitter:description" content="<?= e_text($seoDesc); ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($seoImage, ENT_QUOTES, 'UTF-8'); ?>">

    <link rel="icon" href="<?= BASEURL; ?>assets/images/logo/logo.webp" type="image/webp">
    <link rel="alternate icon" href="<?= BASEURL; ?>assets/images/logo/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/css/icons.css" rel="stylesheet">
    <link href="<?= BASEURL; ?>assets/css/guest.css?v=20261124" rel="stylesheet">
    <?php if (isset($css)): foreach ($css as $csss): ?>
        <link rel="stylesheet" href="<?= (strpos($csss, 'http://') === 0 || strpos($csss, 'https://') === 0) ? $csss : (BASEURL . $csss); ?>">
    <?php endforeach; endif; ?>
    <?php foreach ($seoSchema as $schemaItem): ?>
    <script type="application/ld+json"><?= json_encode($schemaItem, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
    <?php endforeach; ?>
</head>
<body<?= !empty($bodyClass) ? ' class="' . htmlspecialchars($bodyClass) . '"' : ''; ?>>
