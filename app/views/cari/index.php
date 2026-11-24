<?php
$hasCariFilter = !empty($keyword) || !empty($selectedHari) || !empty($selectedKategori);
$cariParts = array();
if (!empty($keyword)) $cariParts[] = '"' . decode_text($keyword) . '"';
if (!empty($selectedHari)) $cariParts[] = 'Hari ' . decode_text($selectedHari);
if (!empty($selectedKategori)) $cariParts[] = decode_text($selectedKategori);

$cariSeoTitle = !empty($cariParts)
    ? 'Hasil Pencarian Gereja Katolik: ' . implode(' · ', $cariParts) . ' | Info Gereja'
    : 'Cari Gereja Katolik Indonesia — Nama Paroki, Kota & Hari Misa | Info Gereja';
$cariSeoDesc = !empty($cariParts)
    ? 'Ditemukan ' . count($gerejaList) . ' gereja Katolik untuk pencarian ' . implode(', ', $cariParts) . ' lengkap dengan alamat dan jadwal misa.'
    : 'Cari gereja Katolik di seluruh Indonesia berdasarkan nama paroki, kota, hari misa, atau kategori jadwal misa.';

$cariSeo = array(
    'title' => $cariSeoTitle,
    'description' => $cariSeoDesc,
    'keywords' => 'cari gereja katolik, pencarian gereja katolik indonesia, cari jadwal misa, paroki terdekat',
    'canonical' => BASEURL . 'cari',
    'robots' => $hasCariFilter ? 'noindex, follow' : 'index, follow',
    'schema' => array(
        array(
            '@context' => 'https://schema.org',
            '@type' => 'SearchResultsPage',
            'name' => 'Cari Gereja Katolik Indonesia',
            'url' => BASEURL . 'cari',
            'description' => $cariSeoDesc,
        ),
        array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array(
                array('@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => BASEURL),
                array('@type' => 'ListItem', 'position' => 2, 'name' => 'Cari Gereja', 'item' => BASEURL . 'cari'),
            ),
        ),
    ),
);
?>
<?php $this->view('layouts/guest_openTag', array('title' => $title, 'seo' => $cariSeo)); ?>

<?php $this->view('layouts/guest_navbar', array('activeMenu' => 'cari')); ?>

<header class="gki-page-header">
    <div class="gki-container">
        <h1>Cari Gereja</h1>
        <p>Temukan gereja berdasarkan nama, kota, hari misa, atau kategori jadwal.</p>
    </div>
</header>

<main class="gki-container">
    <section class="gki-panel" aria-label="Form pencarian gereja">
        <form action="<?= BASEURL; ?>cari" method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-5 col-lg-5 gki-field">
                    <label for="cari_q">Kata Kunci</label>
                    <input type="text" id="cari_q" name="q" class="gki-input" placeholder="Nama gereja, paroki, atau kota..." value="<?= htmlspecialchars($keyword); ?>">
                </div>
                <div class="col-6 col-md-3 col-lg-2 gki-field">
                    <label for="cari_hari">Hari Misa</label>
                    <select id="cari_hari" name="hari" class="gki-select">
                        <option value="">Semua Hari</option>
                        <?php foreach ($hariList as $h): ?>
                        <option value="<?= htmlspecialchars($h); ?>" <?= $selectedHari == $h ? 'selected' : ''; ?>><?= htmlspecialchars($h); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-2 col-lg-2 gki-field">
                    <label for="cari_kategori">Kategori</label>
                    <select id="cari_kategori" name="kategori" class="gki-select">
                        <option value="">Semua</option>
                        <?php foreach ($kategoriList as $k): ?>
                        <option value="<?= htmlspecialchars($k); ?>" <?= $selectedKategori == $k ? 'selected' : ''; ?>><?= htmlspecialchars($k); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-2 col-lg-3 d-flex gap-2">
                    <button type="submit" class="gki-btn gki-btn-primary flex-fill">
                        <i class="bx bx-search" aria-hidden="true"></i>
                        <span>Cari</span>
                    </button>
                    <?php if ($hasCariFilter): ?>
                    <a href="<?= BASEURL; ?>cari" class="gki-btn gki-btn-outline" title="Reset filter" aria-label="Reset filter">
                        <i class="bx bx-x" aria-hidden="true"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </section>

    <?php if ($hasCariFilter): ?>
    <div class="gki-active-filters">
        <span class="gki-active-filters-label">Filter aktif:</span>
        <?php if ($keyword): ?>
        <span class="gki-chip gki-chip-primary"><i class="bx bx-search" aria-hidden="true"></i>"<?= htmlspecialchars($keyword); ?>"</span>
        <?php endif; ?>
        <?php if ($selectedHari): ?>
        <span class="gki-chip gki-chip-accent"><i class="bx bx-calendar" aria-hidden="true"></i><?= htmlspecialchars($selectedHari); ?></span>
        <?php endif; ?>
        <?php if ($selectedKategori): ?>
        <span class="gki-chip gki-chip-neutral"><i class="bx bx-category" aria-hidden="true"></i><?= htmlspecialchars($selectedKategori); ?></span>
        <?php endif; ?>
        <a href="<?= BASEURL; ?>cari" class="gki-clear-link">Hapus semua</a>
    </div>
    <?php endif; ?>

    <?php if (empty($gerejaList)): ?>
    <div class="gki-empty">
        <i class="bx bx-search-alt" aria-hidden="true"></i>
        <h3>Tidak ada gereja ditemukan</h3>
        <p>Coba ubah kata kunci pencarian atau pilih hari dan kategori misa lain.</p>
        <a href="<?= BASEURL; ?>cari" class="gki-btn gki-btn-primary">
            <i class="bx bx-x" aria-hidden="true"></i>Reset Filter
        </a>
    </div>
    <?php else: ?>
    <div class="gki-section-head">
        <div class="gki-section-title-group">
            <h2 class="gki-section-title">Hasil Pencarian</h2>
            <span class="gki-count-pill"><?= count($gerejaList); ?> gereja</span>
        </div>
    </div>

    <div class="gki-church-grid">
        <?php foreach ($gerejaList as $g): ?>
        <?php
            $lokasiParts = array_filter(array($g->kabupaten_kota, $g->provinsi));
            $lokasiText = implode(', ', $lokasiParts);
            $subWilayah = array_filter(array(
                $g->kecamatan ? 'Kec. ' . $g->kecamatan : '',
                $g->kelurahan ? 'Kel. ' . $g->kelurahan : ''
            ));
        ?>
        <article class="gki-church-card">
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

                <?php if (!empty($subWilayah)): ?>
                <div class="gki-church-meta">
                    <i class="bx bx-compass" aria-hidden="true"></i>
                    <span><?= e_text(implode(' · ', $subWilayah)); ?></span>
                </div>
                <?php endif; ?>

                <?php if ($g->kontak_telepon): ?>
                <div class="gki-church-meta tabular-nums">
                    <i class="bx bx-phone" aria-hidden="true"></i>
                    <span><?= e_text($g->kontak_telepon); ?></span>
                    <a href="tel:<?= e_text($g->kontak_telepon); ?>" class="gki-church-phone-link">Hubungi</a>
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
</main>

<?php $this->view('layouts/guest_footer'); ?>
<?php $this->view('layouts/guest_bottom_nav', array('activeMenu' => 'cari')); ?>
<?php $this->view('layouts/guest_closeTag'); ?>
