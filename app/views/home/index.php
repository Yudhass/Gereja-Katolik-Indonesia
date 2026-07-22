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
    .lokasi-badge { background: rgba(0,0,0,0.06); color: #444; padding: 0.2rem 0.6rem; border-radius: 50px; font-size: 0.75rem; display: inline-block; font-weight: 500; }
    .empty-state { text-align: center; padding: 3rem 1rem; color: #555; }
    .empty-state i { font-size: 4rem; color: #bbb; margin-bottom: 1rem; display: block; }
    @media (max-width: 767px) {
        .hero-section { padding: 2rem 1rem 1.5rem; }
        .hero-section h1 { font-size: 1.4rem; }
        .gereja-card .card-img-top { height: 140px; }
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

<div class="container px-3 px-md-4 pb-5 mb-4">
    <div class="filter-card">
        <div class="card-body">
            <form method="GET" action="<?= BASEURL; ?>" id="filterForm">
                <div class="row g-2 align-items-end">
                    <div class="col-6 col-md-4">
                        <label for="provinsi"><i class="bx bx-map me-1"></i>Provinsi</label>
                        <select name="provinsi" id="provinsi" class="form-select w-100 mt-1" onchange="document.getElementById('filterForm').submit();">
                            <option value="">Semua Provinsi</option>
                            <?php foreach ($provinsiList as $p): ?>
                            <option value="<?= htmlspecialchars($p->provinsi); ?>" <?= $selectedProvinsi === $p->provinsi ? 'selected' : ''; ?>><?= htmlspecialchars($p->provinsi); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-4">
                        <label for="kabupaten"><i class="bx bx-building me-1"></i>Kabupaten/Kota</label>
                        <select name="kabupaten" id="kabupaten" class="form-select w-100 mt-1" onchange="document.getElementById('filterForm').submit();">
                            <option value="">Semua Kabupaten/Kota</option>
                            <?php foreach ($kabupatenList as $k): ?>
                            <option value="<?= htmlspecialchars($k->kabupaten_kota); ?>" <?= $selectedKabupaten === $k->kabupaten_kota ? 'selected' : ''; ?>><?= htmlspecialchars($k->kabupaten_kota); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-filter flex-fill"><i class="bx bx-filter-alt me-1"></i>Filter</button>
                        <a href="<?= BASEURL; ?>" class="btn-reset flex-fill text-center"><i class="bx bx-x me-1"></i>Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($selectedProvinsi || $selectedKabupaten): ?>
    <div class="mb-3">
        <span class="stat-badge"><i class="bx bx-filter-alt me-1"></i>Filter aktif</span>
        <?php if ($selectedProvinsi): ?>
        <span class="stat-badge ms-1"><?= htmlspecialchars($selectedProvinsi); ?></span>
        <?php endif; ?>
        <?php if ($selectedKabupaten): ?>
        <span class="stat-badge ms-1"><?= htmlspecialchars($selectedKabupaten); ?></span>
        <?php endif; ?>
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
        <p class="mb-0">Coba ubah filter provinsi atau kabupaten/kota.</p>
    </div>
    <?php else: ?>
    <div class="row g-3">
        <?php foreach ($gerejaList as $g): ?>
        <div class="col-12 col-md-6 col-lg-4">
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

<?php $this->view('layouts/guest_bottom_nav', array('activeMenu' => 'home')); ?>
<?php $this->view('layouts/guest_closeTag'); ?>
