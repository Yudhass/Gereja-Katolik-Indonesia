<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div><img src="<?= BASEURL; ?>assets/images/logo/logo.webp" class="logo-icon" alt="logo" style="max-height:2.5rem; width:auto;"></div>
        <div><h4 class="logo-text">Info Gereja</h4></div>
        <div class="mobile-toggle-icon ms-auto"><i class='bx bx-x'></i></div>
    </div>
    <ul class="metismenu" id="menu">
        <li class="<?= isset($sidebar) && $sidebar == 'dashboard' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>admin/dashboard">
                <div class="parent-icon"><i class="bx bx-home"></i></div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>

        <li class="menu-label">MASTER DATA</li>

        <li class="<?= isset($sidebar) && $sidebar == 'gereja' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>admin/gereja">
                <div class="parent-icon"><i class="bx bx-church"></i></div>
                <div class="menu-title">Data Gereja</div>
            </a>
        </li>

        <li class="<?= isset($sidebar) && $sidebar == 'jadwal' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>admin/jadwal">
                <div class="parent-icon"><i class="bx bx-calendar-event"></i></div>
                <div class="menu-title">Jadwal Misa</div>
            </a>
        </li>

        <li class="<?= isset($sidebar) && $sidebar == 'saran' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>admin/saran">
                <div class="parent-icon"><i class="bx bx-message-dots"></i></div>
                <div class="menu-title">Kotak Saran</div>
            </a>
        </li>
    </ul>
</div>
