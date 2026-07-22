<nav class="navbar fixed-bottom navbar-light bg-white d-md-none shadow-lg border-top" style="z-index: 1050;">
    <div class="container-fluid d-flex justify-content-around py-1">
        <a href="<?= BASEURL; ?>" class="text-center text-decoration-none <?= (isset($activeMenu) && $activeMenu == 'home') ? 'text-accent-church fw-bold' : 'text-secondary' ?>" style="min-width:50px; padding:2px 0;">
            <i class="bx bx-home" style="font-size: 1.3rem;"></i><br><small style="font-size: 0.65rem; line-height:1.1;">Beranda</small>
        </a>
        <a href="<?= BASEURL; ?>cari" class="text-center text-decoration-none <?= (isset($activeMenu) && $activeMenu == 'cari') ? 'text-accent-church fw-bold' : 'text-secondary' ?>" style="min-width:50px; padding:2px 0;">
            <i class="bx bx-search" style="font-size: 1.3rem;"></i><br><small style="font-size: 0.65rem; line-height:1.1;">Cari</small>
        </a>
        <a href="<?= BASEURL; ?>jadwal" class="text-center text-decoration-none <?= (isset($activeMenu) && $activeMenu == 'jadwal') ? 'text-accent-church fw-bold' : 'text-secondary' ?>" style="min-width:50px; padding:2px 0;">
            <i class="bx bx-calendar" style="font-size: 1.3rem;"></i><br><small style="font-size: 0.65rem; line-height:1.1;">Jadwal</small>
        </a>
    </div>
</nav>
