<nav class="navbar fixed-bottom navbar-light bg-white d-md-none shadow-lg border-top" style="z-index: 1050;">
    <div class="container-fluid d-flex justify-content-around py-2">
        <a href="<?= BASEURL; ?>" class="text-center text-decoration-none <?= (isset($activeMenu) && $activeMenu == 'home') ? 'text-accent-church fw-bold' : 'text-secondary' ?>" style="min-width:60px; padding:4px 0;">
            <i class="bx bx-home" style="font-size: 1.6rem;"></i><br><small style="font-size: 0.75rem; line-height:1.2;">Beranda</small>
        </a>
        <a href="<?= BASEURL; ?>cari" class="text-center text-decoration-none <?= (isset($activeMenu) && $activeMenu == 'cari') ? 'text-accent-church fw-bold' : 'text-secondary' ?>" style="min-width:60px; padding:4px 0;">
            <i class="bx bx-search" style="font-size: 1.6rem;"></i><br><small style="font-size: 0.75rem; line-height:1.2;">Cari</small>
        </a>
        <a href="<?= BASEURL; ?>jadwal" class="text-center text-decoration-none <?= (isset($activeMenu) && $activeMenu == 'jadwal') ? 'text-accent-church fw-bold' : 'text-secondary' ?>" style="min-width:60px; padding:4px 0;">
            <i class="bx bx-calendar" style="font-size: 1.6rem;"></i><br><small style="font-size: 0.75rem; line-height:1.2;">Jadwal</small>
        </a>
    </div>
</nav>
