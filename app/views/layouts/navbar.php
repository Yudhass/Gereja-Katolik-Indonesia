<header>
    <div class="topbar">
        <nav class="navbar navbar-expand gap-2 align-items-center">
            <div class="mobile-toggle-menu d-flex"><i class='bx bx-menu'></i></div>
            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center gap-1"></ul>
            </div>
            <div class="user-box dropdown px-3">
                <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bx bx-user-circle" style="font-size:2rem; color:#2C4463;"></i>
                    <div class="user-info">
                        <p class="user-name mb-0"><?= Auth()->nama; ?></p>
                        <p class="designation mb-0"><?= ucfirst(Auth()->role_nama); ?></p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item d-flex align-items-center" target="__blank" href="<?= BASEURL; ?>">
                        <i class="bx bx-home fs-5"></i><span>Buka Website</span></a>
                    </li>
                    <li><div class="dropdown-divider mb-0"></div></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="<?= BASEURL; ?>logout">
                        <i class="bx bx-log-out-circle"></i><span>Logout</span></a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
