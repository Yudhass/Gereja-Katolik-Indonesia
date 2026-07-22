<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="<?= BASEURL; ?>assets/images/logo.png" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text">SILAU</h4>
        </div>
        <div class="mobile-toggle-icon ms-auto"><i class='bx bx-x'></i>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li class="<?= isset($sidebar) && $sidebar == 'dashboard' ? 'mm-active' : ''; ?>">
            <?php if (Auth()->role_nama == 'admin'): ?>
                <a href="<?= BASEURL; ?>admin/dashboard">
                    <div class="parent-icon"><i class="fa-solid fa-house"></i>
                    </div>
                    <div class="menu-title">Dashboard</div>
                </a>
            <?php else: ?>
                <a href="<?= BASEURL; ?>petugas/dashboard">
                    <div class="parent-icon"><i class="fa-solid fa-house"></i>
                    </div>
                    <div class="menu-title">Dashboard</div>
                </a>
            <?php endif ?>
        </li>

        <li class="menu-label">PASIEN</li>
        <li class="<?= isset($sidebar) && $sidebar == 'data_pasien_ranap' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>pasien/ranap">
                <div class="parent-icon"><i class="bx bx-user"></i>
                </div>
                <div class="menu-title">Data Pasien RANAP</div>
            </a>
        </li>
        <?php if (CekAkses(Auth()->role, 'penyerahan_linen_bersih_ke_pasien')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'penyerahan_linen_bersih_ke_pasien' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>pasien/penyerahan-linen-bersih-pasien">
                    <div class="parent-icon">
                        <i class="bx bx-export"></i>
                    </div>
                    <div class="menu-title">Penyerahan Linen Bersih ke Pasien</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'penerimaan_linen_kotor_dari_pasien')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'penerimaan_linen_kotor_pasien' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>pasien/penerimaan-linen-kotor-pasien">
                    <div class="parent-icon"><i class="bx bx-import"></i>
                    </div>
                    <div class="menu-title">Pengembalian Linen Kotor Pasien</div>
                </a>
            </li>
        <?php endif ?>

        <?php if (CekAkses(Auth()->role, 'cuci_linen_kotor')): ?>
            <li class="menu-label">RUANG CUCI</li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'cuci_linen_kotor')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'cuci_linen_kotor' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>linen/cuci-linen-kotor">
                    <div class="parent-icon">
                        <i class="bx bx-export"></i>
                    </div>
                    <div class="menu-title">Cuci Linen Kotor</div>
                </a>
            </li>
        <?php endif ?>

        <?php if (CekAkses(Auth()->role, 'setrika_linen_bersih')): ?>
            <li class="menu-label">RUANG SETRIKA</li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'setrika_linen_bersih')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'setrika_linen_bersih' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>linen/setrika-linen-bersih">
                    <div class="parent-icon">
                        <i class="bx bx-export"></i>
                    </div>
                    <div class="menu-title">Setrika Linen Bersih</div>
                </a>
            </li>
        <?php endif ?>

        <?php if (
            CekAkses(Auth()->role, 'penerimaan_linen_bersih_dari_laundry') ||
            CekAkses(Auth()->role, 'show_linen_bersih_di_unit') ||
            CekAkses(Auth()->role, 'show_pengguna_aktif_linen_pasien') ||
            CekAkses(Auth()->role, 'show_linen_kotor_di_unit') ||
            CekAkses(Auth()->role, 'penyerahan_linen_kotor_dari_ranap')
        ): ?>
            <li class="menu-label">RUANG UNIT</li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'penerimaan_linen_bersih_dari_laundry')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'penerimaan_linen_bersih_dari_laundry' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>distribusi/penerimaan-linen-bersih-dari-laundry">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/terima_kain.webp" alt="terima kain icon" class="icon-img">
                    </div>
                    <div class="menu-title">Penerimaan Linen Bersih</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'show_linen_bersih_di_unit')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'show_linen_bersih_di_unit' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>linen/daftar-linen-bersih-di-unit">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/bersih.webp" alt="bersih icon" class="icon-img">
                    </div>
                    <div class="menu-title">Daftar Linen Bersih di Unit</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'show_pengguna_aktif_linen_pasien')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'show_pengguna_aktif_linen_pasien' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>linen/daftar-linen-digunakan-pasien">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/linen_pasien.webp" alt="linen icon" class="icon-img">
                    </div>
                    <div class="menu-title">Daftar Pengguna Aktif Linen Pasien</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'show_linen_kotor_di_unit')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'show_linen_kotor_di_unit' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>linen/daftar-linen-kotor-di-unit">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/kotor.webp" alt="kotor icon" class="icon-img">
                    </div>
                    <div class="menu-title">Daftar Linen Kotor di Unit</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'penyerahan_linen_kotor_dari_ranap')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'penyerahan_linen_kotor_dari_ranap' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>distribusi/penyerahan-linen-kotor-dari-ranap">
                    <div class="parent-icon">
                        <i class="bx bx-export"></i>
                    </div>
                    <div class="menu-title">Penyerahan Linen Kotor dari Ranap</div>
                </a>
            </li>
        <?php endif ?>


        <!-- Ruangan Laundry -->
        <?php if (
            CekAkses(Auth()->role, 'penyerahan_linen_bersih_dari_laundry') ||
            CekAkses(Auth()->role, 'penerimaan_linen_kotor_dari_unit') ||
            CekAkses(Auth()->role, 'show_linen_bersih_di_laundry') ||
            CekAkses(Auth()->role, 'show_linen_kotor_di_laundry') ||
            CekAkses(Auth()->role, 'show_linen_yang_di_cuci')
        ): ?>
            <li class="menu-label">RUANG LAUNDRY</li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'penyerahan_linen_bersih_dari_laundry')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'penyerahan_linen_bersih_dari_laundry' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>distribusi/penyerahan-linen-bersih-dari-laundry">
                    <div class="parent-icon">
                        <i class="bx bx-export"></i>
                    </div>
                    <div class="menu-title">Penyerahan Linen Bersih dari Laundry</div>
                </a>
            </li>
        <?php endif; ?>
        <?php if (CekAkses(Auth()->role, 'penerimaan_linen_kotor_dari_unit')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'penerimaan_linen_kotor_dari_unit' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>distribusi/penerimaan-linen-kotor-dari-unit">
                    <div class="parent-icon">
                        <i class="bx bx-import"></i>
                    </div>
                    <div class="menu-title">Penerimaan Linen Kotor dari Unit</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'show_linen_bersih_di_laundry')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'show_linen_bersih_di_laundry' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>linen/daftar-linen-bersih-di-laundry">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/bersih.webp" alt="bersih icon" class="icon-img">
                    </div>
                    <div class="menu-title">Daftar Linen Bersih di Laundry</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'show_linen_kotor_di_laundry')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'show_linen_kotor_di_laundry' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>linen/daftar-linen-kotor-di-laundry">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/linen_kotor.webp" alt="linen kotor icon" class="icon-img">
                    </div>
                    <div class="menu-title">Daftar Linen Kotor di Laundry</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'show_linen_yang_di_cuci')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'show_linen_yang_di_cuci' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>show-linen-yang-di-cuci">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/dicuci.webp" alt="linen dicuci icon" class="icon-img">
                    </div>
                    <div class="menu-title">Daftar Linen yang di Cuci</div>
                </a>
            </li>
        <?php endif ?>

        <!-- History -->
        <?php if (
            CekAkses(Auth()->role, 'show_history_linen') ||
            CekAkses(Auth()->role, 'show_history_scan_linen') ||
            CekAkses(Auth()->role, 'show_history_distribusi')
        ): ?>
            <li class="menu-label">HISTORY</li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'show_history_linen')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'history_linen' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>history/linen">
                    <div class="parent-icon"><i class="fas fa-history"></i>
                    </div>
                    <div class="menu-title">History Linen</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'show_history_scan_linen')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'history_scan' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>history/scan">
                    <div class="parent-icon"><i class="fas fa-history"></i>
                    </div>
                    <div class="menu-title">History Scan</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'show_history_distribusi')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'history_distribusi' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>history/distribusi">
                    <div class="parent-icon"><i class="fas fa-history"></i>
                    </div>
                    <div class="menu-title">History Distribusi</div>
                </a>
            </li>
        <?php endif ?>

        <!-- Master Data -->
        <?php if (
            CekAkses(Auth()->role, 'show_data_linen') ||
            CekAkses(Auth()->role, 'show_jenis') ||
            CekAkses(Auth()->role, 'show_warna') ||
            CekAkses(Auth()->role, 'show_ukuran') ||
            CekAkses(Auth()->role, 'show_lokasi') ||
            CekAkses(Auth()->role, 'show_kategori')
        ): ?>
            <li class="menu-label">MASTER</li>
            <li class="<?= isset($sidebar) && in_array($sidebar, array('linen', 'jenis', 'warna', 'ukuran', 'lokasi', 'kategori')) ? 'mm-active' : ''; ?>">
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div class="menu-title">Master Data</div>
                </a>
                <ul class="<?= isset($sidebar) && in_array($sidebar, array('linen', 'jenis', 'warna', 'ukuran', 'lokasi', 'kategori')) ? 'mm-show' : ''; ?>">
                <?php endif ?>
                <?php if (CekAkses(Auth()->role, 'show_data_linen')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'linen' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>master/linen"><i class='bx bx-package'></i>Data Linen</a>
                    </li>
                <?php endif; ?>
                <?php if (CekAkses(Auth()->role, 'show_jenis')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'jenis' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>master/jenis"><i class='bx bx-grid-alt'></i>Jenis</a>
                    </li>
                <?php endif; ?>
                <?php if (CekAkses(Auth()->role, 'show_warna')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'warna' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>master/warna"><i class='bx bx-palette'></i>Warna</a>
                    </li>
                <?php endif; ?>
                <?php if (CekAkses(Auth()->role, 'show_ukuran')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'ukuran' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>master/ukuran"><i class='bx bx-ruler'></i>Ukuran</a>
                    </li>
                <?php endif; ?>
                <?php if (CekAkses(Auth()->role, 'show_lokasi')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'lokasi' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>master/lokasi"><i class='bx bx-map-pin'></i>Lokasi</a>
                    </li>
                <?php endif; ?>
                <?php if (CekAkses(Auth()->role, 'show_kategori')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'kategori' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>master/kategori"><i class='bx bx-category-alt'></i>Kategori</a>
                    </li>
                <?php endif; ?>

                <?php if (
                    CekAkses(Auth()->role, 'show_data_linen') ||
                    CekAkses(Auth()->role, 'show_jenis') ||
                    CekAkses(Auth()->role, 'show_warna') ||
                    CekAkses(Auth()->role, 'show_ukuran') ||
                    CekAkses(Auth()->role, 'show_lokasi') ||
                    CekAkses(Auth()->role, 'show_kategori')
                ): ?>
                </ul>
            </li>
        <?php endif ?>

        <!-- Pengaturan -->
        <?php if (
            getRole(Auth()->role) == 'admin' || getRole(Auth()->role) == 'Admin' ||
            CekAkses(Auth()->role, 'show_user') ||
            CekAkses(Auth()->role, 'show_role') ||
            CekAkses(Auth()->role, 'show_hak_akses')
        ): ?>
            <li class="menu-label">PENGATURAN</li>
            <li class="<?= isset($sidebar) && in_array($sidebar, array('role', 'hak_akses', 'users')) ? 'mm-active' : ''; ?>">
                <a class="has-arrow" href="javascript:;">
                    <div class="parent-icon"><i class="fa-solid fa-gear"></i>
                    </div>
                    <div class="menu-title">Pengaturan</div>
                </a>
                <ul class="<?= isset($sidebar) && in_array($sidebar, array('role', 'hak_akses', 'users')) ? 'mm-show' : ''; ?>">
                <?php endif; ?>

                <?php if (getRole(Auth()->role) == 'admin' || getRole(Auth()->role) == 'Admin' || CekAkses(Auth()->role, 'show_role')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'role' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>pengaturan/role"><i class='bx bx-slider'></i>Role</a>
                    </li>
                <?php endif ?>
                <?php if (getRole(Auth()->role) == 'admin' || getRole(Auth()->role) == 'Admin' || CekAkses(Auth()->role, 'show_hak_akses')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'hak_akses' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>pengaturan/hak-akses"><i class='bx bx-slider'></i>Hak Akses</a>
                    </li>
                <?php endif ?>
                <?php if (getRole(Auth()->role) == 'admin' || getRole(Auth()->role) == 'Admin' || CekAkses(Auth()->role, 'show_user')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'users' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>pengaturan/users"><i class='bx bx-group'></i>User</a>
                    </li>
                <?php endif; ?>

                <?php if (
                    CekAkses(Auth()->role, 'show_user') ||
                    CekAkses(Auth()->role, 'show_role') ||
                    CekAkses(Auth()->role, 'show_hak_akses')
                ): ?>
                </ul>
            </li>
        <?php endif; ?>
        <!-- <li>
            <a href="user-profile.html">
                <div class="parent-icon"><i class="bx bx-user-circle"></i>
                </div>
                <div class="menu-title">User Profile</div>
            </a>
        </li> -->


    </ul>
    <!--end navigation-->
</div>