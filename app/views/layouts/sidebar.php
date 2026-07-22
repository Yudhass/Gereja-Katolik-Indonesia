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
        <!-- dashboard -->
        <li class="<?= isset($sidebar) && $sidebar == 'dashboard' ? 'mm-active' : ''; ?>">
            <?php if (Auth()->role_nama == 'admin' || Auth()->role_nama == 'Admin'): ?>
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

        <!-- Pasien -->
        <li class="menu-label">PASIEN</li>
        <li class="<?= isset($sidebar) && $sidebar == 'data_pasien_ranap' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>pasien/ranap">
                <div class="parent-icon"><i class="fas fa-bed"></i>
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
        <?php if (CekAkses(Auth()->role, 'read_linen_yang_digunakan_pasien')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'read_linen_yang_digunakan_pasien' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>pasien/daftar-linen-digunakan-pasien">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/linen_pasien.webp" alt="linen icon" class="icon-img">
                    </div>
                    <div class="menu-title">Daftar Pengguna Aktif Linen Pasien</div>
                </a>
            </li>
        <?php endif ?>

        <!-- Ruang Unit -->
        <?php if (
            CekAkses(Auth()->role, 'penerimaan_linen_bersih_dari_laundry') ||
            CekAkses(Auth()->role, 'penyerahan_linen_kotor_ke_laundry') ||
            CekAkses(Auth()->role, 'read_linen_bersih_di_unit') ||
            CekAkses(Auth()->role, 'read_linen_kotor_di_unit') ||
            CekAkses(Auth()->role, 'read_linen_bersih_di_unit_admin') ||
            CekAkses(Auth()->role, 'read_linen_kotor_di_unit_admin')
        ): ?>
            <li class="menu-label">RUANG UNIT</li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'penerimaan_linen_bersih_dari_laundry')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'penerimaan_linen_bersih_dari_laundry' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>distribusi/penerimaan-linen-bersih-dari-laundry">
                    <div class="parent-icon">
                        <i class="bx bx-import"></i>
                    </div>
                    <div class="menu-title">Penerimaan Linen Bersih</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'penyerahan_linen_kotor_ke_laundry')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'penyerahan_linen_kotor_ke_laundry' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>distribusi/penyerahan-linen-kotor-dari-ranap">
                    <div class="parent-icon">
                        <i class="bx bx-export"></i>
                    </div>
                    <div class="menu-title">Penyerahan Linen Kotor Ke Laundry</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'read_linen_bersih_di_unit')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'read_linen_bersih_di_unit' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>linen/daftar-linen-bersih-di-unit">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/bersih.webp" alt="bersih icon" class="icon-img">
                    </div>
                    <div class="menu-title">Daftar Linen Bersih di Unit</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'read_linen_bersih_di_unit_admin')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'read_linen_bersih_di_unit_admin' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>linen/daftar-linen-bersih-di-unit-admin">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/bersih.webp" alt="bersih icon" class="icon-img">
                    </div>
                    <div class="menu-title">Daftar Linen Bersih di Unit (Admin)</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'read_linen_kotor_di_unit')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'read_linen_kotor_di_unit' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>linen/daftar-linen-kotor-di-unit">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/kotor.webp" alt="kotor icon" class="icon-img">
                    </div>
                    <div class="menu-title">Daftar Linen Kotor di Unit</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'read_linen_kotor_di_unit_admin')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'read_linen_kotor_di_unit_admin' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>linen/daftar-linen-kotor-di-unit-admin">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/kotor.webp" alt="kotor icon" class="icon-img">
                    </div>
                    <div class="menu-title">Daftar Linen Kotor di Unit (Admin)</div>
                </a>
            </li>
        <?php endif ?>

        <!-- Ruangan Cuci -->
        <?php if (
            CekAkses(Auth()->role, 'read_linen_yang_sedang_dicuci') ||
            CekAkses(Auth()->role, 'history_linen_yang_dicuci')
        ): ?>
            <li class="menu-label">RUANG CUCI</li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'read_linen_yang_sedang_dicuci')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'read_linen_yang_sedang_dicuci' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>linen/daftar-linen-yang-sedang-dicuci">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/dicuci.webp" alt="linen dicuci icon" class="icon-img">
                    </div>
                    <div class="menu-title">Daftar Linen Yang Sedang Dicuci</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'history_linen_yang_dicuci')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'history_linen_yang_dicuci' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>linen/history-linen-yang-dicuci">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/bersih.webp" alt="linen bersih icon" class="icon-img">
                    </div>
                    <div class="menu-title">History Linen Yang Dicuci</div>
                </a>
            </li>
        <?php endif ?>

        <!-- Ruangan Setrika -->
        <?php if (
            CekAkses(Auth()->role, 'setrika_linen') ||
            CekAkses(Auth()->role, 'history_linen_yang_disetrika')
        ): ?>
            <li class="menu-label">RUANG SETRIKA</li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'setrika_linen')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'setrika_linen' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>linen/setrika-linen">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/setrika.webp" alt="linen setrika icon" class="icon-img">
                    </div>
                    <div class="menu-title">Setrika Linen</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'history_linen_yang_disetrika')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'history_linen_yang_disetrika' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>linen/history-linen-yang-disetrika">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/history_setrika.webp" alt="linen setrika icon" class="icon-img">
                    </div>
                    <div class="menu-title">History Setrika Linen</div>
                </a>
            </li>
        <?php endif ?>


        <!-- Ruangan Laundry -->
        <?php if (
            CekAkses(Auth()->role, 'read_linen_bersih_di_laundry') ||
            CekAkses(Auth()->role, 'read_linen_kotor_di_laundry') ||
            CekAkses(Auth()->role, 'penyerahan_linen_bersih_dari_laundry') ||
            CekAkses(Auth()->role, 'penerimaan_linen_kotor_dari_unit')
        ): ?>
            <li class="menu-label">RUANG LAUNDRY</li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'read_linen_bersih_di_laundry')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'read_linen_bersih_di_laundry' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>linen/daftar-linen-bersih-di-laundry">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/bersih.webp" alt="bersih icon" class="icon-img">
                    </div>
                    <div class="menu-title">Daftar Linen Bersih di Laundry</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'read_linen_kotor_di_laundry')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'show_linen_kotor_di_laundry' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>linen/daftar-linen-kotor-di-laundry">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/linen_kotor.webp" alt="linen kotor icon" class="icon-img">
                    </div>
                    <div class="menu-title">Daftar Linen Kotor di Laundry</div>
                </a>
            </li>
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

        <!-- Cek RFID -->
        <li class="menu-label">PELACAKAN</li>
        <li class="<?= isset($sidebar) && $sidebar == 'cek_rfid_linen' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>linen/cek-rfid">
                <div class="parent-icon"><i class="fas fa-search"></i>
                </div>
                <div class="menu-title">Cek RFID Linen</div>
            </a>
        </li>

        <!-- History -->
        <?php if (
            CekAkses(Auth()->role, 'read_history_scan_linen') ||
            CekAkses(Auth()->role, 'read_history_distribusi_ke_unit') ||
            CekAkses(Auth()->role, 'read_history_distribusi_ke_pasien')
        ): ?>
            <li class="menu-label">HISTORY</li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'read_history_scan_linen')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'history_scan' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>history/scan">
                    <div class="parent-icon"><i class="fas fa-history"></i>
                    </div>
                    <div class="menu-title">History Scan</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'read_history_distribusi_ke_unit')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'history_distribusi_ke_unit' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>history/distribusi-unit">
                    <div class="parent-icon"><i class="fas fa-history"></i>
                    </div>
                    <div class="menu-title">History Distribusi Unit</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'read_history_distribusi_ke_pasien')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'history_distribusi_ke_pasien' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>history/distribusi-pasien">
                    <div class="parent-icon"><i class="fas fa-history"></i>
                    </div>
                    <div class="menu-title">History Distribusi Pasien</div>
                </a>
            </li>
        <?php endif ?>

        <?php if (
            CekAkses(Auth()->role, 'laporan_log_book_cuci') ||
            CekAkses(Auth()->role, 'laporan_log_book_setrika') ||
            CekAkses(Auth()->role, 'laporan_log_book_cuci_admin') ||
            CekAkses(Auth()->role, 'laporan_log_book_setrika_admin')
        ): ?>
            <li class="menu-label">LOG BOOK</li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'laporan_log_book_cuci')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'laporan_log_book_cuci' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>laporan/log-book-cuci">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/logbook.webp" alt="log book icon" class="icon-img">
                    </div>
                    <div class="menu-title">Laporan Log Book Cuci</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'laporan_log_book_setrika')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'laporan_log_book_setrika' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>laporan/log-book-setrika">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/logbook.webp" alt="log book icon" class="icon-img">
                    </div>
                    <div class="menu-title">Laporan Log Book Setrika</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'laporan_log_book_cuci_admin')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'laporan_log_book_cuci_admin' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>laporan/log-book-cuci-admin">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/logbook.webp" alt="log book icon" class="icon-img">
                    </div>
                    <div class="menu-title">Laporan Log Book Cuci (Admin)</div>
                </a>
            </li>
        <?php endif ?>
        <?php if (CekAkses(Auth()->role, 'laporan_log_book_setrika_admin')): ?>
            <li class="<?= isset($sidebar) && $sidebar == 'laporan_log_book_setrika_admin' ? 'mm-active' : ''; ?>">
                <a href="<?= BASEURL; ?>laporan/log-book-setrika-admin">
                    <div class="parent-icon">
                        <img src="<?= BASEURL; ?>assets/images/icons/logbook.webp" alt="log book icon" class="icon-img">
                    </div>
                    <div class="menu-title">Laporan Log Book Setrika (Admin)</div>
                </a>
            </li>
        <?php endif ?>

        <!-- Master Data -->
        <?php if (
            CekAkses(Auth()->role, 'read_kategori') ||
            CekAkses(Auth()->role, 'read_lokasi') ||
            CekAkses(Auth()->role, 'read_ukuran') ||
            CekAkses(Auth()->role, 'read_warna') ||
            CekAkses(Auth()->role, 'read_jenis') ||
            CekAkses(Auth()->role, 'read_data_linen')
        ): ?>
            <li class="menu-label">MASTER DATA</li>
            <li class="<?= isset($sidebar) && in_array($sidebar, array('kategori', 'lokasi', 'ukuran', 'warna', 'jenis', 'linen')) ? 'mm-active' : ''; ?>">
                <a class="has-arrow" href="javascript:;">
                    <div class="parent-icon"><i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div class="menu-title">Master Data</div>
                </a>
                <ul class="<?= isset($sidebar) && in_array($sidebar, array('kategori', 'lokasi', 'ukuran', 'warna', 'jenis', 'linen')) ? 'mm-show' : ''; ?>">
                <?php endif ?>

                <?php if (CekAkses(Auth()->role, 'read_kategori')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'kategori' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>master/kategori"><i class='bx bx-category-alt'></i>Data Kategori</a>
                    </li>
                <?php endif ?>

                <?php if (CekAkses(Auth()->role, 'read_lokasi')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'lokasi' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>master/lokasi"><i class='bx bx-map'></i>Data Lokasi</a>
                    </li>
                <?php endif ?>

                <?php if (CekAkses(Auth()->role, 'read_ukuran')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'ukuran' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>master/ukuran"><i class='bx bx-ruler'></i>Data Ukuran</a>
                    </li>
                <?php endif ?>

                <?php if (CekAkses(Auth()->role, 'read_warna')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'warna' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>master/warna"><i class='bx bx-palette'></i>Data Warna</a>
                    </li>
                <?php endif ?>

                <?php if (CekAkses(Auth()->role, 'read_jenis')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'jenis' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>master/jenis"><i class='bx bx-grid-alt'></i>Data Jenis</a>
                    </li>
                <?php endif ?>

                <?php if (CekAkses(Auth()->role, 'read_data_linen')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'linen' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>master/linen"><i class='bx bx-package'></i>Data Linen</a>
                    </li>
                <?php endif; ?>

                <?php if (
                    CekAkses(Auth()->role, 'read_kategori') ||
                    CekAkses(Auth()->role, 'read_lokasi') ||
                    CekAkses(Auth()->role, 'read_ukuran') ||
                    CekAkses(Auth()->role, 'read_warna') ||
                    CekAkses(Auth()->role, 'read_jenis') ||
                    CekAkses(Auth()->role, 'read_data_linen')
                ): ?>
                </ul>
            </li>
        <?php endif ?>

        <!-- Pengaturan -->
        <?php if (
            getRole(Auth()->role) == 'admin' || getRole(Auth()->role) == 'Admin' ||
            CekAkses(Auth()->role, 'read_user') ||
            CekAkses(Auth()->role, 'read_role') ||
            CekAkses(Auth()->role, 'read_hak_akses')
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

                <?php if (getRole(Auth()->role) == 'admin' || getRole(Auth()->role) == 'Admin' || CekAkses(Auth()->role, 'read_role')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'role' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>pengaturan/role"><i class="fas fa-users-cog"></i>Role</a>
                    </li>
                <?php endif ?>
                <?php if (getRole(Auth()->role) == 'admin' || getRole(Auth()->role) == 'Admin' || CekAkses(Auth()->role, 'read_hak_akses')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'hak_akses' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>pengaturan/hak-akses"><i class="fas fa-user-lock"></i>Hak Akses</a>
                    </li>
                <?php endif ?>
                <?php if (getRole(Auth()->role) == 'admin' || getRole(Auth()->role) == 'Admin' || CekAkses(Auth()->role, 'read_user')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'users' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>pengaturan/users"><i class="fas fa-users"></i>User</a>
                    </li>
                <?php endif; ?>

                <?php if (
                    CekAkses(Auth()->role, 'read_user') ||
                    CekAkses(Auth()->role, 'read_role') ||
                    CekAkses(Auth()->role, 'read_hak_akses')
                ): ?>
                </ul>
            </li>
        <?php endif; ?>


    </ul>
    <!--end navigation-->
</div>