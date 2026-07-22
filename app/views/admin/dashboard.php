<?php $this->view('layouts/openTag', array('title' => $title)); ?>
<style>
    .welcome-section { background: linear-gradient(135deg, #2C4463 0%, #1A2D47 100%); border-radius:14px; padding:1.5rem 2rem; color:#fff; }
    .stat-card { transition:transform .2s ease, box-shadow .2s ease; border-radius:12px; border:none; box-shadow:0 2px 12px rgba(0,0,0,.06); }
    .stat-card:hover { transform:translateY(-4px); box-shadow:0 8px 25px rgba(0,0,0,.12); }
    .stat-value { font-size:1.75rem; font-weight:700; }
    .icon-box { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
    .dash-card { border-radius:12px; border:none; box-shadow:0 2px 12px rgba(0,0,0,.06); }
    .dash-card .table thead th { background:#2C4463; color:#fff; font-weight:600; font-size:.8rem; border-color:#1A2D47; padding:.6rem .75rem; }
    .dash-card .table tbody td { padding:.6rem .75rem; vertical-align:middle; font-size:.85rem; }
    .dash-card .table-hover tbody tr:hover { background:rgba(44,68,99,.04); }
    .dash-card .card-body-empty { padding:2rem; text-align:center; color:#adb5bd; font-size:.9rem; }
</style>
<body>
<div class="wrapper">
    <?php $this->view('layouts/admin_sidebar', array('sidebar' => 'dashboard')); ?>
    <?php $this->view('layouts/navbar'); ?>
    <div class="page-wrapper">
        <div class="page-content">

            <div class="welcome-section mb-4 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0 fw-bold"><i class="bx bx-dashboard me-2"></i>Dashboard Admin</h5>
                    <p class="mb-0 mt-1 opacity-75 small">Selamat datang di panel admin Gereja Katolik Indonesia</p>
                </div>
                <div class="d-none d-sm-block">
                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                        <i class="bx bx-calendar me-1"></i><?= date('d F Y'); ?>
                    </span>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6 col-sm-4 col-xl-4">
                    <div class="card stat-card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-box me-2" style="background:rgba(44,68,99,.12);">
                                    <i class="bx bx-church" style="color:#2C4463;"></i>
                                </div>
                            </div>
                            <div class="stat-value" style="color:#2C4463;"><?= $totalGereja; ?></div>
                            <div class="stat-label text-secondary mt-1">Total Gereja</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-xl-4">
                    <div class="card stat-card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-box me-2" style="background:rgba(218,165,32,.12);">
                                    <i class="bx bx-calendar-event" style="color:#DAA520;"></i>
                                </div>
                            </div>
                            <div class="stat-value" style="color:#DAA520;"><?= $totalJadwal; ?></div>
                            <div class="stat-label text-secondary mt-1">Total Jadwal Misa</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-xl-4">
                    <div class="card stat-card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-box me-2" style="background:rgba(220,53,69,.12);">
                                    <i class="bx bx-message-dots" style="color:#dc3545;"></i>
                                </div>
                            </div>
                            <div class="stat-value" style="color:#dc3545;"><?= $totalSaranPending; ?></div>
                            <div class="stat-label text-secondary mt-1">Saran Pending</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12 col-xl-6">
                    <div class="card dash-card">
                        <div class="card-header d-flex align-items-center" style="background:#fff; border-bottom:1px solid rgba(0,0,0,.06);">
                            <h6 class="fw-bold mb-0"><i class="bx bx-building-house me-1" style="color:#2C4463;"></i>Gereja Terbaru</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nama Gereja</th>
                                            <th>Kontak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($gerejaTerbaru)): ?>
                                        <tr><td colspan="2" class="text-center py-3 text-muted">Belum ada data gereja.</td></tr>
                                        <?php else: ?>
                                        <?php foreach ($gerejaTerbaru as $g): ?>
                                        <tr>
                                            <td class="fw-semibold"><?= htmlspecialchars($g->nama_gereja); ?></td>
                                            <td><?= htmlspecialchars($g->kontak_telepon ? $g->kontak_telepon : '-'); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6">
                    <div class="card dash-card">
                        <div class="card-header d-flex align-items-center" style="background:#fff; border-bottom:1px solid rgba(0,0,0,.06);">
                            <h6 class="fw-bold mb-0"><i class="bx bx-message-dots me-1" style="color:#dc3545;"></i>Saran Terbaru</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Gereja</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($saranTerbaru)): ?>
                                        <tr><td colspan="3" class="text-center py-3 text-muted">Belum ada saran masuk.</td></tr>
                                        <?php else: ?>
                                        <?php foreach ($saranTerbaru as $s): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($s->nama_gereja); ?></td>
                                            <td>
                                                <?php if ($s->status == 'Pending'): ?>
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                <?php elseif ($s->status == 'Approved'): ?>
                                                    <span class="badge bg-success">Approved</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Rejected</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-muted" style="font-size:.8rem;"><?= date('d/m/Y', strtotime($s->created_at)); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php $this->view('layouts/closeTag'); ?>
