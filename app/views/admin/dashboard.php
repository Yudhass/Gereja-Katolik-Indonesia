<?php $this->view('layouts/openTag', array('title' => $title)); ?>
<?php
$bulanIndo = array(
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
);
$hariIndo = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
$tanggalHariIni = $hariIndo[(int)date('w')] . ', ' . date('j') . ' ' . $bulanIndo[(int)date('n')] . ' ' . date('Y');
?>
<style>
    .dash-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
        padding-bottom: 1.1rem;
        border-bottom: 1px solid var(--gki-border);
    }
    .dash-date-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--gki-primary);
        background: var(--gki-primary-light);
        padding: 0.25rem 0.7rem;
        border-radius: 999px;
        margin-bottom: 0.5rem;
    }
    .dash-title {
        font-size: clamp(1.3rem, 1.1rem + 0.7vw, 1.65rem);
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--gki-text);
        margin: 0 0 0.2rem;
    }
    .dash-subtitle {
        font-size: 0.875rem;
        color: var(--gki-muted);
        margin: 0;
    }
    .dash-quick-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    /* Metric Cards */
    .kpi-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        background: var(--gki-surface);
        border: 1px solid var(--gki-border);
        border-radius: 14px;
        padding: 1.2rem 1.25rem;
        text-decoration: none;
        color: inherit;
        transition: border-color 0.16s ease, box-shadow 0.16s ease, transform 0.16s ease;
    }
    .kpi-card:hover {
        border-color: var(--gki-border-strong);
        box-shadow: var(--gki-shadow);
        transform: translateY(-2px);
        color: inherit;
    }
    .kpi-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.9rem;
    }
    .kpi-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--gki-muted);
    }
    .kpi-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .kpi-icon-primary { background: var(--gki-primary-light); color: var(--gki-primary); }
    .kpi-icon-accent { background: var(--gki-accent-soft); color: var(--gki-accent); }
    .kpi-icon-danger { background: #FEE2E2; color: #B91C1C; }

    .kpi-number {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -0.03em;
        color: var(--gki-text);
        font-variant-numeric: tabular-nums;
        margin-bottom: 0.85rem;
    }
    .kpi-foot {
        margin-top: auto;
        padding-top: 0.75rem;
        border-top: 1px solid var(--gki-bg);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--gki-primary);
    }
    .kpi-card.has-pending {
        border-color: #FECACA;
    }
    .kpi-card.has-pending .kpi-number {
        color: #B91C1C;
    }

    /* Data Panels */
    .panel-card {
        background: var(--gki-surface);
        border: 1px solid var(--gki-border);
        border-radius: 14px;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--gki-border);
    }
    .panel-head-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--gki-text);
    }
    .panel-head-title i {
        color: var(--gki-primary);
        font-size: 1.15rem;
    }
    .panel-link {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--gki-primary);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
    }
    .panel-link:hover {
        color: var(--gki-primary-hover);
        text-decoration: underline;
    }

    /* Clean Light-Header Table inside Dashboard */
    .dash-table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
        font-variant-numeric: tabular-nums;
    }
    .dash-table thead th {
        background: var(--gki-bg) !important;
        color: var(--gki-muted) !important;
        border-bottom: 1px solid var(--gki-border) !important;
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.65rem 1.25rem;
    }
    .dash-table tbody td {
        padding: 0.85rem 1.25rem;
        border-bottom: 1px solid var(--gki-bg);
        vertical-align: middle;
        font-size: 0.86rem;
        color: var(--gki-text);
    }
    .dash-table tbody tr:last-child td {
        border-bottom: none;
    }
    .dash-table tbody tr:hover {
        background: var(--gki-primary-lighter);
    }
    .church-cell-name {
        font-weight: 700;
        color: var(--gki-text);
        line-height: 1.35;
    }
    .church-cell-loc {
        font-size: 0.78rem;
        color: var(--gki-muted);
        margin-top: 0.1rem;
    }

    /* Saran Feed List */
    .saran-list {
        display: flex;
        flex-direction: column;
    }
    .saran-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.85rem;
        padding: 0.9rem 1.25rem;
        border-bottom: 1px solid var(--gki-bg);
        transition: background-color 0.14s ease;
    }
    .saran-row:last-child {
        border-bottom: none;
    }
    .saran-row:hover {
        background: var(--gki-primary-lighter);
    }
    .saran-main {
        min-width: 0;
        flex: 1;
    }
    .saran-church {
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--gki-text);
        margin-bottom: 0.15rem;
    }
    .saran-meta {
        font-size: 0.78rem;
        color: var(--gki-muted);
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        align-items: center;
    }
    .saran-side {
        text-align: right;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.25rem;
    }
    .saran-date {
        font-size: 0.75rem;
        color: var(--gki-muted);
        font-variant-numeric: tabular-nums;
    }

    /* Soft Semantic Status Pills */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
    }
    .status-pending { background: #FEF3C7; color: #92400E; }
    .status-approved { background: #DCFCE7; color: #166534; }
    .status-rejected { background: #FEE2E2; color: #991B1B; }

    .dash-empty {
        padding: 2.5rem 1.25rem;
        text-align: center;
        color: var(--gki-muted);
        font-size: 0.86rem;
    }
    .dash-empty i {
        font-size: 2.25rem;
        display: block;
        margin-bottom: 0.45rem;
        opacity: 0.6;
    }
</style>
<body>
<div class="wrapper">
    <?php $this->view('layouts/admin_sidebar', array('sidebar' => 'dashboard')); ?>
    <?php $this->view('layouts/navbar'); ?>
    <div class="page-wrapper">
        <div class="page-content">

            <div class="dash-header">
                <div>
                    <div class="dash-date-pill">
                        <i class="bx bx-calendar" aria-hidden="true"></i>
                        <span><?= htmlspecialchars($tanggalHariIni); ?></span>
                    </div>
                    <h1 class="dash-title">Dashboard Admin</h1>
                    <p class="dash-subtitle">Pantau data gereja, jadwal misa, dan laporan koreksi dari pengunjung.</p>
                </div>
                <div class="dash-quick-actions">
                    <a href="<?= BASEURL; ?>admin/gereja" class="btn btn-custom-primary btn-sm">
                        <i class="bx bx-church me-1" aria-hidden="true"></i>Kelola Gereja
                    </a>
                    <a href="<?= BASEURL; ?>admin/jadwal" class="btn btn-custom-outline btn-sm">
                        <i class="bx bx-calendar-event me-1" aria-hidden="true"></i>Kelola Jadwal
                    </a>
                    <a href="<?= BASEURL; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-custom-outline btn-sm">
                        <i class="bx bx-link-external me-1" aria-hidden="true"></i>Lihat Situs
                    </a>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-lg-4">
                    <a href="<?= BASEURL; ?>admin/gereja" class="kpi-card">
                        <div class="kpi-top">
                            <span class="kpi-label">Total Gereja</span>
                            <span class="kpi-icon kpi-icon-primary" aria-hidden="true"><i class="bx bx-church"></i></span>
                        </div>
                        <div class="kpi-number"><?= number_format((int)$totalGereja, 0, ',', '.'); ?></div>
                        <div class="kpi-foot">
                            <span>Buka daftar gereja</span>
                            <i class="bx bx-chevron-right" aria-hidden="true"></i>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                    <a href="<?= BASEURL; ?>admin/jadwal" class="kpi-card">
                        <div class="kpi-top">
                            <span class="kpi-label">Total Jadwal Misa</span>
                            <span class="kpi-icon kpi-icon-accent" aria-hidden="true"><i class="bx bx-calendar-event"></i></span>
                        </div>
                        <div class="kpi-number"><?= number_format((int)$totalJadwal, 0, ',', '.'); ?></div>
                        <div class="kpi-foot">
                            <span>Atur jadwal misa</span>
                            <i class="bx bx-chevron-right" aria-hidden="true"></i>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-lg-4">
                    <a href="<?= BASEURL; ?>admin/saran" class="kpi-card <?= (int)$totalSaranPending > 0 ? 'has-pending' : ''; ?>">
                        <div class="kpi-top">
                            <span class="kpi-label">Saran Menunggu Tinjauan</span>
                            <span class="kpi-icon kpi-icon-danger" aria-hidden="true"><i class="bx bx-message-dots"></i></span>
                        </div>
                        <div class="kpi-number"><?= number_format((int)$totalSaranPending, 0, ',', '.'); ?></div>
                        <div class="kpi-foot">
                            <span><?= (int)$totalSaranPending > 0 ? 'Perlu ditinjau sekarang' : 'Semua saran sudah ditangani'; ?></span>
                            <i class="bx bx-chevron-right" aria-hidden="true"></i>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12 col-xl-7">
                    <section class="panel-card" aria-labelledby="gerejaTerbaruHeading">
                        <div class="panel-head">
                            <h2 class="panel-head-title" id="gerejaTerbaruHeading">
                                <i class="bx bx-building-house" aria-hidden="true"></i>Gereja Terbaru
                            </h2>
                            <a href="<?= BASEURL; ?>admin/gereja" class="panel-link">
                                Lihat semua <i class="bx bx-chevron-right" aria-hidden="true"></i>
                            </a>
                        </div>
                        <?php if (empty($gerejaTerbaru)): ?>
                        <div class="dash-empty">
                            <i class="bx bx-church" aria-hidden="true"></i>
                            Belum ada data gereja yang ditambahkan.
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Gereja &amp; Wilayah</th>
                                        <th>Kontak</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($gerejaTerbaru as $g): ?>
                                    <?php $wilayah = implode(', ', array_filter(array($g->kabupaten_kota, $g->provinsi))); ?>
                                    <tr>
                                        <td>
                                            <div class="church-cell-name"><?= htmlspecialchars($g->nama_gereja); ?></div>
                                            <?php if ($wilayah): ?>
                                            <div class="church-cell-loc"><?= htmlspecialchars($wilayah); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($g->kontak_telepon ? $g->kontak_telepon : '-'); ?></td>
                                        <td class="text-end text-nowrap">
                                            <a href="<?= BASEURL; ?>admin/gereja/edit/<?= $g->id; ?>" class="btn btn-custom-outline btn-sm py-1 px-2">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </section>
                </div>

                <div class="col-12 col-xl-5">
                    <section class="panel-card" aria-labelledby="saranTerbaruHeading">
                        <div class="panel-head">
                            <h2 class="panel-head-title" id="saranTerbaruHeading">
                                <i class="bx bx-message-dots" aria-hidden="true"></i>Saran Koreksi Terbaru
                            </h2>
                            <a href="<?= BASEURL; ?>admin/saran" class="panel-link">
                                Lihat semua <i class="bx bx-chevron-right" aria-hidden="true"></i>
                            </a>
                        </div>
                        <?php if (empty($saranTerbaru)): ?>
                        <div class="dash-empty">
                            <i class="bx bx-message-check" aria-hidden="true"></i>
                            Belum ada saran koreksi masuk.
                        </div>
                        <?php else: ?>
                        <div class="saran-list">
                            <?php foreach ($saranTerbaru as $s): ?>
                            <div class="saran-row">
                                <div class="saran-main">
                                    <div class="saran-church"><?= htmlspecialchars($s->nama_gereja); ?></div>
                                    <div class="saran-meta">
                                        <span><?= htmlspecialchars(!empty($s->nama_pengunjung) ? $s->nama_pengunjung : 'Pengunjung'); ?></span>
                                        <?php if (!empty($s->saran_hari) || !empty($s->saran_waktu)): ?>
                                        <span>&bull;</span>
                                        <span>
                                            <?= htmlspecialchars(!empty($s->saran_hari) ? $s->saran_hari : ''); ?>
                                            <?= !empty($s->saran_waktu) ? date('H:i', strtotime($s->saran_waktu)) . ' WIB' : ''; ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="saran-side">
                                    <?php if ($s->status == 'Pending'): ?>
                                        <span class="status-pill status-pending">Pending</span>
                                    <?php elseif ($s->status == 'Approved'): ?>
                                        <span class="status-pill status-approved">Disetujui</span>
                                    <?php else: ?>
                                        <span class="status-pill status-rejected">Ditolak</span>
                                    <?php endif; ?>
                                    <span class="saran-date"><?= date('d/m/Y', strtotime($s->created_at)); ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </section>
                </div>
            </div>

        </div>
    </div>
</div>
<?php $this->view('layouts/closeTag'); ?>
