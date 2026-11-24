<?php if (!function_exists('buildPageUrl')): function buildPageUrl($page, $limit) {
    $params = $_GET;
    $params['page'] = $page;
    $params['limit'] = $limit;
    return '?' . http_build_query($params);
} endif; ?>

<?php
$hasLocationFilter = !empty($selectedProvinsi) || !empty($selectedKabupaten) || !empty($selectedKecamatan) || !empty($selectedKelurahan);
$hasAnyFilter = $hasLocationFilter || $hasJadwalFilter;

$regionParts = array_filter(array($selectedKabupaten, $selectedProvinsi));
$regionLabel = implode(', ', $regionParts);
$homeSeoTitle = $regionLabel
    ? 'Gereja Katolik di ' . decode_text($regionLabel) . ' — Jadwal Misa & Alamat | Info Gereja'
    : 'Info Gereja — Direktori Gereja Katolik Indonesia & Jadwal Misa Terbaru';
$homeSeoDesc = $regionLabel
    ? 'Temukan ' . (int)$totalGereja . ' gereja Katolik di ' . decode_text($regionLabel) . ' lengkap dengan alamat paroki, kontak, peta lokasi, dan jadwal misa terbaru.'
    : 'Direktori lengkap ' . (int)$totalGereja . ' gereja Katolik di seluruh Indonesia. Cari lokasi gereja terdekat, peta interaktif, dan jadwal misa harian maupun mingguan.';
$homeCanonical = BASEURL . ($currentPage > 1 && !$hasAnyFilter ? '?page=' . (int)$currentPage : '');
$homeSeo = array(
    'title' => $homeSeoTitle,
    'description' => $homeSeoDesc,
    'keywords' => 'gereja katolik indonesia, jadwal misa, direktori gereja katolik, paroki katolik' . ($regionLabel ? ', gereja katolik ' . strtolower(decode_text($regionLabel)) : ''),
    'canonical' => $homeCanonical,
    'robots' => $hasAnyFilter ? 'noindex, follow' : 'index, follow',
    'prev' => (!$hasAnyFilter && $currentPage > 1) ? (BASEURL . ($currentPage > 2 ? '?page=' . ((int)$currentPage - 1) : '')) : '',
    'next' => (!$hasAnyFilter && $currentPage < $totalPages) ? (BASEURL . '?page=' . ((int)$currentPage + 1)) : '',
    'schema' => array(
        array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'Info Gereja',
            'alternateName' => 'Direktori Gereja Katolik Indonesia',
            'url' => BASEURL,
            'potentialAction' => array(
                '@type' => 'SearchAction',
                'target' => BASEURL . 'cari?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ),
        ),
        array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Info Gereja',
            'url' => BASEURL,
            'logo' => BASEURL . 'assets/images/logo/logo.png',
        ),
    ),
);
?>
<?php $this->view('layouts/guest_openTag', array('title' => $title, 'seo' => $homeSeo)); ?>
<?php
ob_start();
?>
<a href="<?= BASEURL; ?>maps" class="gki-btn gki-btn-outline gki-btn-sm d-md-none" aria-label="Peta Gereja">
    <i class="bx bx-map" aria-hidden="true"></i>
    <span>Peta</span>
</a>
<button type="button" class="gki-btn gki-btn-outline gki-btn-sm d-md-none <?= $hasAnyFilter ? 'is-active' : ''; ?>" id="mobileFilterBtn" onclick="toggleFilterDrawer()" aria-expanded="false" aria-controls="filterDrawer">
    <i class="bx bx-filter-alt" aria-hidden="true"></i>
    <span>Filter</span>
</button>
<?php
$mobileActionsHtml = ob_get_clean();
?>

<style>
    .home-filter-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr)) auto;
        gap: 0.65rem;
        align-items: end;
    }
    .home-filter-secondary {
        display: none;
        margin-top: 0.85rem;
        padding-top: 0.85rem;
        border-top: 1px solid var(--border);
        grid-template-columns: repeat(4, minmax(0, 1fr)) auto;
        gap: 0.65rem;
        align-items: end;
    }
    .home-filter-secondary.is-open {
        display: grid;
    }
    .home-filter-actions {
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }
    .home-quick-links {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    .home-quick-link {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.8rem;
        border-radius: var(--radius-pill);
        background: rgba(255, 255, 255, 0.14);
        color: #fff;
        font-size: 0.82rem;
        font-weight: 600;
        transition: background-color 0.15s ease;
    }
    .home-quick-link:hover {
        background: rgba(255, 255, 255, 0.24);
        color: #fff;
    }
    @media (max-width: 991px) {
        .home-filter-grid,
        .home-filter-secondary.is-open {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .home-filter-actions {
            grid-column: 1 / -1;
            justify-content: flex-end;
        }
    }
</style>

<?php $this->view('layouts/guest_navbar', array('activeMenu' => 'home', 'mobileActions' => $mobileActionsHtml)); ?>

<header class="gki-hero">
    <div class="gki-container">
        <div class="gki-hero-inner">
            <h1>Direktori Gereja Katolik Indonesia</h1>
            <p>Temukan lokasi gereja terdekat, jadwal misa harian maupun mingguan, dan rute menuju paroki tujuan Anda.</p>
            <form action="<?= BASEURL; ?>cari" method="GET" class="gki-search-bar" role="search">
                <input type="text" name="q" placeholder="Cari nama gereja, paroki, atau kota..." aria-label="Cari nama gereja atau kota">
                <button type="submit" aria-label="Cari gereja">
                    <i class="bx bx-search" aria-hidden="true"></i>
                    <span>Cari</span>
                </button>
            </form>
            <div class="home-quick-links">
                <a href="<?= BASEURL; ?>jadwal" class="home-quick-link"><i class="bx bx-calendar" aria-hidden="true"></i>Jadwal Misa</a>
                <a href="<?= BASEURL; ?>maps" class="home-quick-link"><i class="bx bx-map-alt" aria-hidden="true"></i>Jelajahi Peta</a>
            </div>
        </div>
    </div>
</header>

<main class="gki-container">
    <section class="gki-panel d-none d-md-block" aria-label="Filter gereja">
        <form method="GET" action="<?= BASEURL; ?>" id="filterForm">
            <div class="home-filter-grid">
                <div class="gki-field">
                    <label for="provinsi">Provinsi</label>
                    <select name="provinsi" id="provinsi" class="gki-select" onchange="document.getElementById('filterForm').submit();">
                        <option value="">Semua Provinsi</option>
                        <?php foreach ($provinsiList as $p): ?>
                        <option value="<?= htmlspecialchars($p->provinsi); ?>" <?= $selectedProvinsi === $p->provinsi ? 'selected' : ''; ?>><?= htmlspecialchars($p->provinsi); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="gki-field">
                    <label for="kabupaten">Kabupaten/Kota</label>
                    <select name="kabupaten" id="kabupaten" class="gki-select" onchange="document.getElementById('filterForm').submit();">
                        <option value="">Semua Kabupaten/Kota</option>
                        <?php foreach ($kabupatenList as $k): ?>
                        <option value="<?= htmlspecialchars($k->kabupaten_kota); ?>" <?= $selectedKabupaten === $k->kabupaten_kota ? 'selected' : ''; ?>><?= htmlspecialchars($k->kabupaten_kota); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="gki-field">
                    <label for="kecamatan">Kecamatan</label>
                    <select name="kecamatan" id="kecamatan" class="gki-select" onchange="document.getElementById('filterForm').submit();">
                        <option value="">Semua Kecamatan</option>
                        <?php foreach ($kecamatanList as $k): ?>
                        <option value="<?= htmlspecialchars($k->kecamatan); ?>" <?= $selectedKecamatan === $k->kecamatan ? 'selected' : ''; ?>><?= htmlspecialchars($k->kecamatan); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="gki-field">
                    <label for="kelurahan">Kelurahan</label>
                    <select name="kelurahan" id="kelurahan" class="gki-select" onchange="document.getElementById('filterForm').submit();">
                        <option value="">Semua Kelurahan</option>
                        <?php foreach ($kelurahanList as $k): ?>
                        <option value="<?= htmlspecialchars($k->kelurahan); ?>" <?= $selectedKelurahan === $k->kelurahan ? 'selected' : ''; ?>><?= htmlspecialchars($k->kelurahan); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="home-filter-actions">
                    <button type="button" class="gki-btn gki-btn-outline <?= $hasJadwalFilter ? 'is-active' : ''; ?>" id="toggleScheduleBtn" onclick="toggleScheduleFilter()" aria-expanded="<?= $hasJadwalFilter ? 'true' : 'false'; ?>" aria-controls="scheduleFilterRow">
                        <i class="bx bx-calendar" aria-hidden="true"></i>
                        <span>Jadwal</span>
                    </button>
                    <?php if ($hasAnyFilter): ?>
                    <a href="<?= BASEURL; ?>" class="gki-btn gki-btn-outline" title="Reset semua filter">
                        <i class="bx bx-x" aria-hidden="true"></i>
                        <span>Reset</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="home-filter-secondary <?= $hasJadwalFilter ? 'is-open' : ''; ?>" id="scheduleFilterRow">
                <div class="gki-field">
                    <label for="tgl_dari">Tanggal Dari</label>
                    <input type="date" name="tgl_dari" id="tgl_dari" class="gki-input" value="<?= htmlspecialchars($selectedTglDari); ?>">
                </div>
                <div class="gki-field">
                    <label for="tgl_sampai">Tanggal Sampai</label>
                    <input type="date" name="tgl_sampai" id="tgl_sampai" class="gki-input" value="<?= htmlspecialchars($selectedTglSampai); ?>">
                </div>
                <div class="gki-field">
                    <label for="jam_dari">Jam Dari</label>
                    <input type="time" name="jam_dari" id="jam_dari" class="gki-input" value="<?= htmlspecialchars($selectedJamDari); ?>">
                </div>
                <div class="gki-field">
                    <label for="jam_sampai">Jam Sampai</label>
                    <input type="time" name="jam_sampai" id="jam_sampai" class="gki-input" value="<?= htmlspecialchars($selectedJamSampai); ?>">
                </div>
                <div class="home-filter-actions">
                    <button type="submit" class="gki-btn gki-btn-primary">
                        <i class="bx bx-filter-alt" aria-hidden="true"></i>
                        <span>Terapkan</span>
                    </button>
                </div>
            </div>
        </form>
    </section>

    <div class="gki-drawer-overlay d-md-none" id="filterOverlay" onclick="toggleFilterDrawer()"></div>
    <div class="gki-drawer d-md-none" id="filterDrawer" role="dialog" aria-modal="true" aria-label="Filter Pencarian">
        <div class="gki-drawer-header">
            <h2><i class="bx bx-filter-alt" aria-hidden="true"></i>Filter Pencarian</h2>
            <button type="button" class="btn-close" onclick="toggleFilterDrawer()" aria-label="Tutup filter"></button>
        </div>
        <div class="gki-drawer-body">
            <form method="GET" action="<?= BASEURL; ?>">
                <div class="gki-field mb-3">
                    <label for="m_provinsi">Provinsi</label>
                    <select name="provinsi" id="m_provinsi" class="gki-select">
                        <option value="">Semua Provinsi</option>
                        <?php foreach ($provinsiList as $p): ?>
                        <option value="<?= htmlspecialchars($p->provinsi); ?>" <?= $selectedProvinsi === $p->provinsi ? 'selected' : ''; ?>><?= htmlspecialchars($p->provinsi); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="gki-field mb-3">
                    <label for="m_kabupaten">Kabupaten/Kota</label>
                    <select name="kabupaten" id="m_kabupaten" class="gki-select">
                        <option value="">Semua Kabupaten/Kota</option>
                        <?php foreach ($kabupatenList as $k): ?>
                        <option value="<?= htmlspecialchars($k->kabupaten_kota); ?>" <?= $selectedKabupaten === $k->kabupaten_kota ? 'selected' : ''; ?>><?= htmlspecialchars($k->kabupaten_kota); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="gki-field mb-3">
                    <label for="m_kecamatan">Kecamatan</label>
                    <select name="kecamatan" id="m_kecamatan" class="gki-select">
                        <option value="">Semua Kecamatan</option>
                        <?php foreach ($kecamatanList as $k): ?>
                        <option value="<?= htmlspecialchars($k->kecamatan); ?>" <?= $selectedKecamatan === $k->kecamatan ? 'selected' : ''; ?>><?= htmlspecialchars($k->kecamatan); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="gki-field mb-3">
                    <label for="m_kelurahan">Kelurahan</label>
                    <select name="kelurahan" id="m_kelurahan" class="gki-select">
                        <option value="">Semua Kelurahan</option>
                        <?php foreach ($kelurahanList as $k): ?>
                        <option value="<?= htmlspecialchars($k->kelurahan); ?>" <?= $selectedKelurahan === $k->kelurahan ? 'selected' : ''; ?>><?= htmlspecialchars($k->kelurahan); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <hr class="my-3">
                <div class="gki-field mb-3">
                    <label for="m_tgl_dari">Tanggal Dari</label>
                    <input type="date" name="tgl_dari" id="m_tgl_dari" class="gki-input" value="<?= htmlspecialchars($selectedTglDari); ?>">
                </div>
                <div class="gki-field mb-3">
                    <label for="m_tgl_sampai">Tanggal Sampai</label>
                    <input type="date" name="tgl_sampai" id="m_tgl_sampai" class="gki-input" value="<?= htmlspecialchars($selectedTglSampai); ?>">
                </div>
                <div class="gki-field mb-3">
                    <label for="m_jam_dari">Jam Dari</label>
                    <input type="time" name="jam_dari" id="m_jam_dari" class="gki-input" value="<?= htmlspecialchars($selectedJamDari); ?>">
                </div>
                <div class="gki-field mb-3">
                    <label for="m_jam_sampai">Jam Sampai</label>
                    <input type="time" name="jam_sampai" id="m_jam_sampai" class="gki-input" value="<?= htmlspecialchars($selectedJamSampai); ?>">
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="gki-btn gki-btn-primary flex-fill">Terapkan Filter</button>
                    <a href="<?= BASEURL; ?>" class="gki-btn gki-btn-outline flex-fill">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <?php if ($hasAnyFilter): ?>
    <div class="gki-active-filters">
        <span class="gki-active-filters-label">Filter aktif:</span>
        <?php if ($selectedProvinsi): ?>
        <span class="gki-chip gki-chip-primary"><?= htmlspecialchars($selectedProvinsi); ?></span>
        <?php endif; ?>
        <?php if ($selectedKabupaten): ?>
        <span class="gki-chip gki-chip-primary"><?= htmlspecialchars($selectedKabupaten); ?></span>
        <?php endif; ?>
        <?php if ($selectedKecamatan): ?>
        <span class="gki-chip gki-chip-primary"><?= htmlspecialchars($selectedKecamatan); ?></span>
        <?php endif; ?>
        <?php if ($selectedKelurahan): ?>
        <span class="gki-chip gki-chip-primary"><?= htmlspecialchars($selectedKelurahan); ?></span>
        <?php endif; ?>
        <?php if ($selectedTglDari): ?>
        <span class="gki-chip gki-chip-accent">&ge; <?= date('d/m/Y', strtotime($selectedTglDari)); ?></span>
        <?php endif; ?>
        <?php if ($selectedTglSampai): ?>
        <span class="gki-chip gki-chip-accent">&le; <?= date('d/m/Y', strtotime($selectedTglSampai)); ?></span>
        <?php endif; ?>
        <?php if ($selectedJamDari): ?>
        <span class="gki-chip gki-chip-neutral">&ge; <?= htmlspecialchars($selectedJamDari); ?></span>
        <?php endif; ?>
        <?php if ($selectedJamSampai): ?>
        <span class="gki-chip gki-chip-neutral">&le; <?= htmlspecialchars($selectedJamSampai); ?></span>
        <?php endif; ?>
        <a href="<?= BASEURL; ?>" class="gki-clear-link">Hapus semua</a>
    </div>
    <?php endif; ?>

    <div class="gki-section-head">
        <div class="gki-section-title-group">
            <h2 class="gki-section-title">Daftar Gereja</h2>
            <span class="gki-count-pill"><?= $totalGereja; ?> gereja</span>
        </div>
    </div>

    <?php if (empty($gerejaList)): ?>
    <div class="gki-empty">
        <i class="bx bx-church" aria-hidden="true"></i>
        <h3>Tidak ada gereja ditemukan</h3>
        <p>Coba ubah filter wilayah atau rentang jadwal misa yang dipilih.</p>
        <?php if ($hasAnyFilter): ?>
        <a href="<?= BASEURL; ?>" class="gki-btn gki-btn-primary">
            <i class="bx bx-x" aria-hidden="true"></i>Reset Filter
        </a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="gki-church-grid">
        <?php foreach ($gerejaList as $g): ?>
        <?php
            $fotoUrl = isset($fotoByGereja[$g->id]) ? $fotoByGereja[$g->id] : '';
            $lokasiParts = array_filter(array($g->kabupaten_kota, $g->provinsi));
            $lokasiText = implode(', ', $lokasiParts);
        ?>
        <article class="gki-church-card">
            <div class="gki-church-media">
                <?php if ($fotoUrl): ?>
                    <img src="<?= htmlspecialchars($fotoUrl); ?>" alt="<?= e_text($g->nama_gereja); ?>" loading="lazy">
                <?php else: ?>
                    <div class="gki-church-media-empty" aria-hidden="true">
                        <i class="bx bx-church"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="gki-church-body">
                <?php if ($lokasiText): ?>
                <div class="gki-church-region" title="<?= e_text($lokasiText); ?>">
                    <i class="bx bx-map" aria-hidden="true"></i>
                    <span><?= e_text($lokasiText); ?></span>
                </div>
                <?php endif; ?>

                <h3 class="gki-church-name"><?= e_text($g->nama_gereja); ?></h3>

                <div class="gki-church-meta" title="<?= e_text($g->alamat); ?>">
                    <i class="bx bx-map-pin" aria-hidden="true"></i>
                    <span class="gki-church-address-text"><?= e_text($g->alamat); ?></span>
                </div>

                <?php if ($g->kontak_telepon): ?>
                <div class="gki-church-meta tabular-nums">
                    <i class="bx bx-phone" aria-hidden="true"></i>
                    <span><?= e_text($g->kontak_telepon); ?></span>
                    <a href="tel:<?= e_text($g->kontak_telepon); ?>" class="gki-church-phone-link">Hubungi</a>
                </div>
                <?php endif; ?>

                <?php if ($hasJadwalFilter && isset($jadwalByGereja[$g->id])): ?>
                <div class="gki-church-schedule-preview">
                    <div class="gki-church-schedule-title"><i class="bx bx-calendar-event" aria-hidden="true"></i>Jadwal Misa</div>
                    <?php foreach ($jadwalByGereja[$g->id] as $jj): ?>
                    <div class="gki-church-schedule-row">
                        <span><?= htmlspecialchars($jj->hari); ?><?= $jj->tanggal ? ', ' . date('d/m', strtotime($jj->tanggal)) : ''; ?></span>
                        <span class="fw-semibold"><?= date('H:i', strtotime($jj->waktu_mulai)); ?> WIB</span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="gki-church-cta">
                    <a href="<?= BASEURL; ?>gereja/<?= htmlspecialchars($g->slug ? $g->slug : $g->id); ?>" class="gki-btn gki-btn-soft w-100">
                        <span>Lihat Detail &amp; Jadwal</span>
                        <i class="bx bx-chevron-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($totalPages > 1 || $limit != 10): ?>
    <div class="gki-pagination-bar">
        <div class="gki-pagination-info">Menampilkan <?= count($gerejaList); ?> dari <?= $totalGereja; ?> gereja</div>
        <div class="gki-pagination-controls">
            <div class="gki-limit-select">
                <label for="limitSelect" class="mb-0">Tampil:</label>
                <select id="limitSelect" onchange="window.location.href=buildPageUrl(1, this.value)">
                    <option value="10" <?= $limit == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="20" <?= $limit == 20 ? 'selected' : ''; ?>>20</option>
                    <option value="50" <?= $limit == 50 ? 'selected' : ''; ?>>50</option>
                </select>
            </div>
            <?php if ($totalPages > 1): ?>
            <nav class="gki-pagination-links" aria-label="Navigasi halaman">
                <a href="<?= $currentPage > 1 ? buildPageUrl(1, $limit) : '#'; ?>" class="<?= $currentPage <= 1 ? 'disabled' : ''; ?>" aria-label="Halaman Pertama"><i class="bx bx-chevrons-left" aria-hidden="true"></i></a>
                <a href="<?= $currentPage > 1 ? buildPageUrl($currentPage - 1, $limit) : '#'; ?>" class="<?= $currentPage <= 1 ? 'disabled' : ''; ?>" aria-label="Sebelumnya"><i class="bx bx-chevron-left" aria-hidden="true"></i></a>
                <?php
                $start = max(1, min($currentPage - 2, $totalPages - 4));
                $end = min($totalPages, max(5, $start + 4));
                for ($i = $start; $i <= $end; $i++): ?>
                <a href="<?= buildPageUrl($i, $limit); ?>" class="<?= $i == $currentPage ? 'active' : ''; ?>" <?= $i == $currentPage ? 'aria-current="page"' : ''; ?>><?= $i; ?></a>
                <?php endfor; ?>
                <a href="<?= $currentPage < $totalPages ? buildPageUrl($currentPage + 1, $limit) : '#'; ?>" class="<?= $currentPage >= $totalPages ? 'disabled' : ''; ?>" aria-label="Selanjutnya"><i class="bx bx-chevron-right" aria-hidden="true"></i></a>
                <a href="<?= $currentPage < $totalPages ? buildPageUrl($totalPages, $limit) : '#'; ?>" class="<?= $currentPage >= $totalPages ? 'disabled' : ''; ?>" aria-label="Halaman Terakhir"><i class="bx bx-chevrons-right" aria-hidden="true"></i></a>
            </nav>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</main>

<?php $this->view('layouts/guest_footer'); ?>
<?php $this->view('layouts/guest_bottom_nav', array('activeMenu' => 'home')); ?>

<script>
function toggleScheduleFilter() {
    var row = document.getElementById('scheduleFilterRow');
    var btn = document.getElementById('toggleScheduleBtn');
    if (!row || !btn) return;
    var isOpen = row.classList.toggle('is-open');
    btn.classList.toggle('is-active', isOpen);
    btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
}

function toggleFilterDrawer() {
    var drawer = document.getElementById('filterDrawer');
    var overlay = document.getElementById('filterOverlay');
    var btn = document.getElementById('mobileFilterBtn');
    if (!drawer || !overlay) return;
    var isOpen = drawer.classList.toggle('active');
    overlay.classList.toggle('active', isOpen);
    if (btn) {
        btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }
    document.body.style.overflow = isOpen ? 'hidden' : '';
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        var drawer = document.getElementById('filterDrawer');
        if (drawer && drawer.classList.contains('active')) {
            toggleFilterDrawer();
        }
    }
});

function buildPageUrl(page, limit) {
    var params = new URLSearchParams(window.location.search);
    params.set('page', page);
    params.set('limit', limit);
    return '?' + params.toString();
}
</script>
<?php $this->view('layouts/guest_closeTag'); ?>
