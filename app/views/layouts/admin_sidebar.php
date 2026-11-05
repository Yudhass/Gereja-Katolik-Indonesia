<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div class="d-flex align-items-center gap-2">
            <img src="<?= BASEURL; ?>assets/images/logo/logo.webp" class="logo-icon" alt="logo" style="max-height:2.25rem; width:auto;">
            <span class="logo-text mb-0" style="color:#ffffff !important;-webkit-text-fill-color:#ffffff !important;font-size:1.25rem;font-weight:700;line-height:1;display:inline-block;">Info Gereja</span>
        </div>
        <div class="mobile-toggle-icon ms-auto" style="color:#ffffff !important;"><i class='bx bx-x'></i></div>
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
<div class="overlay"></div>