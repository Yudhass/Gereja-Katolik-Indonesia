<?php
$mapLat = (float)$gereja->latitude;
$mapLng = (float)$gereja->longitude;
$backUrl = (isset($_GET['from']) && $_GET['from'] === 'maps') ? BASEURL . 'maps' : BASEURL;
$backLabel = (isset($_GET['from']) && $_GET['from'] === 'maps') ? 'Kembali ke Peta' : 'Kembali ke Beranda';
$lokasiRingkas = implode(', ', array_filter(array($gereja->kabupaten_kota, $gereja->provinsi)));
$flash = function_exists('getFlashMessage') ? getFlashMessage() : null;

if (!function_exists('getNextDate')) {
    function getNextDate($hari) {
        $map = array('Minggu'=>0,'Senin'=>1,'Selasa'=>2,'Rabu'=>3,'Kamis'=>4,'Jumat'=>5,'Sabtu'=>6);
        if (!isset($map[$hari])) return null;
        $target = $map[$hari];
        $today = (int)date('w');
        $diff = $target - $today;
        if ($diff < 0) $diff += 7;
        return date('Y-m-d', strtotime("+$diff days"));
    }
}
$canonicalSlug = !empty($gereja->slug) ? $gereja->slug : $gereja->id;
$canonicalUrl = BASEURL . 'gereja/' . rawurlencode($canonicalSlug);
$cleanNama = decode_text($gereja->nama_gereja);
$cleanAlamat = decode_text($gereja->alamat);
$cleanLokasi = decode_text($lokasiRingkas);
$firstPhoto = (!empty($fotoList) && !empty($fotoList[0]->foto_url)) ? $fotoList[0]->foto_url : '';

$detailSeoTitle = $cleanNama . ($cleanLokasi ? ' (' . $cleanLokasi . ')' : '') . ' — Alamat & Jadwal Misa | Info Gereja';
$detailDescParts = array('Informasi alamat, lokasi peta, dan jadwal misa ' . $cleanNama);
if ($cleanLokasi) $detailDescParts[] = 'di ' . $cleanLokasi;
if ($cleanAlamat) $detailDescParts[] = 'Alamat: ' . $cleanAlamat;
$detailSeoDesc = implode('. ', $detailDescParts);
if (function_exists('mb_strlen') && mb_strlen($detailSeoDesc, 'UTF-8') > 160) {
    $detailSeoDesc = rtrim(mb_substr($detailSeoDesc, 0, 157, 'UTF-8')) . '...';
}

$sameAsLinks = array();
if (!empty($socialList)) {
    foreach ($socialList as $sm) {
        if (!empty($sm->url)) $sameAsLinks[] = $sm->url;
    }
}

$churchSchema = array(
    '@context' => 'https://schema.org',
    '@type' => 'CatholicChurch',
    'name' => $cleanNama,
    'url' => $canonicalUrl,
    'description' => !empty($gereja->deskripsi) ? decode_text($gereja->deskripsi) : $detailSeoDesc,
    'address' => array(
        '@type' => 'PostalAddress',
        'streetAddress' => $cleanAlamat,
        'addressLocality' => decode_text($gereja->kabupaten_kota),
        'addressRegion' => decode_text($gereja->provinsi),
        'addressCountry' => 'ID',
    ),
);
if ($firstPhoto) $churchSchema['image'] = $firstPhoto;
if (!empty($gereja->kontak_telepon)) $churchSchema['telephone'] = decode_text($gereja->kontak_telepon);
if ($mapLat != 0.0 || $mapLng != 0.0) {
    $churchSchema['geo'] = array(
        '@type' => 'GeoCoordinates',
        'latitude' => $mapLat,
        'longitude' => $mapLng,
    );
    $churchSchema['hasMap'] = !empty($gereja->link_maps)
        ? $gereja->link_maps
        : ('https://www.google.com/maps?q=' . $mapLat . ',' . $mapLng);
}
if (!empty($sameAsLinks)) $churchSchema['sameAs'] = $sameAsLinks;

$detailBreadcrumbSchema = array(
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => array(
        array('@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => BASEURL),
        array('@type' => 'ListItem', 'position' => 2, 'name' => $cleanNama, 'item' => $canonicalUrl),
    ),
);

$detailSeo = array(
    'title' => $detailSeoTitle,
    'description' => $detailSeoDesc,
    'keywords' => $cleanNama . ', jadwal misa ' . $cleanNama . ($cleanLokasi ? ', gereja katolik ' . $cleanLokasi : '') . ', gereja katolik indonesia',
    'canonical' => $canonicalUrl,
    'og_type' => 'place',
    'image' => $firstPhoto,
    'image_alt' => $cleanNama,
    'schema' => array($churchSchema, $detailBreadcrumbSchema),
);
?>
<?php $this->view('layouts/guest_openTag', array(
    'title' => $title,
    'seo' => $detailSeo,
    'css' => array('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css')
)); ?>

<style>
    .detail-gallery {
        margin-bottom: 1.25rem;
    }
    .detail-carousel {
        border-radius: var(--radius-md);
        overflow: hidden;
        border: 1px solid var(--border);
        background: var(--primary-dark);
    }
    .detail-carousel .carousel-item img {
        height: 360px;
        width: 100%;
        object-fit: cover;
        display: block;
    }
    .detail-thumbs {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.6rem;
        overflow-x: auto;
        padding-bottom: 0.25rem;
    }
    .detail-thumbs img {
        width: 72px;
        height: 50px;
        object-fit: cover;
        border-radius: var(--radius-sm);
        cursor: pointer;
        opacity: 0.6;
        border: 2px solid transparent;
        transition: opacity 0.15s ease, border-color 0.15s ease;
        flex-shrink: 0;
    }
    .detail-thumbs img:hover,
    .detail-thumbs img.active {
        opacity: 1;
        border-color: var(--primary);
    }

    .detail-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        overflow: hidden;
        margin-bottom: 1.15rem;
    }
    .detail-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        padding: 0.9rem 1.15rem;
        border-bottom: 1px solid var(--border);
    }
    .detail-card-head h2 {
        font-size: 0.98rem;
        font-weight: 700;
        color: var(--ink);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }
    .detail-card-head h2 i {
        color: var(--primary);
        font-size: 1.15rem;
    }
    .detail-card-body {
        padding: 1rem 1.15rem;
    }

    .detail-meta-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.9rem 1.25rem;
    }
    .detail-meta-item.is-full {
        grid-column: 1 / -1;
    }
    .detail-meta-label {
        font-size: 0.76rem;
        font-weight: 600;
        color: var(--ink-muted);
        margin-bottom: 0.2rem;
    }
    .detail-meta-value {
        font-size: 0.9rem;
        color: var(--ink);
        line-height: 1.55;
    }
    .detail-meta-value.prose {
        max-width: 68ch;
        color: var(--ink-secondary);
    }

    .socmed-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        margin-top: 0.25rem;
    }
    .socmed-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.8rem;
        border-radius: var(--radius-sm);
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid var(--border-strong);
        background: var(--surface);
        color: var(--ink);
        transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
    }
    .socmed-btn:hover {
        background: var(--primary-soft);
        border-color: var(--primary);
        color: var(--primary);
    }
    .socmed-btn i {
        font-size: 1.05rem;
    }

    .jadwal-group {
        margin-bottom: 1.15rem;
    }
    .jadwal-group:last-child {
        margin-bottom: 0;
    }
    .jadwal-group-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        padding-bottom: 0.45rem;
        margin-bottom: 0.5rem;
        border-bottom: 1px solid var(--border);
    }
    .jadwal-group-title {
        font-weight: 700;
        font-size: 0.92rem;
        color: var(--ink);
    }
    .jadwal-group-cat {
        font-size: 0.74rem;
        font-weight: 700;
        padding: 0.18rem 0.55rem;
        border-radius: var(--radius-pill);
        background: var(--surface-subtle);
        color: var(--ink-secondary);
    }
    .jadwal-slot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.55rem 0.65rem;
        border-radius: var(--radius-sm);
        border-bottom: 1px solid var(--surface-subtle);
        font-variant-numeric: tabular-nums;
    }
    .jadwal-slot:last-child {
        border-bottom: none;
    }
    .jadwal-slot:hover {
        background: var(--bg-canvas);
    }
    .jadwal-slot-left {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
        min-width: 0;
    }
    .jadwal-slot-time {
        font-weight: 700;
        font-size: 0.88rem;
        color: var(--primary);
        background: var(--primary-soft);
        padding: 0.2rem 0.6rem;
        border-radius: 6px;
        white-space: nowrap;
    }
    .jadwal-slot-date {
        font-size: 0.82rem;
        color: var(--ink-secondary);
        white-space: nowrap;
    }
    .jadwal-slot-note {
        font-size: 0.82rem;
        color: var(--ink-muted);
    }

    .detail-sidebar-sticky {
        position: sticky;
        top: calc(var(--nav-height) + 1rem);
    }
    #map-detail {
        height: 240px;
        width: 100%;
    }

    @media (max-width: 767px) {
        .detail-carousel .carousel-item img {
            height: 220px;
        }
        .detail-meta-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php $this->view('layouts/guest_navbar'); ?>

<header class="gki-page-header">
    <div class="gki-container">
        <a href="<?= $backUrl; ?>" class="gki-breadcrumb">
            <i class="bx bx-arrow-back" aria-hidden="true"></i><?= $backLabel; ?>
        </a>
        <h1><?= e_text($gereja->nama_gereja); ?></h1>
        <?php if ($lokasiRingkas): ?>
        <p><?= e_text($lokasiRingkas); ?></p>
        <?php endif; ?>
    </div>
</header>

<main class="gki-container">
    <?php if ($flash):
        $alertClass = 'gki-alert-info';
        $alertIcon = 'bx bx-info-circle';
        if ($flash['type'] === 'success') { $alertClass = 'gki-alert-success'; $alertIcon = 'bx bx-check-circle'; }
        elseif ($flash['type'] === 'error') { $alertClass = 'gki-alert-error'; $alertIcon = 'bx bx-x-circle'; }
        elseif ($flash['type'] === 'warning') { $alertClass = 'gki-alert-warning'; $alertIcon = 'bx bx-error'; }
    ?>
    <div class="gki-alert <?= $alertClass; ?>" role="alert">
        <i class="<?= $alertIcon; ?>" aria-hidden="true"></i>
        <div><?= htmlspecialchars($flash['message']); ?></div>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <?php if (!empty($fotoList)): ?>
            <div class="detail-gallery">
                <div id="fotoCarousel" class="carousel slide detail-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php $fno = 0; foreach ($fotoList as $f): ?>
                        <div class="carousel-item <?= $fno === 0 ? 'active' : ''; ?>">
                            <img src="<?= htmlspecialchars($f->foto_url); ?>" alt="<?= e_text($gereja->nama_gereja); ?>" <?= $fno > 0 ? 'loading="lazy"' : ''; ?>>
                        </div>
                        <?php $fno++; endforeach; ?>
                    </div>
                    <?php if (count($fotoList) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#fotoCarousel" data-bs-slide="prev" aria-label="Foto sebelumnya">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#fotoCarousel" data-bs-slide="next" aria-label="Foto berikutnya">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                    <?php endif; ?>
                </div>
                <?php if (count($fotoList) > 1): ?>
                <div class="detail-thumbs" id="fotoThumbs">
                    <?php $fno = 0; foreach ($fotoList as $f): ?>
                    <img src="<?= htmlspecialchars($f->foto_url); ?>" class="<?= $fno === 0 ? 'active' : ''; ?>" data-bs-target="#fotoCarousel" data-bs-slide-to="<?= $fno; ?>" alt="Foto <?= $fno + 1; ?>" loading="lazy">
                    <?php $fno++; endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <section class="detail-card" aria-labelledby="jadwalHeading">
                <div class="detail-card-head">
                    <h2 id="jadwalHeading"><i class="bx bx-calendar-event" aria-hidden="true"></i>Jadwal Misa</h2>
                    <span class="gki-count-pill"><?= count($jadwalList); ?> jadwal</span>
                </div>
                <div class="detail-card-body">
                    <?php
                    $hariUrut = array('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Spesial');
                    $kelompok = array();
                    foreach ($jadwalList as $j) {
                        $kelompok[$j->hari][] = $j;
                    }
                    $blnIndo = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
                    ?>
                    <?php if (empty($jadwalList)): ?>
                        <div class="text-center py-4" style="color: var(--ink-muted);">
                            <i class="bx bx-calendar-x d-block mb-2" style="font-size: 2.25rem;" aria-hidden="true"></i>
                            <p class="mb-0 small">Belum ada jadwal misa tercatat untuk gereja ini.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($hariUrut as $hari): if (isset($kelompok[$hari])): ?>
                        <div class="jadwal-group">
                            <div class="jadwal-group-head">
                                <span class="jadwal-group-title"><?= $hari === 'Spesial' ? 'Jadwal Khusus / Hari Raya' : 'Hari ' . htmlspecialchars($hari); ?></span>
                                <?php if (!empty($kelompok[$hari][0]->kategori)): ?>
                                <span class="jadwal-group-cat"><?= htmlspecialchars($kelompok[$hari][0]->kategori); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php foreach ($kelompok[$hari] as $j): ?>
                            <?php
                                $displayDate = $j->tanggal ? $j->tanggal : getNextDate($j->hari);
                                $formattedDate = '';
                                if ($displayDate) {
                                    $ts = strtotime($displayDate);
                                    $formattedDate = date('j', $ts) . ' ' . $blnIndo[(int)date('n', $ts) - 1] . ' ' . date('Y', $ts);
                                }
                            ?>
                            <div class="jadwal-slot">
                                <div class="jadwal-slot-left">
                                    <span class="jadwal-slot-time"><?= date('H:i', strtotime($j->waktu_mulai)); ?> WIB</span>
                                    <?php if ($formattedDate): ?>
                                    <span class="jadwal-slot-date"><?= htmlspecialchars($formattedDate); ?></span>
                                    <?php endif; ?>
                                    <?php if ($j->keterangan): ?>
                                    <span class="jadwal-slot-note"><?= htmlspecialchars($j->keterangan); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <section class="detail-card" aria-labelledby="infoHeading">
                <div class="detail-card-head">
                    <h2 id="infoHeading"><i class="bx bx-info-circle" aria-hidden="true"></i>Informasi Gereja</h2>
                </div>
                <div class="detail-card-body">
                    <div class="detail-meta-grid">
                        <div class="detail-meta-item is-full">
                            <div class="detail-meta-label">Alamat Lengkap</div>
                            <div class="detail-meta-value"><?= e_text($gereja->alamat); ?></div>
                        </div>

                        <div class="detail-meta-item">
                            <div class="detail-meta-label">Provinsi</div>
                            <div class="detail-meta-value"><?= e_text($gereja->provinsi); ?></div>
                        </div>

                        <div class="detail-meta-item">
                            <div class="detail-meta-label">Kabupaten / Kota</div>
                            <div class="detail-meta-value"><?= e_text($gereja->kabupaten_kota); ?></div>
                        </div>

                        <?php if ($gereja->kecamatan): ?>
                        <div class="detail-meta-item">
                            <div class="detail-meta-label">Kecamatan</div>
                            <div class="detail-meta-value"><?= e_text($gereja->kecamatan); ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ($gereja->kelurahan): ?>
                        <div class="detail-meta-item">
                            <div class="detail-meta-label">Kelurahan / Desa</div>
                            <div class="detail-meta-value"><?= e_text($gereja->kelurahan); ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ($gereja->kontak_telepon): ?>
                        <div class="detail-meta-item is-full">
                            <div class="detail-meta-label">Kontak Telepon</div>
                            <div class="detail-meta-value tabular-nums">
                                <a href="tel:<?= e_text($gereja->kontak_telepon); ?>"><?= e_text($gereja->kontak_telepon); ?></a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($gereja->deskripsi): ?>
                        <div class="detail-meta-item is-full">
                            <div class="detail-meta-label">Tentang Gereja</div>
                            <div class="detail-meta-value prose"><?= nl2br(e_text($gereja->deskripsi)); ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($socialList)): ?>
                        <div class="detail-meta-item is-full">
                            <div class="detail-meta-label">Website &amp; Media Sosial</div>
                            <div class="socmed-grid">
                                <?php foreach ($socialList as $sm):
                                    $icon = 'bx bx-link';
                                    if ($sm->platform == 'website') $icon = 'bx bx-globe';
                                    elseif ($sm->platform == 'instagram') $icon = 'bx bxl-instagram';
                                    elseif ($sm->platform == 'facebook') $icon = 'bx bxl-facebook';
                                    elseif ($sm->platform == 'twitter') $icon = 'bx bxl-twitter';
                                    elseif ($sm->platform == 'youtube') $icon = 'bx bxl-youtube';
                                    elseif ($sm->platform == 'tiktok') $icon = 'bx bxl-tiktok';
                                    elseif ($sm->platform == 'linkedin') $icon = 'bx bxl-linkedin';
                                    elseif ($sm->platform == 'whatsapp') $icon = 'bx bxl-whatsapp';
                                    elseif ($sm->platform == 'telegram') $icon = 'bx bxl-telegram';
                                ?>
                                <a href="<?= htmlspecialchars($sm->url); ?>" target="_blank" rel="noopener noreferrer" class="socmed-btn">
                                    <i class="<?= $icon; ?>" aria-hidden="true"></i>
                                    <span><?= htmlspecialchars(ucfirst($sm->platform)); ?></span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>

        <aside class="col-12 col-lg-4">
            <div class="detail-sidebar-sticky">
                <div class="detail-card">
                    <div class="detail-card-head">
                        <h2><i class="bx bx-map-pin" aria-hidden="true"></i>Lokasi &amp; Aksi</h2>
                    </div>
                    <div id="map-detail"></div>
                    <div class="detail-card-body d-grid gap-2">
                        <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $mapLat; ?>,<?= $mapLng; ?>" target="_blank" rel="noopener noreferrer" class="gki-btn gki-btn-primary w-100">
                            <i class="bx bx-navigation" aria-hidden="true"></i>
                            <span>Petunjuk Arah</span>
                        </a>
                        <a href="<?= !empty($gereja->link_maps) ? htmlspecialchars($gereja->link_maps) : 'https://www.google.com/maps?q=' . $mapLat . ',' . $mapLng; ?>" target="_blank" rel="noopener noreferrer" class="gki-btn gki-btn-outline w-100">
                            <i class="bx bx-map" aria-hidden="true"></i>
                            <span>Buka di Google Maps</span>
                        </a>
                        <a href="<?= BASEURL; ?>saran/<?= htmlspecialchars($gereja->slug ? $gereja->slug : $gereja->id); ?>" class="gki-btn gki-btn-soft w-100">
                            <i class="bx bx-flag" aria-hidden="true"></i>
                            <span>Laporkan Koreksi Jadwal</span>
                        </a>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</main>

<?php $this->view('layouts/guest_footer'); ?>
<?php $this->view('layouts/guest_bottom_nav'); ?>
<?php
$detailScripts = '<script>
var map = L.map("map-detail").setView([' . $mapLat . ', ' . $mapLng . '], 16);
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
    maxZoom: 18
}).addTo(map);
L.marker([' . $mapLat . ', ' . $mapLng . ']).addTo(map)
    .bindPopup(' . json_encode('<b>' . e_text($gereja->nama_gereja) . '</b>') . ')
    .openPopup();

var carouselEl = document.getElementById("fotoCarousel");
if (carouselEl) {
    carouselEl.addEventListener("slid.bs.carousel", function (event) {
        var thumbs = document.querySelectorAll("#fotoThumbs img");
        thumbs.forEach(function (img, idx) {
            img.classList.toggle("active", idx === event.to);
        });
    });
}
</script>';
$this->view('layouts/guest_closeTag', array('js' => array('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'), 'scripts' => $detailScripts));
?>
