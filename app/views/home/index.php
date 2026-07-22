<?php $this->view('layouts/guest_openTag', array('title' => $title)); ?>

<style>
    .hero-section {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 50%, var(--primary-dark) 100%);
        padding: 3rem 1rem 2rem;
        text-align: center;
        color: white;
        border-radius: 0 0 2rem 2rem;
        margin-bottom: 1.5rem;
    }
    .hero-section h1 { font-weight: 700; font-size: 1.8rem; margin-bottom: 0.5rem; color: #fff; }
    .hero-section p { font-size: 1rem; margin-bottom: 1.2rem; }
    .search-box { max-width: 500px; margin: 0 auto; }
    .search-box .input-group { background: white; border-radius: 50px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
    .search-box input { border: none; padding: 0.75rem 1.2rem; font-size: 0.95rem; }
    .search-box input:focus { box-shadow: none; }
    .search-box button { background: var(--primary); color: white; border: none; padding: 0 1.5rem; }
    .search-box button:hover { background: var(--primary-dark); }
    .section-title { font-weight: 700; color: #222; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .section-title i { color: var(--primary); }
    .filter-card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden; }
    .filter-card .card-body { padding: 1rem 1.25rem; }
    .filter-card select { border-radius: 10px; border: 1px solid #bbb; padding: 0.5rem 0.75rem; font-size: 0.9rem; }
    .filter-card select:focus { border-color: var(--primary); box-shadow: 0 0 0 0.2rem rgba(44,68,99,0.1); }
    .filter-card label { font-weight: 600; font-size: 0.85rem; color: #333; margin-bottom: 0.3rem; }
    .filter-card .btn-filter { background: var(--primary); color: white; border: none; border-radius: 10px; padding: 0.5rem 1.5rem; font-weight: 600; font-size: 0.9rem; }
    .filter-card .btn-filter:hover { background: var(--primary-dark); }
    .filter-card .btn-reset { background: transparent; color: #444; border: 1px solid #bbb; border-radius: 10px; padding: 0.5rem 1rem; font-weight: 600; font-size: 0.9rem; text-decoration: none; display: inline-block; }
    .filter-card .btn-reset:hover { background: #f5f5f5; }
    .gereja-card { border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); transition: transform 0.2s, box-shadow 0.2s; height: 100%; }
    .gereja-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.12); }
    .gereja-card .card-img-top { height: 180px; object-fit: cover; background: #f0f0f0; }
    .gereja-card .card-body { padding: 1rem; }
    .gereja-card .card-title { font-weight: 700; font-size: 1.05rem; color: #2C4463; }
    .gereja-card .card-text { font-size: 0.85rem; color: #333; }
    .gereja-card .btn-detail { background: var(--primary); color: white; border-radius: 50px; padding: 0.35rem 1rem; font-size: 0.85rem; border: none; }
    .gereja-card .btn-detail:hover { background: var(--primary-dark); }
    .stat-badge { background: rgba(44,68,99,0.12); color: #2C4463; padding: 0.4rem 0.8rem; border-radius: 50px; font-size: 0.8rem; font-weight: 700; display: inline-block; }
    .filter-active-bar {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 0.65rem 1rem;
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        flex-wrap: wrap;
        border: 1px solid rgba(0,0,0,0.04);
    }
    .filter-active-label {
        font-weight: 700;
        font-size: 0.78rem;
        color: var(--primary);
        white-space: nowrap;
        padding: 0.25rem 0.6rem;
        background: rgba(44,68,99,0.08);
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
    }
    .filter-tags { display: flex; flex-wrap: wrap; gap: 0.4rem; }
    .filter-tag {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.78rem;
        font-weight: 600;
        gap: 0.3rem;
    }
    .filter-tag.tag-location { background: rgba(44,68,99,0.1); color: var(--primary); }
    .filter-tag.tag-date { background: rgba(207,169,105,0.18); color: #7A5F2E; }
    .filter-tag.tag-time { background: rgba(127,168,201,0.18); color: #2A6A8A; }
    .lokasi-badge { background: rgba(0,0,0,0.06); color: #444; padding: 0.2rem 0.6rem; border-radius: 50px; font-size: 0.75rem; display: inline-block; font-weight: 500; }
    .empty-state { text-align: center; padding: 3rem 1rem; color: #555; }
    .empty-state i { font-size: 4rem; color: #bbb; margin-bottom: 1rem; display: block; }
    .page-content-wrap { width: 100%; padding-left: 1.25rem; padding-right: 1.25rem; }
    @media (min-width: 768px) { .page-content-wrap { padding-left: 2rem; padding-right: 2rem; } }
    @media (min-width: 1400px) { .page-content-wrap { padding-left: 3rem; padding-right: 3rem; } }
    .site-footer { background: var(--primary); color: rgba(255,255,255,.85); padding: 1.5rem 0; margin-top: 2rem; border-radius: 1.5rem 1.5rem 0 0; }
    .site-footer a { color: rgba(255,255,255,.9); text-decoration: none; }
    .site-footer a:hover { color: #fff; text-decoration: underline; }
    .jadwal-mini { font-size: 0.8rem; }
    .jadwal-mini .hari { color: #333; }
    .jadwal-mini .jam { color: var(--primary); font-weight: 600; }

    .filter-fab {
        position: fixed;
        bottom: 120px; right: 18px;
        width: 54px; height: 54px;
        border-radius: 50%;
        background: var(--primary);
        color: #fff;
        border: none;
        box-shadow: 0 4px 16px rgba(44,68,99,0.35);
        font-size: 1.4rem;
        z-index: 1035;
        display: none;
        align-items: center;
        justify-content: center;
        transition: transform .2s, box-shadow .2s;
    }
    .filter-fab:active { transform: scale(0.92); }
    .filter-fab.has-filter { background: #CFA969; }

    .maps-fab {
        position: fixed;
        bottom: 100px; right: 18px;
        width: 48px; height: 48px;
        border-radius: 50%;
        background: #CFA969;
        color: #fff;
        border: none;
        box-shadow: 0 4px 16px rgba(207,169,105,0.4);
        font-size: 1.3rem;
        z-index: 1035;
        display: none;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: transform .2s, box-shadow .2s;
    }
    .maps-fab:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(207,169,105,0.5); color: #fff; }
    .maps-fab:active { transform: scale(0.92); }

    .filter-drawer-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 1055;
        opacity: 0;
        visibility: hidden;
        transition: opacity .3s, visibility .3s;
    }
    .filter-drawer-overlay.active { opacity: 1; visibility: visible; }

    .filter-drawer {
        position: fixed;
        top: 0; right: 0; bottom: 0;
        width: 320px;
        max-width: 88vw;
        background: #fff;
        z-index: 1060;
        transform: translateX(100%);
        transition: transform .3s ease;
        box-shadow: -4px 0 24px rgba(0,0,0,0.18);
        display: flex;
        flex-direction: column;
        border-radius: 18px 0 0 18px;
    }
    .filter-drawer.active { transform: translateX(0); }

    .filter-drawer-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #eee;
        flex-shrink: 0;
    }
    .filter-drawer-header h6 { font-weight: 700; color: var(--primary); margin: 0; }
    .filter-drawer-header .btn-close { font-size: 0.85rem; }

    .filter-drawer-body {
        flex: 1;
        overflow-y: auto;
        padding: 1.25rem;
    }
    .filter-drawer-body select,
    .filter-drawer-body input {
        border-radius: 10px;
        border: 1px solid #bbb;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        width: 100%;
    }
    .filter-drawer-body select:focus,
    .filter-drawer-body input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(44,68,99,0.1);
        outline: none;
    }
    .filter-drawer-body label {
        font-weight: 600;
        font-size: 0.8rem;
        color: #333;
        margin-bottom: 0.25rem;
        display: block;
    }
    .filter-drawer-body .mb-3 { margin-bottom: 1rem; }
    .filter-drawer-body .btn-filter {
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        font-size: 0.9rem;
        width: 100%;
    }
    .filter-drawer-body .btn-filter:hover { background: var(--primary-dark); }
    .filter-drawer-body .btn-reset {
        background: transparent;
        color: #444;
        border: 1px solid #bbb;
        border-radius: 10px;
        padding: 0.6rem 1rem;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        display: block;
        text-align: center;
        width: 100%;
    }
    .filter-drawer-body .btn-reset:hover { background: #f5f5f5; }

    @media (max-width: 767px) {
        .filter-drawer-body { padding-bottom: 80px; }
        .hero-section { padding: 2rem 1rem 1.5rem; }
        .hero-section h1 { font-size: 1.4rem; }
        .gereja-card .card-img-top { height: 140px; }
        .site-footer { margin-bottom: 0; border-radius: 0; padding: 0.75rem 0; margin-top: 1rem; }
        .filter-fab { display: flex; }
        .maps-fab { display: none; }
    }
    @media (min-width: 768px) {
        .maps-fab { display: flex; }
    }
</style>

<div class="hero-section">
    <h1><i class="bx bx-church"></i> Info Gereja</h1>
    <p>Temukan gereja terdekat dan jadwal misa terbaru</p>
    <div class="search-box">
        <form action="<?= BASEURL; ?>cari" method="GET">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Cari nama gereja atau kota..." aria-label="Cari gereja">
                <button type="submit"><i class="bx bx-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="page-content-wrap pb-1">
    <div class="filter-card d-none d-md-block">
        <div class="card-body">
            <form method="GET" action="<?= BASEURL; ?>" id="filterForm">
                <div class="row g-2 align-items-end mb-2">
                    <div class="col-6 col-md-3">
                        <label for="provinsi"><i class="bx bx-map me-1"></i>Provinsi</label>
                        <select name="provinsi" id="provinsi" class="form-select w-100 mt-1" onchange="document.getElementById('filterForm').submit();">
                            <option value="">Semua Provinsi</option>
                            <?php foreach ($provinsiList as $p): ?>
                            <option value="<?= htmlspecialchars($p->provinsi); ?>" <?= $selectedProvinsi === $p->provinsi ? 'selected' : ''; ?>><?= htmlspecialchars($p->provinsi); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="kabupaten"><i class="bx bx-building me-1"></i>Kabupaten/Kota</label>
                        <select name="kabupaten" id="kabupaten" class="form-select w-100 mt-1" onchange="document.getElementById('filterForm').submit();">
                            <option value="">Semua Kabupaten/Kota</option>
                            <?php foreach ($kabupatenList as $k): ?>
                            <option value="<?= htmlspecialchars($k->kabupaten_kota); ?>" <?= $selectedKabupaten === $k->kabupaten_kota ? 'selected' : ''; ?>><?= htmlspecialchars($k->kabupaten_kota); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="kecamatan"><i class="bx bx-detail me-1"></i>Kecamatan</label>
                        <select name="kecamatan" id="kecamatan" class="form-select w-100 mt-1" onchange="document.getElementById('filterForm').submit();">
                            <option value="">Semua Kecamatan</option>
                            <?php foreach ($kecamatanList as $k): ?>
                            <option value="<?= htmlspecialchars($k->kecamatan); ?>" <?= $selectedKecamatan === $k->kecamatan ? 'selected' : ''; ?>><?= htmlspecialchars($k->kecamatan); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="kelurahan"><i class="bx bx-home me-1"></i>Kelurahan</label>
                        <select name="kelurahan" id="kelurahan" class="form-select w-100 mt-1" onchange="document.getElementById('filterForm').submit();">
                            <option value="">Semua Kelurahan</option>
                            <?php foreach ($kelurahanList as $k): ?>
                            <option value="<?= htmlspecialchars($k->kelurahan); ?>" <?= $selectedKelurahan === $k->kelurahan ? 'selected' : ''; ?>><?= htmlspecialchars($k->kelurahan); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row g-2 align-items-end mb-2">
                    <div class="col-6 col-md-3">
                        <label for="tgl_dari"><i class="bx bx-calendar me-1"></i>Tanggal Dari</label>
                        <input type="date" name="tgl_dari" id="tgl_dari" class="form-control w-100 mt-1" value="<?= htmlspecialchars($selectedTglDari); ?>">
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="tgl_sampai"><i class="bx bx-calendar me-1"></i>Tanggal Sampai</label>
                        <input type="date" name="tgl_sampai" id="tgl_sampai" class="form-control w-100 mt-1" value="<?= htmlspecialchars($selectedTglSampai); ?>">
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="jam_dari"><i class="bx bx-time me-1"></i>Jam Dari</label>
                        <input type="time" name="jam_dari" id="jam_dari" class="form-control w-100 mt-1" value="<?= htmlspecialchars($selectedJamDari); ?>">
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="jam_sampai"><i class="bx bx-time me-1"></i>Jam Sampai</label>
                        <input type="time" name="jam_sampai" id="jam_sampai" class="form-control w-100 mt-1" value="<?= htmlspecialchars($selectedJamSampai); ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-filter flex-fill"><i class="bx bx-filter-alt me-1"></i>Filter</button>
                        <a href="<?= BASEURL; ?>" class="btn-reset flex-fill text-center"><i class="bx bx-x me-1"></i>Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <button class="filter-fab d-md-none <?= $selectedProvinsi || $selectedKabupaten || $selectedKecamatan || $selectedKelurahan || $hasJadwalFilter ? 'has-filter' : ''; ?>" id="filterFab" onclick="toggleFilterDrawer()">
        <i class="bx <?= $selectedProvinsi || $selectedKabupaten || $selectedKecamatan || $selectedKelurahan || $hasJadwalFilter ? 'bx-filter' : 'bx-filter-alt'; ?>"></i>
    </button>
    <a href="<?= BASEURL; ?>maps" class="maps-fab" title="Lihat Peta Gereja">
        <i class="bx bx-map"></i>
    </a>

    <div class="filter-drawer-overlay d-md-none" id="filterOverlay" onclick="toggleFilterDrawer()"></div>
    <div class="filter-drawer d-md-none" id="filterDrawer">
        <div class="filter-drawer-header">
            <h6><i class="bx bx-filter-alt me-1"></i>Filter Pencarian</h6>
            <button type="button" class="btn-close" onclick="toggleFilterDrawer()"></button>
        </div>
        <div class="filter-drawer-body">
            <form method="GET" action="<?= BASEURL; ?>">
                <div class="mb-3">
                    <label><i class="bx bx-map me-1"></i>Provinsi</label>
                    <select name="provinsi" class="form-select">
                        <option value="">Semua Provinsi</option>
                        <?php foreach ($provinsiList as $p): ?>
                        <option value="<?= htmlspecialchars($p->provinsi); ?>" <?= $selectedProvinsi === $p->provinsi ? 'selected' : ''; ?>><?= htmlspecialchars($p->provinsi); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label><i class="bx bx-building me-1"></i>Kabupaten/Kota</label>
                    <select name="kabupaten" class="form-select">
                        <option value="">Semua Kabupaten/Kota</option>
                        <?php foreach ($kabupatenList as $k): ?>
                        <option value="<?= htmlspecialchars($k->kabupaten_kota); ?>" <?= $selectedKabupaten === $k->kabupaten_kota ? 'selected' : ''; ?>><?= htmlspecialchars($k->kabupaten_kota); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label><i class="bx bx-detail me-1"></i>Kecamatan</label>
                    <select name="kecamatan" class="form-select">
                        <option value="">Semua Kecamatan</option>
                        <?php foreach ($kecamatanList as $k): ?>
                        <option value="<?= htmlspecialchars($k->kecamatan); ?>" <?= $selectedKecamatan === $k->kecamatan ? 'selected' : ''; ?>><?= htmlspecialchars($k->kecamatan); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label><i class="bx bx-home me-1"></i>Kelurahan</label>
                    <select name="kelurahan" class="form-select">
                        <option value="">Semua Kelurahan</option>
                        <?php foreach ($kelurahanList as $k): ?>
                        <option value="<?= htmlspecialchars($k->kelurahan); ?>" <?= $selectedKelurahan === $k->kelurahan ? 'selected' : ''; ?>><?= htmlspecialchars($k->kelurahan); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <hr>
                <div class="mb-3">
                    <label><i class="bx bx-calendar me-1"></i>Tanggal Dari</label>
                    <input type="date" name="tgl_dari" class="form-control" value="<?= htmlspecialchars($selectedTglDari); ?>">
                </div>
                <div class="mb-3">
                    <label><i class="bx bx-calendar me-1"></i>Tanggal Sampai</label>
                    <input type="date" name="tgl_sampai" class="form-control" value="<?= htmlspecialchars($selectedTglSampai); ?>">
                </div>
                <div class="mb-3">
                    <label><i class="bx bx-time me-1"></i>Jam Dari</label>
                    <input type="time" name="jam_dari" class="form-control" value="<?= htmlspecialchars($selectedJamDari); ?>">
                </div>
                <div class="mb-3">
                    <label><i class="bx bx-time me-1"></i>Jam Sampai</label>
                    <input type="time" name="jam_sampai" class="form-control" value="<?= htmlspecialchars($selectedJamSampai); ?>">
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-filter flex-fill"><i class="bx bx-filter-alt me-1"></i>Filter</button>
                    <a href="<?= BASEURL; ?>" class="btn-reset flex-fill"><i class="bx bx-x me-1"></i>Reset</a>
                </div>
                <div style="height:1px;"></div>
            </form>
        </div>
    </div>

    <?php if ($selectedProvinsi || $selectedKabupaten || $selectedKecamatan || $selectedKelurahan || $hasJadwalFilter): ?>
    <div class="filter-active-bar mb-3">
        <span class="filter-active-label"><i class="bx bx-filter-alt me-1"></i>Filter Aktif</span>
        <div class="filter-tags">
            <?php if ($selectedProvinsi): ?>
            <span class="filter-tag tag-location"><i class="bx bx-map"></i><?= htmlspecialchars($selectedProvinsi); ?></span>
            <?php endif; ?>
            <?php if ($selectedKabupaten): ?>
            <span class="filter-tag tag-location"><i class="bx bx-building"></i><?= htmlspecialchars($selectedKabupaten); ?></span>
            <?php endif; ?>
            <?php if ($selectedKecamatan): ?>
            <span class="filter-tag tag-location"><i class="bx bx-detail"></i><?= htmlspecialchars($selectedKecamatan); ?></span>
            <?php endif; ?>
            <?php if ($selectedKelurahan): ?>
            <span class="filter-tag tag-location"><i class="bx bx-home"></i><?= htmlspecialchars($selectedKelurahan); ?></span>
            <?php endif; ?>
            <?php if ($selectedTglDari): ?>
            <span class="filter-tag tag-date"><i class="bx bx-calendar"></i>&ge; <?= date('d/m/Y', strtotime($selectedTglDari)); ?></span>
            <?php endif; ?>
            <?php if ($selectedTglSampai): ?>
            <span class="filter-tag tag-date"><i class="bx bx-calendar"></i>&le; <?= date('d/m/Y', strtotime($selectedTglSampai)); ?></span>
            <?php endif; ?>
            <?php if ($selectedJamDari): ?>
            <span class="filter-tag tag-time"><i class="bx bx-time"></i>&ge; <?= htmlspecialchars($selectedJamDari); ?></span>
            <?php endif; ?>
            <?php if ($selectedJamSampai): ?>
            <span class="filter-tag tag-time"><i class="bx bx-time"></i>&le; <?= htmlspecialchars($selectedJamSampai); ?></span>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="section-title">
            <i class="bx bx-building-house fs-4"></i>
            <span>Daftar Gereja</span>
            <span class="stat-badge ms-2"><?= $totalGereja; ?> gereja</span>
        </div>
    </div>

    <?php if (empty($gerejaList)): ?>
    <div class="empty-state">
        <i class="bx bx-church"></i>
        <h5 class="fw-bold">Tidak ada gereja ditemukan</h5>
        <p class="mb-0">Coba ubah filter lokasi atau tanggal/jam jadwal misa.</p>
    </div>
    <?php else: ?>
    <div class="row g-3">
        <?php foreach ($gerejaList as $g): ?>
        <div class="col-12 col-md-4 col-xl-3">
            <div class="card gereja-card">
                <?php
                    $fotoUrl = isset($fotoByGereja[$g->id]) ? $fotoByGereja[$g->id] : '';
                ?>
                <?php if ($fotoUrl): ?>
                    <img src="<?= htmlspecialchars($fotoUrl); ?>" class="card-img-top" alt="<?= htmlspecialchars($g->nama_gereja); ?>">
                <?php else: ?>
                    <div class="card-img-top d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));">
                        <i class="bx bx-church" style="font-size: 4rem; color: rgba(255,255,255,0.3);"></i>
                    </div>
                <?php endif; ?>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($g->nama_gereja); ?></h5>
                    <p class="card-text flex-grow-1">
                        <i class="bx bx-map-pin text-secondary me-1"></i><?= htmlspecialchars($g->alamat); ?>
                    </p>
                    <p class="card-text mb-2">
                        <span class="lokasi-badge"><i class="bx bx-map me-1"></i><?= htmlspecialchars($g->provinsi); ?></span>
                        <span class="lokasi-badge ms-1"><i class="bx bx-building me-1"></i><?= htmlspecialchars($g->kabupaten_kota); ?></span>
                    </p>
                    <?php if ($g->kontak_telepon): ?>
                        <p class="card-text mb-2"><i class="bx bx-phone text-secondary me-1"></i><?= htmlspecialchars($g->kontak_telepon); ?></p>
                    <?php endif; ?>
                    <?php if ($hasJadwalFilter && isset($jadwalByGereja[$g->id])): ?>
                    <div class="mb-2 pt-2 border-top">
                        <small class="fw-bold" style="color:var(--primary);"><i class="bx bx-calendar-event me-1"></i>Jadwal Misa</small>
                        <?php foreach ($jadwalByGereja[$g->id] as $jj): ?>
                        <div class="d-flex justify-content-between small py-1" style="border-bottom:1px solid rgba(0,0,0,0.04);">
                            <span><?= htmlspecialchars($jj->hari); ?><?= $jj->tanggal ? ', ' . date('d/m', strtotime($jj->tanggal)) : ''; ?></span>
                            <span class="fw-semibold"><?= date('H:i', strtotime($jj->waktu_mulai)); ?> WIB</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <a href="<?= BASEURL; ?>gereja/<?= htmlspecialchars($g->slug ? $g->slug : $g->id); ?>" class="btn btn-detail w-100 mt-2">
                        <i class="bx bx-detail me-1"></i>Lihat Detail & Jadwal
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<footer class="site-footer">
    <div class="page-content-wrap">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                <i class="bx bx-church me-1"></i><strong>Info Gereja</strong> — Direktori Gereja Katolik Indonesia
            </div>
            <div class="col-md-6 text-center text-md-end small">
                &copy; <?= date('Y'); ?> Info Gereja
            </div>
        </div>
    </div>
</footer>

<?php $this->view('layouts/guest_bottom_nav', array('activeMenu' => 'home')); ?>

<script>
function toggleFilterDrawer() {
    var drawer = document.getElementById('filterDrawer');
    var overlay = document.getElementById('filterOverlay');
    var fab = document.getElementById('filterFab');
    drawer.classList.toggle('active');
    overlay.classList.toggle('active');
    document.body.style.overflow = drawer.classList.contains('active') ? 'hidden' : '';
}
</script>
<?php $this->view('layouts/guest_closeTag'); ?>
