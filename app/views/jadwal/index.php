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
    .jadwal-card { border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 1rem; transition: box-shadow 0.2s; }
    .jadwal-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,0.12); }
    .jadwal-card .card-body { padding: 1rem 1.15rem; }
    .jadwal-card .card-title { font-size: 1.05rem; font-weight: 700; color: #2C4463; }
    .jadwal-item { display: flex; align-items: center; gap: 0.5rem; padding: 0.45rem 0; border-bottom: 1px solid #f0f0f0; }
    .jadwal-item:last-child { border-bottom: none; }
    .jadwal-day { font-weight: 600; font-size: 0.82rem; color: #2C4463; flex: 0 0 3.2rem; }
    .jadwal-time { font-weight: 700; font-size: 0.85rem; color: var(--primary); flex: 0 0 2.8rem; background: rgba(44,68,99,0.08); padding: 0.1rem 0.35rem; border-radius: 6px; text-align: center; }
    .jadwal-ket { font-size: 0.78rem; color: #555; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .badge-kategori { background: rgba(44,68,99,0.1); color: #2C4463; font-weight: 700; font-size: 0.62rem; padding: 0.15rem 0.45rem; border-radius: 4px; white-space: nowrap; flex-shrink: 0; }
    .gereja-lokasi { font-size: 0.8rem; color: #666; }
    .lokasi-badge { background: rgba(0,0,0,0.06); color: #444; padding: 0.2rem 0.6rem; border-radius: 50px; font-size: 0.7rem; display: inline-block; font-weight: 500; }
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
    .empty-state { text-align: center; padding: 3rem 1rem; color: #555; }
    .empty-state i { font-size: 4rem; color: #bbb; margin-bottom: 1rem; display: block; }
    .site-footer { background: var(--primary); color: rgba(255,255,255,.85); padding: 1.5rem 0; margin-top: 2rem; border-radius: 1.5rem 1.5rem 0 0; }
    .site-footer a { color: rgba(255,255,255,.9); text-decoration: none; }
    .site-footer a:hover { color: #fff; text-decoration: underline; }
    @media (max-width: 767px) {
        .page-header { padding: 1.8rem 1rem 1.5rem; }
        .page-header h1 { font-size: 1.3rem; }
        .site-footer { margin-bottom: 0; border-radius: 0; padding: 0.75rem 0; margin-top: 1rem; }
        .jadwal-item { gap: 0.35rem; }
        .jadwal-day { flex: 0 0 2.6rem; font-size: 0.75rem; }
        .jadwal-time { flex: 0 0 2.4rem; font-size: 0.8rem; }
        .badge-kategori { font-size: 0.55rem; padding: 0.12rem 0.35rem; }
    }
</style>

<div class="page-header">
    <h1><i class="bx bx-calendar me-1"></i>Jadwal Misa</h1>
    <p>Jadwal misa dari gereja-gereja Katolik di Indonesia</p>
</div>

<div class="page-content-wrap pb-1">
    <div class="filter-card">
        <div class="card-body">
            <form action="<?= BASEURL; ?>jadwal" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-6 col-md-3">
                        <label><i class="bx bx-map me-1"></i>Provinsi</label>
                        <select name="provinsi" class="form-select w-100 mt-1">
                            <option value="">Semua Provinsi</option>
                            <?php foreach ($provinsiList as $p): ?>
                            <option value="<?= htmlspecialchars($p); ?>" <?= $selectedProvinsi == $p ? 'selected' : ''; ?>><?= htmlspecialchars($p); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label><i class="bx bx-building me-1"></i>Kabupaten/Kota</label>
                        <select name="kabupaten" class="form-select w-100 mt-1">
                            <option value="">Semua Kabupaten/Kota</option>
                            <?php foreach ($kabupatenList as $k): ?>
                            <option value="<?= htmlspecialchars($k); ?>" <?= $selectedKabupaten == $k ? 'selected' : ''; ?>><?= htmlspecialchars($k); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label><i class="bx bx-calendar me-1"></i>Hari</label>
                        <select name="hari" class="form-select w-100 mt-1">
                            <option value="">Semua Hari</option>
                            <?php foreach ($hariList as $h): ?>
                            <option value="<?= $h; ?>" <?= $selectedHari == $h ? 'selected' : ''; ?>><?= $h; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3 d-flex gap-2 align-items-end justify-content-md-end">
                        <button type="submit" class="btn btn-filter flex-fill flex-md-grow-0 px-md-3"><i class="bx bx-filter me-1"></i>Filter</button>
                        <a href="<?= BASEURL; ?>jadwal" class="btn-reset flex-fill flex-md-grow-0 px-md-2"><i class="bx bx-reset"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($gerejaList)): ?>
        <div class="empty-state">
            <i class="bx bx-calendar-x"></i>
            <h5 class="fw-bold mt-3">Tidak ada jadwal ditemukan</h5>
            <p class="mb-3">Coba ubah filter atau pilih provinsi/kabupaten lain.</p>
            <a href="<?= BASEURL; ?>jadwal" class="btn btn-filter"><i class="bx bx-x me-1"></i>Reset Filter</a>
        </div>
    <?php else: ?>
        <div class="d-flex align-items-baseline gap-2 flex-wrap mb-3">
            <span class="fw-semibold" style="font-size:0.9rem;color:#333;">
                <i class="bx bx-calendar-event" style="color:var(--primary);"></i>
                <?= count($gerejaList); ?> gereja
            </span>
            <?php if ($selectedProvinsi): ?>
                <span class="stat-badge"><i class="bx bx-map"></i> <?= htmlspecialchars($selectedProvinsi); ?></span>
            <?php endif; ?>
            <?php if ($selectedKabupaten): ?>
                <span class="stat-badge"><i class="bx bx-building"></i> <?= htmlspecialchars($selectedKabupaten); ?></span>
            <?php endif; ?>
            <?php if ($selectedHari): ?>
                <span class="stat-badge"><i class="bx bx-calendar"></i> <?= htmlspecialchars($selectedHari); ?></span>
            <?php endif; ?>
        </div>
        <?php foreach ($gerejaList as $g): ?>
        <div class="jadwal-card">
            <div class="card-body">
                <a href="<?= BASEURL; ?>gereja/<?= htmlspecialchars($g->slug ? $g->slug : $g->id); ?>" class="text-decoration-none">
                    <h5 class="card-title mb-1"><?= htmlspecialchars($g->nama_gereja); ?></h5>
                </a>
                <p class="gereja-lokasi mb-2">
                    <i class="bx bx-map-pin me-1" style="color:var(--primary);"></i><?= htmlspecialchars($g->alamat); ?>
                    <span class="lokasi-badge ms-1"><?= htmlspecialchars($g->provinsi); ?></span>
                    <span class="lokasi-badge ms-1"><?= htmlspecialchars($g->kabupaten_kota); ?></span>
                </p>
                <?php
                $hariUrut = array('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Spesial');
                $kelompok = array();
                foreach ($g->jadwal_list as $j) {
                    $kelompok[$j->hari][] = $j;
                }
                ?>
                <?php foreach ($hariUrut as $hari): if (isset($kelompok[$hari])): ?>
                    <?php foreach ($kelompok[$hari] as $j): ?>
                    <div class="jadwal-item">
                        <span class="jadwal-day"><?= $j->hari; ?></span>
                        <span class="jadwal-time"><?= date('H:i', strtotime($j->waktu_mulai)); ?></span>
                        <?php if ($j->keterangan): ?>
                            <span class="jadwal-ket"><?= htmlspecialchars($j->keterangan); ?></span>
                        <?php endif; ?>
                        <span class="badge-kategori ms-auto"><?= $j->kategori; ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
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

<?php $this->view('layouts/guest_bottom_nav', array('activeMenu' => 'jadwal')); ?>
<?php $this->view('layouts/guest_closeTag'); ?>
