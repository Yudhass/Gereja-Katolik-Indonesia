<?php
$hasJadwalPageFilter = !empty($selectedProvinsi) || !empty($selectedKabupaten) || !empty($selectedHari);
$jadwalLabelParts = array();
if (!empty($selectedHari)) $jadwalLabelParts[] = 'Hari ' . decode_text($selectedHari);
if (!empty($selectedKabupaten)) $jadwalLabelParts[] = decode_text($selectedKabupaten);
elseif (!empty($selectedProvinsi)) $jadwalLabelParts[] = decode_text($selectedProvinsi);

$jadwalSeoTitle = !empty($jadwalLabelParts)
    ? 'Jadwal Misa ' . implode(' di ', $jadwalLabelParts) . ' | Info Gereja'
    : 'Jadwal Misa Gereja Katolik Indonesia — Harian, Mingguan & Hari Raya | Info Gereja';
$jadwalSeoDesc = !empty($jadwalLabelParts)
    ? 'Daftar jadwal misa ' . implode(' di ', $jadwalLabelParts) . ' dari ' . count($gerejaList) . ' gereja Katolik. Cek jam misa terbaru hari ini.'
    : 'Telusuri jadwal misa harian, Sabtu, Minggu, dan hari raya di gereja-gereja Katolik seluruh Indonesia berdasarkan provinsi, kota, dan hari.';

$jadwalSeo = array(
    'title' => $jadwalSeoTitle,
    'description' => $jadwalSeoDesc,
    'keywords' => 'jadwal misa, jadwal misa katolik, jadwal misa minggu, jadwal misa harian, jam misa gereja katolik indonesia',
    'canonical' => BASEURL . 'jadwal',
    'robots' => $hasJadwalPageFilter ? 'noindex, follow' : 'index, follow',
    'schema' => array(
        array(
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'Jadwal Misa Gereja Katolik Indonesia',
            'url' => BASEURL . 'jadwal',
            'description' => $jadwalSeoDesc,
        ),
        array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array(
                array('@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => BASEURL),
                array('@type' => 'ListItem', 'position' => 2, 'name' => 'Jadwal Misa', 'item' => BASEURL . 'jadwal'),
            ),
        ),
    ),
);
?>
<?php $this->view('layouts/guest_openTag', array('title' => $title, 'seo' => $jadwalSeo)); ?>

<style>
    .jadwal-list-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 420px), 1fr));
        gap: 1.05rem;
    }
    .jadwal-church-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 1.1rem 1.15rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        transition: border-color 0.16s ease, box-shadow 0.16s ease;
    }
    .jadwal-church-card:hover {
        border-color: var(--border-strong);
        box-shadow: var(--shadow-sm);
    }
    .jadwal-church-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
    }
    .jadwal-church-title {
        font-size: 1.02rem;
        font-weight: 700;
        color: var(--ink);
        margin: 0 0 0.25rem;
        line-height: 1.35;
    }
    .jadwal-church-title a {
        color: inherit;
    }
    .jadwal-church-title a:hover {
        color: var(--primary);
    }
    .jadwal-church-sub {
        font-size: 0.8125rem;
        color: var(--ink-secondary);
        margin: 0;
        display: flex;
        align-items: flex-start;
        gap: 0.35rem;
    }
    .jadwal-church-sub i {
        color: var(--ink-muted);
        margin-top: 0.15rem;
        flex-shrink: 0;
    }
    .jadwal-rows {
        border-top: 1px solid var(--border);
        padding-top: 0.55rem;
        display: flex;
        flex-direction: column;
    }
    .jadwal-row {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.45rem 0;
        border-bottom: 1px solid var(--surface-subtle);
        font-variant-numeric: tabular-nums;
    }
    .jadwal-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .jadwal-day {
        font-weight: 700;
        font-size: 0.82rem;
        color: var(--ink);
        flex: 0 0 4.25rem;
    }
    .jadwal-time {
        font-weight: 700;
        font-size: 0.82rem;
        color: var(--primary);
        background: var(--primary-soft);
        padding: 0.18rem 0.55rem;
        border-radius: 6px;
        flex-shrink: 0;
    }
    .jadwal-ket {
        font-size: 0.8rem;
        color: var(--ink-secondary);
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .jadwal-cat {
        margin-left: auto;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.16rem 0.5rem;
        border-radius: var(--radius-pill);
        background: var(--surface-subtle);
        color: var(--ink-secondary);
        flex-shrink: 0;
    }
</style>

<?php $this->view('layouts/guest_navbar', array('activeMenu' => 'jadwal')); ?>

<header class="gki-page-header">
    <div class="gki-container">
        <h1>Jadwal Misa</h1>
        <p>Telusuri jadwal misa harian, mingguan, dan hari raya di gereja Katolik seluruh Indonesia.</p>
    </div>
</header>

<main class="gki-container">
    <section class="gki-panel" aria-label="Filter jadwal misa">
        <form action="<?= BASEURL; ?>jadwal" method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-sm-6 col-md-3 gki-field">
                    <label for="jadwal_provinsi">Provinsi</label>
                    <select id="jadwal_provinsi" name="provinsi" class="gki-select">
                        <option value="">Semua Provinsi</option>
                        <?php foreach ($provinsiList as $p): ?>
                        <option value="<?= htmlspecialchars($p); ?>" <?= $selectedProvinsi == $p ? 'selected' : ''; ?>><?= htmlspecialchars($p); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3 gki-field">
                    <label for="jadwal_kabupaten">Kabupaten/Kota</label>
                    <select id="jadwal_kabupaten" name="kabupaten" class="gki-select">
                        <option value="">Semua Kabupaten/Kota</option>
                        <?php foreach ($kabupatenList as $k): ?>
                        <option value="<?= htmlspecialchars($k); ?>" <?= $selectedKabupaten == $k ? 'selected' : ''; ?>><?= htmlspecialchars($k); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3 gki-field">
                    <label for="jadwal_hari">Hari</label>
                    <select id="jadwal_hari" name="hari" class="gki-select">
                        <option value="">Semua Hari</option>
                        <?php foreach ($hariList as $h): ?>
                        <option value="<?= htmlspecialchars($h); ?>" <?= $selectedHari == $h ? 'selected' : ''; ?>><?= htmlspecialchars($h); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="gki-btn gki-btn-primary flex-fill">
                        <i class="bx bx-filter-alt" aria-hidden="true"></i>
                        <span>Terapkan</span>
                    </button>
                    <?php if ($hasJadwalPageFilter): ?>
                    <a href="<?= BASEURL; ?>jadwal" class="gki-btn gki-btn-outline" title="Reset filter" aria-label="Reset filter">
                        <i class="bx bx-x" aria-hidden="true"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </section>

    <?php if ($hasJadwalPageFilter): ?>
    <div class="gki-active-filters">
        <span class="gki-active-filters-label">Filter aktif:</span>
        <?php if ($selectedProvinsi): ?>
        <span class="gki-chip gki-chip-primary"><?= htmlspecialchars($selectedProvinsi); ?></span>
        <?php endif; ?>
        <?php if ($selectedKabupaten): ?>
        <span class="gki-chip gki-chip-primary"><?= htmlspecialchars($selectedKabupaten); ?></span>
        <?php endif; ?>
        <?php if ($selectedHari): ?>
        <span class="gki-chip gki-chip-accent"><?= htmlspecialchars($selectedHari); ?></span>
        <?php endif; ?>
        <a href="<?= BASEURL; ?>jadwal" class="gki-clear-link">Hapus semua</a>
    </div>
    <?php endif; ?>

    <?php if (empty($gerejaList)): ?>
    <div class="gki-empty">
        <i class="bx bx-calendar-x" aria-hidden="true"></i>
        <h3>Tidak ada jadwal ditemukan</h3>
        <p>Coba ubah filter hari atau pilih provinsi dan kabupaten/kota lain.</p>
        <a href="<?= BASEURL; ?>jadwal" class="gki-btn gki-btn-primary">
            <i class="bx bx-x" aria-hidden="true"></i>Reset Filter
        </a>
    </div>
    <?php else: ?>
    <div class="gki-section-head">
        <div class="gki-section-title-group">
            <h2 class="gki-section-title">Jadwal Tersedia</h2>
            <span class="gki-count-pill"><?= count($gerejaList); ?> gereja</span>
        </div>
    </div>

    <div class="jadwal-list-grid">
        <?php foreach ($gerejaList as $g): ?>
        <?php
            $lokasiText = implode(', ', array_filter(array($g->kabupaten_kota, $g->provinsi)));
            $hariUrut = array('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Spesial');
            $kelompok = array();
            foreach ($g->jadwal_list as $j) {
                $kelompok[$j->hari][] = $j;
            }
        ?>
        <article class="jadwal-church-card">
            <div class="jadwal-church-head">
                <div class="min-w-0">
                    <?php if ($lokasiText): ?>
                    <div class="gki-church-region mb-1">
                        <i class="bx bx-map" aria-hidden="true"></i>
                        <span><?= e_text($lokasiText); ?></span>
                    </div>
                    <?php endif; ?>
                    <h3 class="jadwal-church-title">
                        <a href="<?= BASEURL; ?>gereja/<?= htmlspecialchars($g->slug ? $g->slug : $g->id); ?>">
                            <?= e_text($g->nama_gereja); ?>
                        </a>
                    </h3>
                    <p class="jadwal-church-sub">
                        <i class="bx bx-map-pin" aria-hidden="true"></i>
                        <span><?= e_text($g->alamat); ?></span>
                    </p>
                </div>
                <a href="<?= BASEURL; ?>gereja/<?= htmlspecialchars($g->slug ? $g->slug : $g->id); ?>" class="gki-btn gki-btn-soft gki-btn-sm flex-shrink-0">
                    Detail
                </a>
            </div>

            <div class="jadwal-rows">
                <?php foreach ($hariUrut as $hari): if (isset($kelompok[$hari])): ?>
                    <?php foreach ($kelompok[$hari] as $j): ?>
                    <div class="jadwal-row">
                        <span class="jadwal-day"><?= e_text($j->hari); ?></span>
                        <span class="jadwal-time"><?= date('H:i', strtotime($j->waktu_mulai)); ?></span>
                        <?php if ($j->keterangan): ?>
                        <span class="jadwal-ket" title="<?= e_text($j->keterangan); ?>"><?= e_text($j->keterangan); ?></span>
                        <?php endif; ?>
                        <span class="jadwal-cat"><?= e_text($j->kategori); ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; endforeach; ?>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>

<?php $this->view('layouts/guest_footer'); ?>
<?php $this->view('layouts/guest_bottom_nav', array('activeMenu' => 'jadwal')); ?>
<?php $this->view('layouts/guest_closeTag'); ?>
