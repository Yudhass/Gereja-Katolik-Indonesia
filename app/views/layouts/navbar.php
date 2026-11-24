<header>
    <div class="topbar">
        <nav class="navbar navbar-expand align-items-center justify-content-between w-100">
            <button type="button" class="mobile-toggle-menu" aria-label="Buka menu navigasi">
                <i class="bx bx-menu" aria-hidden="true"></i>
            </button>
            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center gap-1"></ul>
            </div>
            <div class="user-box dropdown">
                <a class="user-trigger nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu akun admin">
                    <span class="user-avatar-icon" aria-hidden="true">
                        <i class="bx bx-user-circle"></i>
                    </span>
                    <div class="user-info d-none d-sm-block">
                        <p class="user-name mb-0"><?= htmlspecialchars(Auth()->nama); ?></p>
                        <p class="designation mb-0"><?= htmlspecialchars(ucfirst(Auth()->role_nama)); ?></p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item d-flex align-items-center gap-2" target="_blank" rel="noopener noreferrer" href="<?= BASEURL; ?>">
                        <i class="bx bx-home fs-5" aria-hidden="true"></i><span>Buka Website</span></a>
                    </li>
                    <li><div class="dropdown-divider my-1"></div></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="<?= BASEURL; ?>logout">
                        <i class="bx bx-log-out-circle fs-5" aria-hidden="true"></i><span>Logout</span></a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
