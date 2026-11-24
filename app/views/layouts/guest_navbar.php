<?php $activeMenu = isset($activeMenu) ? $activeMenu : ''; ?>
<nav class="gki-navbar" aria-label="Navigasi utama">
    <div class="gki-container gki-navbar-inner">
        <a href="<?= BASEURL; ?>" class="gki-brand">
            <img src="<?= BASEURL; ?>assets/images/logo/logo.webp" alt="Logo Info Gereja" class="gki-brand-logo" width="30" height="30">
            <span>Info Gereja</span>
        </a>

        <div class="gki-nav-links">
            <a href="<?= BASEURL; ?>" <?= $activeMenu === 'home' ? 'aria-current="page"' : ''; ?>>
                <i class="bx bx-home" aria-hidden="true"></i>Beranda
            </a>
            <a href="<?= BASEURL; ?>cari" <?= $activeMenu === 'cari' ? 'aria-current="page"' : ''; ?>>
                <i class="bx bx-search" aria-hidden="true"></i>Cari Gereja
            </a>
            <a href="<?= BASEURL; ?>jadwal" <?= $activeMenu === 'jadwal' ? 'aria-current="page"' : ''; ?>>
                <i class="bx bx-calendar" aria-hidden="true"></i>Jadwal Misa
            </a>
            <a href="<?= BASEURL; ?>maps" <?= $activeMenu === 'maps' ? 'aria-current="page"' : ''; ?>>
                <i class="bx bx-map" aria-hidden="true"></i>Peta Gereja
            </a>
        </div>

        <div class="gki-nav-actions">
            <?php if (!empty($mobileActions)): ?>
                <?= $mobileActions; ?>
            <?php else: ?>
                <a href="<?= BASEURL; ?>maps" class="gki-btn gki-btn-outline gki-btn-sm d-md-none" aria-label="Buka Peta Gereja">
                    <i class="bx bx-map" aria-hidden="true"></i>
                    <span>Peta</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>
