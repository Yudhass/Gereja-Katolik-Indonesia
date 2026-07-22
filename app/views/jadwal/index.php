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
    .jadwal-card { border: none; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 1rem; }
    .jadwal-card .card-body { padding: 0.85rem; }
    .jadwal-card .card-title { font-size: 1rem; font-weight: 700; color: #2C4463; }
    .jadwal-item { display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0; border-bottom: 1px dashed #eee; }
    .jadwal-item:last-child { border-bottom: none; }
    .jadwal-day { font-weight: 600; font-size: 0.82rem; color: #2C4463; min-width: 3.5rem; }
    .jadwal-time { font-weight: 700; font-size: 0.85rem; color: #2C4463; min-width: 3.5rem; }
    .jadwal-ket { font-size: 0.78rem; color: #555; }
    .badge-kategori { background: rgba(44,68,99,0.1); color: #2C4463; font-weight: 700; font-size: 0.65rem; padding: 0.2rem 0.5rem; border-radius: 4px; white-space: nowrap; }
    .empty-state { text-align: center; padding: 3rem 1rem; color: #555; }
    .empty-state i { font-size: 4rem; color: #bbb; }
    .gereja-lokasi { font-size: 0.8rem; color: #666; }
    @media (max-width: 767px) {
        .page-header h1 { font-size: 1.2rem; }
    }
</style>

<div class="page-header">
    <h1><i class="bx bx-calendar me-1"></i>Jadwal Misa</h1>
</div>

<div class="container px-3 px-md-4 py-3 pb-5 mb-4">
    <div class="filter-card">
        <div class="card-body">
            <form action="<?= BASEURL; ?>jadwal" method="GET" class="row g-2 align-items-end">
                <div class="col-6 col-md-3">
                    <div class="filter-label">Provinsi</div>
                    <select name="provinsi" class="form-select form-select-sm form-select-sm-custom">
                        <option value="">Semua Provinsi</option>
                        <?php foreach ($provinsiList as $p): ?>
                        <option value="<?= htmlspecialchars($p); ?>" <?= $selectedProvinsi == $p ? 'selected' : ''; ?>><?= htmlspecialchars($p); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <div class="filter-label">Kabupaten/Kota</div>
                    <select name="kabupaten" class="form-select form-select-sm form-select-sm-custom">
                        <option value="">Semua Kabupaten/Kota</option>
                        <?php foreach ($kabupatenList as $k): ?>
                        <option value="<?= htmlspecialchars($k); ?>" <?= $selectedKabupaten == $k ? 'selected' : ''; ?>><?= htmlspecialchars($k); ?></option>
                        <?php endforeach; ?>
                    </select>
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
                    <button type="submit" class="btn btn-filter w-100"><i class="bx bx-filter me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($gerejaList)): ?>
        <div class="empty-state">
            <i class="bx bx-calendar-x"></i>
            <h5 class="mt-3">Tidak ada jadwal ditemukan</h5>
            <p>Coba ubah filter atau pilih provinsi/kabupaten lain.</p>
            <a href="<?= BASEURL; ?>jadwal" class="btn btn-outline-church btn-sm">Reset Filter</a>
        </div>
    <?php else: ?>
        <p class="text-muted mb-3" style="font-size:0.85rem;">
            Menampilkan <strong><?= count($gerejaList); ?></strong> gereja dengan jadwal misa
            <?php if ($selectedHari): ?> hari <strong><?= htmlspecialchars($selectedHari); ?></strong><?php endif; ?>
        </p>
        <?php foreach ($gerejaList as $g): ?>
        <div class="jadwal-card">
            <div class="card-body">
                <a href="<?= BASEURL; ?>gereja/<?= htmlspecialchars($g->slug ? $g->slug : $g->id); ?>" class="text-decoration-none">
                    <h5 class="card-title mb-1"><?= htmlspecialchars($g->nama_gereja); ?></h5>
                </a>
                <p class="gereja-lokasi mb-2"><i class="bx bx-map-pin me-1" style="color:var(--primary);"></i><?= htmlspecialchars($g->alamat); ?></p>
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

<?php $this->view('layouts/guest_bottom_nav', array('activeMenu' => 'jadwal')); ?>
<?php $this->view('layouts/guest_closeTag'); ?>
