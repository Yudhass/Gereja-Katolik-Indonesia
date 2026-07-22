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
        <li class="<?= isset($sidebar) && $sidebar == 'data_linen_pasien' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>pasien/data-linen-pasien">
                <div class="parent-icon">
                    <img src="<?= BASEURL; ?>assets/images/icons/linen.webp" alt="linen icon" class="icon-img">
                </div>
                <div class="menu-title">Data Linen Pasien</div>
            </a>
        </li>
        <li class="<?= isset($sidebar) && $sidebar == 'penyerahan_linen_bersih_pasien' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>pasien/penyerahan-linen-bersih-pasien">
                <div class="parent-icon"><i class="bx bx-export"></i>
                </div>
                <div class="menu-title">Penyerahan Linen Bersih Pasien</div>
            </a>
        </li>
        <li class="<?= isset($sidebar) && $sidebar == 'penerimaan_linen_kotor_pasien' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>pasien/penerimaan-linen-kotor-pasien">
                <div class="parent-icon"><i class="bx bx-import"></i>
                </div>
                <div class="menu-title">Penerimaan Linen Kotor Pasien</div>
            </a>
        </li>

        <li class="menu-label">RUANG LAIN</li>
        <li class="<?= isset($sidebar) && $sidebar == 'penerimaan_linen_unit' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>distribusi/penerimaan-linen-unit">
                <div class="parent-icon">
                    <img src="<?= BASEURL; ?>assets/images/icons/terima_kain.webp" alt="terima kain icon" class="icon-img">
                </div>
                <div class="menu-title">Penerimaan Linen Bersih</div>
            </a>
        </li>
        <li class="<?= isset($sidebar) && $sidebar == 'daftar_linen_bersih_unit' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>linen/daftar-linen-bersih-unit">
                <div class="parent-icon">
                    <img src="<?= BASEURL; ?>assets/images/icons/bersih.webp" alt="bersih icon" class="icon-img">
                </div>
                <div class="menu-title">Daftar Linen Bersih</div>
            </a>
        </li>
        <li class="<?= isset($sidebar) && $sidebar == 'daftar_linen_kotor_unit' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>linen/daftar-linen-kotor-unit">
                <div class="parent-icon">
                    <img src="<?= BASEURL; ?>assets/images/icons/linen_kotor.webp" alt="linen kotor icon" class="icon-img">
                </div>
                <div class="menu-title">Daftar Linen Kotor</div>
            </a>
        </li>
        <li class="<?= isset($sidebar) && $sidebar == 'penyerahan_linen_kotor_ranap' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>distribusi/penyerahan-linen-kotor-ranap">
                <div class="parent-icon">
                    <img src="<?= BASEURL; ?>assets/images/icons/kotor.webp" alt="kotor icon" class="icon-img">
                </div>
                <div class="menu-title">Penyerahan Linen Kotor Ranap</div>
            </a>
        </li>
        <li class="<?= isset($sidebar) && $sidebar == 'penyerahan_linen_kotor_ok' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>distribusi/penyerahan-linen-kotor-ok">
                <div class="parent-icon">
                    <img src="<?= BASEURL; ?>assets/images/icons/kotor.webp" alt="kotor icon" class="icon-img">
                </div>
                <div class="menu-title">Penyerahan Linen Kotor OK</div>
            </a>
        </li>

        <li class="menu-label">RUANG LAUNDRY</li>
        <li class="<?= isset($sidebar) && $sidebar == 'daftar_linen_bersih_laundry' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>linen/daftar-linen-bersih-laundry">
                <div class="parent-icon">
                    <img src="<?= BASEURL; ?>assets/images/icons/bersih.webp" alt="bersih icon" class="icon-img">
                </div>
                <div class="menu-title">Daftar Linen Bersih Laundry</div>
            </a>
        </li>
        <li class="<?= isset($sidebar) && $sidebar == 'daftar_linen_kotor_laundry' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>linen/daftar-linen-kotor-laundry">
                <div class="parent-icon">
                    <img src="<?= BASEURL; ?>assets/images/icons/linen_kotor.webp" alt="linen kotor icon" class="icon-img">
                </div>
                <div class="menu-title">Daftar Linen Kotor Laundry</div>
            </a>
        </li>
        <li class="<?= isset($sidebar) && $sidebar == 'daftar_linen_dicuci_laundry' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>linen/daftar-linen-dicuci-laundry">
                <div class="parent-icon">
                    <img src="<?= BASEURL; ?>assets/images/icons/dicuci.webp" alt="linen dicuci icon" class="icon-img">
                </div>
                <div class="menu-title">Daftar Linen Dicuci Laundry</div>
            </a>
        </li>
        <li class="<?= isset($sidebar) && $sidebar == 'penyerahan_linen_unit' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>distribusi/penyerahan-linen-unit">
                <div class="parent-icon">
                    <i class="bx bx-export"></i>
                </div>
                <div class="menu-title">Penyerahan Linen Bersih</div>
            </a>
        </li>
        <li class="<?= isset($sidebar) && $sidebar == 'penerimaan_linen_unit' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>penerimaan-linen-unit">
                <div class="parent-icon">
                    <i class="bx bx-import"></i>
                </div>
                <div class="menu-title">Penerimaan Linen Kotor</div>
            </a>
        </li>

        <li class="menu-label">RUANG SETRIKA</li>

        <li class="menu-label">HISTORY</li>
        <li class="<?= isset($sidebar) && $sidebar == 'history_linen' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>history/linen">
                <div class="parent-icon"><i class="fas fa-history"></i>
                </div>
                <div class="menu-title">History Linen</div>
            </a>
        </li>
        <li class="<?= isset($sidebar) && $sidebar == 'history_scan' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>history/scan">
                <div class="parent-icon"><i class="fas fa-history"></i>
                </div>
                <div class="menu-title">History Scan</div>
            </a>
        </li>
        <li class="<?= isset($sidebar) && $sidebar == 'history_distribusi' ? 'mm-active' : ''; ?>">
            <a href="<?= BASEURL; ?>history/distribusi">
                <div class="parent-icon"><i class="fas fa-history"></i>
                </div>
                <div class="menu-title">History Distribusi</div>
            </a>
        </li>

        <li class="menu-label">MASTER</li>
        <li class="<?php
                    if (isset($sidebar) && in_array($sidebar, array('kategori', 'warna', 'ukuran', 'lokasi'))) {
                        echo 'mm-active';
                    }
                    ?>">
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div class="menu-title">Master Data</div>
            </a>
            <ul class="<?php
                        if (isset($sidebar) && in_array($sidebar, array('kategori', 'warna', 'ukuran', 'lokasi'))) {
                            echo 'mm-show';
                        }
                        ?>">
                <?php if (CekAkses(Auth()->role, 'show_linen')): ?>
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
            </ul>
        </li>
        <li class="menu-label">PENGATURAN</li>
        <li class="<?php
                    if (isset($sidebar) && in_array($sidebar, array('hak_akses', 'users'))) {
                        echo 'mm-active';
                    }
                    ?>">
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class="fa-solid fa-gear"></i>
                </div>
                <div class="menu-title">Pengaturan</div>
            </a>
            <ul class="<?php
                        if (isset($sidebar) && in_array($sidebar, array('hak_akses', 'users'))) {
                            echo 'mm-show';
                        }
                        ?>">
                <li class="<?= isset($sidebar) && $sidebar == 'role' ? 'mm-active' : ''; ?>">
                    <a href="<?= BASEURL; ?>pengaturan/role"><i class='bx bx-slider'></i>Role</a>
                </li>
                <li class="<?= isset($sidebar) && $sidebar == 'hak_akses' ? 'mm-active' : ''; ?>">
                    <a href="<?= BASEURL; ?>pengaturan/hak-akses"><i class='bx bx-slider'></i>Hak Akses</a>
                </li>
                <?php if (CekAkses(Auth()->role, 'show_user')): ?>
                    <li class="<?= isset($sidebar) && $sidebar == 'users' ? 'mm-active' : ''; ?>">
                        <a href="<?= BASEURL; ?>pengaturan/users"><i class='bx bx-group'></i>User</a>
                    </li>
                <?php endif; ?>
            </ul>
        </li>
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