<nav class="navbar fixed-bottom navbar-light bg-white d-md-none shadow-lg border-top" style="z-index: 1050;">
    <div class="container-fluid d-flex justify-content-around py-0" style="min-height:52px;">
        <a href="<?= BASEURL; ?>" class="text-center text-decoration-none d-flex flex-column align-items-center justify-content-center <?= (isset($activeMenu) && $activeMenu == 'home') ? 'text-accent-church fw-bold' : 'text-secondary' ?>" style="min-width:50px;">
            <i class="bx bx-home" style="font-size: 1.5rem;"></i><small style="font-size: 0.6rem; line-height:1; display:block;">Beranda</small>
        </a>
        <a href="<?= BASEURL; ?>cari" class="text-center text-decoration-none d-flex flex-column align-items-center justify-content-center <?= (isset($activeMenu) && $activeMenu == 'cari') ? 'text-accent-church fw-bold' : 'text-secondary' ?>" style="min-width:50px;">
            <i class="bx bx-search" style="font-size: 1.5rem;"></i><small style="font-size: 0.6rem; line-height:1; display:block;">Cari</small>
        </a>
        <a href="<?= BASEURL; ?>maps" class="text-center text-decoration-none d-flex flex-column align-items-center justify-content-center <?= (isset($activeMenu) && $activeMenu == 'maps') ? 'text-accent-church fw-bold' : 'text-secondary' ?>" style="min-width:50px;">
            <i class="bx bx-map" style="font-size: 1.5rem;"></i><small style="font-size: 0.6rem; line-height:1; display:block;">Maps</small>
        </a>
        <a href="<?= BASEURL; ?>jadwal" class="text-center text-decoration-none d-flex flex-column align-items-center justify-content-center <?= (isset($activeMenu) && $activeMenu == 'jadwal') ? 'text-accent-church fw-bold' : 'text-secondary' ?>" style="min-width:50px;">
            <i class="bx bx-calendar" style="font-size: 1.5rem;"></i><small style="font-size: 0.6rem; line-height:1; display:block;">Jadwal</small>
        </a>
    </div>
</nav>
