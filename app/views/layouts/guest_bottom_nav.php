<?php $activeMenu = isset($activeMenu) ? $activeMenu : ''; ?>
<nav class="gki-bottom-nav d-md-none" aria-label="Navigasi bawah">
    <div class="gki-bottom-nav-inner">
        <a href="<?= BASEURL; ?>" <?= $activeMenu === 'home' ? 'aria-current="page"' : ''; ?>>
            <i class="bx bx-home" aria-hidden="true"></i>
            <span>Beranda</span>
        </a>
        <a href="<?= BASEURL; ?>cari" <?= $activeMenu === 'cari' ? 'aria-current="page"' : ''; ?>>
            <i class="bx bx-search" aria-hidden="true"></i>
            <span>Cari</span>
        </a>
        <a href="<?= BASEURL; ?>jadwal" <?= $activeMenu === 'jadwal' ? 'aria-current="page"' : ''; ?>>
            <i class="bx bx-calendar" aria-hidden="true"></i>
            <span>Jadwal</span>
        </a>
        <a href="<?= BASEURL; ?>maps" <?= $activeMenu === 'maps' ? 'aria-current="page"' : ''; ?>>
            <i class="bx bx-map" aria-hidden="true"></i>
            <span>Peta</span>
        </a>
    </div>
</nav>
