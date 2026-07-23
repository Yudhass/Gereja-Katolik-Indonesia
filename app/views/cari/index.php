<?php $this->view('layouts/guest_openTag', array('title' => $title)); ?>

<style>
    .page-header { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 50%, var(--primary-dark) 100%); color: white; padding: 2.5rem 1rem 2rem; text-align: center; border-radius: 0 0 2rem 2rem; margin-bottom: 1.5rem; }
    .page-header h1 { font-size: 1.6rem; font-weight: 700; color: #fff; margin-bottom: 0.5rem; }
    .page-header p { font-size: 0.95rem; margin-bottom: 0; opacity: 0.85; }
    .page-content-wrap { width: 100%; padding-left: 1.25rem; padding-right: 1.25rem; }
    @media (min-width: 768px) { .page-content-wrap { padding-left: 2rem; padding-right: 2rem; } }
    @media (min-width: 1400px) { .page-content-wrap { padding-left: 3rem; padding-right: 3rem; } }
    .filter-card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden; }
    .filter-card .card-body { padding: 1rem 1.25rem; }
    .filter-card label { font-weight: 600; font-size: 0.85rem; color: #333; margin-bottom: 0.3rem; }
    .filter-card select, .filter-card input { border-radius: 10px; border: 1px solid #bbb; padding: 0.45rem 0.75rem; font-size: 0.88rem; }
    .filter-card select:focus, .filter-card input:focus { border-color: var(--primary); box-shadow: 0 0 0 0.2rem rgba(44,68,99,0.1); }
    .btn-filter { background: var(--primary); color: white; border: none; border-radius: 10px; padding: 0.45rem 1.2rem; font-weight: 600; font-size: 0.88rem; }
    .btn-filter:hover { background: var(--primary-dark); color: white; }
    .btn-reset { background: transparent; color: #444; border: 1px solid #bbb; border-radius: 10px; padding: 0.45rem 1rem; font-weight: 600; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
    .btn-reset:hover { background: #f5f5f5; }
    .result-card { border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); transition: transform 0.2s, box-shadow 0.2s; height: 100%; }
    .result-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.12); }
    .result-card .card-body { padding: 1rem 1rem 0.85rem; }
    .result-card .card-title { font-size: 1.05rem; font-weight: 700; color: #2C4463; margin-bottom: 0.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .result-card .card-text { font-size: 0.85rem; color: #444; }
    .result-card .btn-detail { background: var(--primary); color: white; border-radius: 50px; padding: 0.4rem 1rem; font-size: 0.82rem; border: none; font-weight: 600; transition: background .2s, transform .15s; }
    .result-card .btn-detail:hover { background: var(--primary-dark); color: white; transform: scale(1.02); }
    .result-card .btn-detail:active { transform: scale(0.97); }
    .result-card .lokasi-badge { background: rgba(0,0,0,0.05); color: #555; padding: 0.2rem 0.6rem; border-radius: 50px; font-size: 0.72rem; display: inline-flex; align-items: center; gap: 0.2rem; font-weight: 500; white-space: nowrap; }
    .info-row-card { display: flex; align-items: flex-start; gap: 0.45rem; font-size: 0.82rem; color: #444; line-height: 1.45; }
    .info-row-card .bx { color: var(--primary); font-size: 0.95rem; margin-top: 0.2rem; flex-shrink: 0; }
    .info-row-card .bx { color: var(--primary); font-size: 0.95rem; margin-top: 0.2rem; flex-shrink: 0; }
    .empty-state { text-align: center; padding: 3rem 1rem; color: #555; }
    .empty-state i { font-size: 4rem; color: #bbb; margin-bottom: 1rem; display: block; }
    .site-footer { background: var(--primary); color: rgba(255,255,255,.85); padding: 1.5rem 0; margin-top: 2rem; border-radius: 1.5rem 1.5rem 0 0; }
    .site-footer a { color: rgba(255,255,255,.9); text-decoration: none; }
    .site-footer a:hover { color: #fff; text-decoration: underline; }
    .section-title { font-weight: 700; color: #222; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .section-title i { color: var(--primary); }
    .stat-badge { background: rgba(44,68,99,0.12); color: #2C4463; padding: 0.3rem 0.7rem; border-radius: 50px; font-size: 0.75rem; font-weight: 700; display: inline-block; }
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
    @media (max-width: 767px) { .maps-fab { display: none; } }
    @media (min-width: 768px) { .maps-fab { display: flex; } }
    @media (max-width: 767px) {
        .page-header { padding: 1.8rem 1rem 1.5rem; }
        .page-header h1 { font-size: 1.4rem; }
        .result-card .card-title { font-size: 1.1rem; }
        .result-card .btn-detail { font-size: 0.9rem; padding: 0.45rem 1rem; }
        .result-card .lokasi-badge { font-size: 0.8rem; }
        .info-row-card { font-size: 0.9rem; }
        .site-footer { margin-bottom: 0; border-radius: 0; padding: 0.75rem 0; margin-top: 1rem; }
    }
</style>

<div class="page-header">
    <h1><i class="bx bx-search me-1"></i>Cari Gereja</h1>
    <p>Temukan gereja berdasarkan nama, kota, hari misa, atau kategori</p>
</div>

<div class="page-content-wrap pb-1">
    <div class="filter-card">
        <div class="card-body">
            <form action="<?= BASEURL; ?>cari" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-5 col-lg-4">
                        <label><i class="bx bx-search me-1"></i>Kata Kunci</label>
                        <input type="text" name="q" class="form-control w-100 mt-1" placeholder="Nama gereja atau kota..." value="<?= htmlspecialchars($keyword); ?>">
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <label><i class="bx bx-calendar me-1"></i>Hari Misa</label>
                        <select name="hari" class="form-select w-100 mt-1">
                            <option value="">Semua Hari</option>
                            <?php foreach ($hariList as $h): ?>
                            <option value="<?= $h; ?>" <?= $selectedHari == $h ? 'selected' : ''; ?>><?= $h; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-2 col-lg-2">
                        <label><i class="bx bx-category me-1"></i>Kategori</label>
                        <select name="kategori" class="form-select w-100 mt-1">
                            <option value="">Semua</option>
                            <?php foreach ($kategoriList as $k): ?>
                            <option value="<?= $k; ?>" <?= $selectedKategori == $k ? 'selected' : ''; ?>><?= $k; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-filter flex-fill"><i class="bx bx-filter me-1"></i>Filter</button>
                        <a href="<?= BASEURL; ?>cari" class="btn-reset flex-fill"><i class="bx bx-reset"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($gerejaList)): ?>
        <div class="empty-state">
            <i class="bx bx-data"></i>
            <h5 class="fw-bold mt-3">Tidak ada gereja ditemukan</h5>
            <p class="mb-3">Coba ubah kata kunci atau filter pencarian Anda.</p>
            <a href="<?= BASEURL; ?>cari" class="btn btn-filter"><i class="bx bx-x me-1"></i>Reset Filter</a>
        </div>
    <?php else: ?>
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bx bx-church" style="color:var(--primary);"></i>
            <span class="fw-semibold" style="font-size:0.9rem;">
                <?= count($gerejaList); ?> gereja ditemukan
                <?php if ($keyword): ?>
                    <span class="stat-badge ms-1"><i class="bx bx-search"></i> "<?= htmlspecialchars($keyword); ?>"</span>
                <?php endif; ?>
                <?php if ($selectedHari): ?>
                    <span class="stat-badge ms-1"><i class="bx bx-calendar"></i> <?= htmlspecialchars($selectedHari); ?></span>
                <?php endif; ?>
                <?php if ($selectedKategori): ?>
                    <span class="stat-badge ms-1"><i class="bx bx-category"></i> <?= htmlspecialchars($selectedKategori); ?></span>
                <?php endif; ?>
            </span>
        </div>
        <div class="row g-3">
            <?php foreach ($gerejaList as $g): ?>
            <div class="col-12 col-md-4 col-xl-3">
                <div class="card result-card">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($g->nama_gereja); ?></h5>

                        <div class="info-row-card mb-1">
                            <i class="bx bx-map-pin"></i>
                            <span><?= htmlspecialchars($g->alamat); ?></span>
                        </div>

                        <div class="d-flex flex-wrap gap-1 mb-1">
                            <span class="lokasi-badge"><i class="bx bx-map"></i><?= htmlspecialchars($g->provinsi); ?></span>
                            <span class="lokasi-badge"><i class="bx bx-building"></i><?= htmlspecialchars($g->kabupaten_kota); ?></span>
                            <?php if ($g->kecamatan): ?>
                            <span class="lokasi-badge"><i class="bx bx-detail"></i><?= htmlspecialchars($g->kecamatan); ?></span>
                            <?php endif; ?>
                            <?php if ($g->kelurahan): ?>
                            <span class="lokasi-badge"><i class="bx bx-home"></i><?= htmlspecialchars($g->kelurahan); ?></span>
                            <?php endif; ?>
                        </div>

                        <?php if ($g->kontak_telepon): ?>
                        <div class="info-row-card mb-1">
                            <i class="bx bx-phone"></i>
                            <span><?= htmlspecialchars($g->kontak_telepon); ?></span>
                            <a href="tel:<?= htmlspecialchars($g->kontak_telepon); ?>" class="ms-auto text-decoration-none" style="color:var(--primary); font-weight:600; font-size:0.75rem;" title="Hubungi"><i class="bx bx-phone-call"></i></a>
                        </div>
                        <?php endif; ?>

                        <a href="<?= BASEURL; ?>gereja/<?= htmlspecialchars($g->slug ? $g->slug : $g->id); ?>" class="btn btn-detail mt-auto">
                            <i class="bx bx-detail me-1"></i>Lihat Detail & Jadwal
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<a href="<?= BASEURL; ?>maps" class="maps-fab" title="Lihat Peta Gereja"><i class="bx bx-map"></i></a>
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

<?php $this->view('layouts/guest_bottom_nav', array('activeMenu' => 'cari')); ?>
<?php $this->view('layouts/guest_closeTag'); ?>
