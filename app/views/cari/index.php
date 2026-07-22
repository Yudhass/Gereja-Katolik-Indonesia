<?php $this->view('layouts/guest_openTag', array('title' => $title)); ?>

<style>
    .page-header { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; padding: 1.5rem 1rem; text-align: center; }
    .page-header h1 { font-size: 1.4rem; font-weight: 700; color: #fff; }
    .filter-card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden; }
    .filter-card .card-body { padding: 1rem; }
    .filter-label { font-size: 0.8rem; font-weight: 700; color: #333; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.3rem; }
    .form-select-sm-custom { font-size: 0.85rem; border-radius: 8px; border: 1px solid #bbb; padding: 0.35rem 0.75rem; }
    .btn-filter { background: var(--primary); color: white; border: none; border-radius: 8px; padding: 0.35rem 1rem; font-size: 0.85rem; }
    .btn-filter:hover { background: var(--primary-dark); color: white; }
    .result-card { border: none; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: transform 0.2s; }
    .result-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .result-card .card-body { padding: 0.85rem; }
    .result-card .card-title { font-size: 1rem; font-weight: 700; color: #2C4463; }
    .result-card .card-text { font-size: 0.82rem; color: #333; }
    .result-card .badge-gereja { background: rgba(44,68,99,0.12); color: #2C4463; font-weight: 700; font-size: 0.7rem; }
    .empty-state { text-align: center; padding: 3rem 1rem; color: #555; }
    .empty-state i { font-size: 4rem; color: #bbb; }
    @media (max-width: 767px) {
        .page-header h1 { font-size: 1.2rem; }
    }
</style>

<div class="page-header">
    <h1><i class="bx bx-search me-1"></i>Cari Gereja</h1>
</div>

<div class="container px-3 px-md-4 py-3 pb-5 mb-4">
    <div class="filter-card">
        <div class="card-body">
            <form action="<?= BASEURL; ?>cari" method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <div class="filter-label">Kata Kunci</div>
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="Nama gereja / kota..." value="<?= htmlspecialchars($keyword); ?>">
                </div>
                <div class="col-6 col-md-3">
                    <div class="filter-label">Hari</div>
                    <select name="hari" class="form-select form-select-sm form-select-sm-custom">
                        <option value="">Semua Hari</option>
                        <?php foreach ($hariList as $h): ?>
                        <option value="<?= $h; ?>" <?= $selectedHari == $h ? 'selected' : ''; ?>><?= $h; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <div class="filter-label">Kategori</div>
                    <select name="kategori" class="form-select form-select-sm form-select-sm-custom">
                        <option value="">Semua</option>
                        <?php foreach ($kategoriList as $k): ?>
                        <option value="<?= $k; ?>" <?= $selectedKategori == $k ? 'selected' : ''; ?>><?= $k; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-filter w-100"><i class="bx bx-filter me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($gerejaList)): ?>
        <div class="empty-state">
            <i class="bx bx-data"></i>
            <h5 class="mt-3">Tidak ada gereja ditemukan</h5>
            <p>Coba ubah kata kunci atau filter pencarian Anda.</p>
            <a href="<?= BASEURL; ?>cari" class="btn btn-outline-church btn-sm">Reset Filter</a>
        </div>
    <?php else: ?>
        <p class="text-muted mb-3" style="font-size:0.85rem;">
            Menampilkan <strong><?= count($gerejaList); ?></strong> gereja
            <?php if ($keyword): ?> dengan kata kunci "<strong><?= htmlspecialchars($keyword); ?></strong>"<?php endif; ?>
        </p>
        <div class="row g-3">
            <?php foreach ($gerejaList as $g): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <a href="<?= BASEURL; ?>gereja/<?= htmlspecialchars($g->slug ? $g->slug : $g->id); ?>" class="text-decoration-none">
                    <div class="card result-card">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($g->nama_gereja); ?></h5>
                            <p class="card-text mb-1"><i class="bx bx-map-pin text-secondary me-1"></i><?= htmlspecialchars($g->alamat); ?></p>
                            <?php if ($g->kontak_telepon): ?>
                                <p class="card-text mb-0"><i class="bx bx-phone text-secondary me-1"></i><?= htmlspecialchars($g->kontak_telepon); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php $this->view('layouts/guest_bottom_nav', array('activeMenu' => 'cari')); ?>
<?php $this->view('layouts/guest_closeTag'); ?>
