<?php
$mapLat = (float)$gereja->latitude;
$mapLng = (float)$gereja->longitude;
?>
<?php $this->view('layouts/guest_openTag', array('title' => $title, 'css' => array('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'))); ?>

<style>
    .detail-header { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; padding: 1rem 1rem; border-radius: 0 0 2rem 2rem; margin-bottom: 1.5rem; }
    .detail-header .header-inner { display: flex; align-items: center; justify-content: center; max-width: 1200px; margin: 0 auto; position: relative; min-height: 44px; }
    .detail-header .back-btn { color: rgba(255,255,255,0.9); font-size: 1.4rem; text-decoration: none; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.12); border-radius: 50%; transition: background .2s; position: absolute; left: 0; top: 50%; transform: translateY(-50%); }
    .detail-header .back-btn:hover { background: rgba(255,255,255,0.25); color: #fff; }
    .detail-header h1 { font-size: 1.15rem; font-weight: 700; color: #fff; margin: 0; text-align: center; }
    .info-card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 1rem; overflow: hidden; }
    .info-card .card-header { background: var(--primary); color: white; font-weight: 700; border: none; padding: 0.75rem 1rem; font-size: 0.9rem; }
    .info-card .card-body { padding: 0.5rem 1rem; }
    .info-item { display: flex; gap: 0.75rem; padding: 0.6rem 0; border-bottom: 1px solid #f0f0f0; align-items: flex-start; }
    .info-item:last-child { border-bottom: none; }
    .info-item .info-icon { color: var(--primary); font-size: 1.1rem; width: 1.5rem; text-align: center; flex-shrink: 0; margin-top: 0.1rem; }
    .info-item .info-label { font-size: 0.72rem; font-weight: 600; color: var(--primary); text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.1rem; }
    .info-item .info-value { font-size: 0.88rem; color: #333; line-height: 1.5; }
    .info-item .info-value a { color: var(--primary); text-decoration: none; }
    .info-item .info-value a:hover { text-decoration: underline; }

    .socmed-grid { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.4rem; }
    .socmed-btn { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.85rem; border-radius: 10px; font-size: 0.78rem; font-weight: 600; text-decoration: none; transition: all .2s; border: none; cursor: pointer; }
    .socmed-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .socmed-btn:active { transform: translateY(0); }
    .socmed-btn .bx, .socmed-btn .bxl { font-size: 1.1rem; }
    .socmed-website { background: #2C4463; color: #fff; }
    .socmed-website:hover { background: #1A2D47; color: #fff; }
    .socmed-instagram { background: linear-gradient(135deg, #833AB4, #FD1D1D, #F77737); color: #fff; }
    .socmed-instagram:hover { color: #fff; }
    .socmed-facebook { background: #1877F2; color: #fff; }
    .socmed-facebook:hover { background: #0f6bd6; color: #fff; }
    .socmed-twitter { background: #1DA1F2; color: #fff; }
    .socmed-twitter:hover { background: #0d8bda; color: #fff; }
    .socmed-youtube { background: #FF0000; color: #fff; }
    .socmed-youtube:hover { background: #d60000; color: #fff; }
    .socmed-tiktok { background: #010101; color: #fff; }
    .socmed-tiktok:hover { background: #333; color: #fff; }
    .socmed-linkedin { background: #0A66C2; color: #fff; }
    .socmed-linkedin:hover { background: #004182; color: #fff; }
    .socmed-whatsapp { background: #25D366; color: #fff; }
    .socmed-whatsapp:hover { background: #1da851; color: #fff; }
    .socmed-telegram { background: #0088CC; color: #fff; }
    .socmed-telegram:hover { background: #0077b3; color: #fff; }
    .socmed-other { background: #6c757d; color: #fff; }
    .socmed-other:hover { background: #5a6268; color: #fff; }

    .jadwal-group { margin-bottom: 1.25rem; }
    .jadwal-group:last-child { margin-bottom: 0; }
    .jadwal-group-header { display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.5rem; border-bottom: 2px solid var(--primary); margin-bottom: 0.75rem; gap: 1rem; }
    .jadwal-group-header-left { display: flex; align-items: center; gap: 0.5rem; }
    .jadwal-group-header i { color: var(--primary); font-size: 1rem; }
    .jadwal-group-title { color: var(--primary); font-weight: 700; font-size: 0.95rem; }
    .jadwal-group-badge { font-size: 0.7rem; font-weight: 600; padding: 0.3rem 0.65rem; border-radius: 4px; background: rgba(44,68,99,0.12); color: #2C4463; white-space: nowrap; }
    .jadwal-item { display: flex; align-items: flex-start; justify-content: space-between; padding: 0.65rem 0.5rem; border-bottom: 1px solid #f0f0f0; transition: background 0.2s; }
    .jadwal-item:last-child { border-bottom: none; }
    .jadwal-item:hover { background: rgba(44,68,99,0.03); }
    .jadwal-item-content { flex: 1; display: flex; flex-direction: column; gap: 0.3rem; min-width: 0; }
    .jadwal-item-header { display: flex; gap: 0.8rem; align-items: center; flex-wrap: wrap; }
    .jadwal-tanggal { font-size: 0.8rem; color: #666; font-weight: 500; white-space: nowrap; }
    .jadwal-hari { font-weight: 700; font-size: 0.9rem; color: #2C4463; white-space: nowrap; display: none; }
    .jadwal-badge { display: none; }
    .jadwal-time { font-weight: 700; font-size: 0.9rem; color: var(--primary); white-space: nowrap; }
    .jadwal-ket { font-size: 0.82rem; color: #555; }

    @media (min-width: 576px) {
        .jadwal-item-header { flex-wrap: nowrap; }
    }
    .btn-lokasi { background: #1565C0; color: white; border: none; border-radius: 12px; padding: 0.6rem 1.2rem; font-weight: 600; font-size: 0.85rem; transition: all .2s; }
    .btn-lokasi:hover { background: #0D47A1; color: white; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(21,101,192,0.3); }
    .btn-navigate { background: #2E7D32; color: white; border: none; border-radius: 12px; padding: 0.6rem 1.2rem; font-weight: 600; font-size: 0.85rem; transition: all .2s; }
    .btn-navigate:hover { background: #1B5E20; color: white; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(46,125,50,0.3); }
    .btn-saran { background: transparent; color: var(--primary); border: 2px solid var(--primary); border-radius: 12px; padding: 0.55rem 1.2rem; font-weight: 600; font-size: 0.85rem; transition: all .2s; }
    .btn-saran:hover { background: var(--primary); color: white; transform: translateY(-1px); }
    #map-detail { height: 220px; }
    .map-wrap { overflow: hidden; }
    .foto-carousel { border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
    .foto-carousel .carousel-item img { height: 300px; object-fit: cover; width: 100%; }
    .foto-carousel .carousel-item .placeholder-img { height: 300px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--primary-light), var(--primary-dark)); }
    .foto-carousel .carousel-item .placeholder-img i { font-size: 5rem; color: rgba(255,255,255,0.3); }
    .foto-thumbs { display: flex; gap: 0.5rem; margin-top: 0.5rem; overflow-x: auto; padding-bottom: 0.25rem; }
    .foto-thumbs img { width: 60px; height: 45px; object-fit: cover; border-radius: 8px; cursor: pointer; opacity: 0.5; border: 2px solid transparent; transition: all 0.2s; flex-shrink: 0; }
    .foto-thumbs img:hover, .foto-thumbs img.active { opacity: 1; border-color: var(--primary); }
    .card-header-icon { font-size: 1rem; margin-right: 0.3rem; }
    @media (max-width: 767px) {
        .detail-header { padding: 0.85rem 0.85rem; }
        .detail-header .back-btn { width: 32px; height: 32px; font-size: 1.2rem; }
        .detail-header h1 { font-size: 1rem; }
        .info-item .info-label { font-size: 0.75rem; }
        .info-item .info-value { font-size: 0.92rem; }
        .jadwal-group h6 { font-size: 0.95rem; }
        .jadwal-tanggal { font-size: 0.88rem; }
        .jadwal-hari { font-size: 0.92rem; }
        .jadwal-time { font-size: 0.92rem; }
        .jadwal-ket { font-size: 0.88rem; }
        .jadwal-badge { font-size: 0.7rem; }
        .btn-lokasi, .btn-navigate, .btn-saran { font-size: 0.9rem; padding: 0.65rem 1.2rem; }
        .socmed-btn { font-size: 0.82rem; padding: 0.5rem 0.9rem; }
        .foto-carousel .carousel-item img { height: 200px; }
        .foto-carousel .carousel-item .placeholder-img { height: 200px; }
    }
</style>

<div class="detail-header">
    <div class="header-inner">
        <a href="<?= isset($_GET['from']) && $_GET['from'] === 'maps' ? BASEURL . 'maps' : BASEURL; ?>" class="back-btn"><i class="bx bx-arrow-back"></i></a>
        <h1>Detail Info Gereja</h1>
    </div>
</div>

<div class="container px-3 px-md-4 pb-5 mb-4">
    <?php if (!empty($fotoList)): ?>
    <div class="mb-3">
        <div id="fotoCarousel" class="carousel slide foto-carousel" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php $fno=0; foreach ($fotoList as $f): ?>
                <div class="carousel-item <?= $fno === 0 ? 'active' : ''; ?>">
                    <img src="<?= htmlspecialchars($f->foto_url); ?>" class="d-block w-100" alt="<?= htmlspecialchars($gereja->nama_gereja); ?>">
                </div>
                <?php $fno++; endforeach; ?>
            </div>
            <?php if (count($fotoList) > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#fotoCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#fotoCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
            <?php endif; ?>
        </div>
        <?php if (count($fotoList) > 1): ?>
        <div class="foto-thumbs">
            <?php $fno=0; foreach ($fotoList as $f): ?>
            <img src="<?= htmlspecialchars($f->foto_url); ?>" class="<?= $fno === 0 ? 'active' : ''; ?>" data-bs-target="#fotoCarousel" data-bs-slide-to="<?= $fno; ?>" alt="">
            <?php $fno++; endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="info-card">
                <div class="card-header"><i class="bx bx-info-circle card-header-icon"></i>Informasi Gereja</div>
                <div class="card-body">
                    <div class="info-item">
                        <i class="bx bx-map-pin info-icon"></i>
                        <div>
                            <div class="info-label">Alamat</div>
                            <div class="info-value"><?= htmlspecialchars($gereja->alamat); ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="bx bx-map info-icon"></i>
                        <div>
                            <div class="info-label">Provinsi</div>
                            <div class="info-value"><?= htmlspecialchars($gereja->provinsi); ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="bx bx-building info-icon"></i>
                        <div>
                            <div class="info-label">Kabupaten / Kota</div>
                            <div class="info-value"><?= htmlspecialchars($gereja->kabupaten_kota); ?></div>
                        </div>
                    </div>
                    <?php if ($gereja->kecamatan): ?>
                    <div class="info-item">
                        <i class="bx bx-detail info-icon"></i>
                        <div>
                            <div class="info-label">Kecamatan</div>
                            <div class="info-value"><?= htmlspecialchars($gereja->kecamatan); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($gereja->kelurahan): ?>
                    <div class="info-item">
                        <i class="bx bx-home info-icon"></i>
                        <div>
                            <div class="info-label">Kelurahan / Desa</div>
                            <div class="info-value"><?= htmlspecialchars($gereja->kelurahan); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($gereja->kontak_telepon): ?>
                    <div class="info-item">
                        <i class="bx bx-phone info-icon"></i>
                        <div>
                            <div class="info-label">Kontak</div>
                            <div class="info-value">
                                <a href="tel:<?= htmlspecialchars($gereja->kontak_telepon); ?>"><?= htmlspecialchars($gereja->kontak_telepon); ?></a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($gereja->deskripsi): ?>
                    <div class="info-item">
                        <i class="bx bx-book info-icon"></i>
                        <div>
                            <div class="info-label">Tentang Gereja</div>
                            <div class="info-value"><?= nl2br(htmlspecialchars($gereja->deskripsi)); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($socialList)): ?>
                    <div class="info-item">
                        <i class="bx bx-share-alt info-icon"></i>
                        <div style="flex:1;">
                            <div class="info-label">Sosial Media & Website</div>
                            <div class="socmed-grid">
                            <?php foreach ($socialList as $sm):
                                $cls = 'socmed-other';
                                $icon = 'bx bx-link';
                                if ($sm->platform == 'website') { $cls = 'socmed-website'; $icon = 'bx bx-globe'; }
                                elseif ($sm->platform == 'instagram') { $cls = 'socmed-instagram'; $icon = 'bx bxl-instagram'; }
                                elseif ($sm->platform == 'facebook') { $cls = 'socmed-facebook'; $icon = 'bx bxl-facebook'; }
                                elseif ($sm->platform == 'twitter') { $cls = 'socmed-twitter'; $icon = 'bx bxl-twitter'; }
                                elseif ($sm->platform == 'youtube') { $cls = 'socmed-youtube'; $icon = 'bx bxl-youtube'; }
                                elseif ($sm->platform == 'tiktok') { $cls = 'socmed-tiktok'; $icon = 'bx bxl-tiktok'; }
                                elseif ($sm->platform == 'linkedin') { $cls = 'socmed-linkedin'; $icon = 'bx bxl-linkedin'; }
                                elseif ($sm->platform == 'whatsapp') { $cls = 'socmed-whatsapp'; $icon = 'bx bxl-whatsapp'; }
                                elseif ($sm->platform == 'telegram') { $cls = 'socmed-telegram'; $icon = 'bx bxl-telegram'; }
                            ?>
                                <a href="<?= htmlspecialchars($sm->url); ?>" target="_blank" rel="noopener" class="socmed-btn <?= $cls; ?>">
                                    <i class="<?= $icon; ?>"></i>
                                    <?= htmlspecialchars(ucfirst($sm->platform)); ?>
                                </a>
                            <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="info-card">
                <div class="card-header"><i class="bx bx-calendar-event card-header-icon"></i>Jadwal Misa</div>
                <div class="card-body">
                    <?php
                    function getNextDate($hari) {
                        $map = array('Minggu'=>0,'Senin'=>1,'Selasa'=>2,'Rabu'=>3,'Kamis'=>4,'Jumat'=>5,'Sabtu'=>6);
                        if (!isset($map[$hari])) return null;
                        $target = $map[$hari];
                        $today = (int)date('w');
                        $diff = $target - $today;
                        if ($diff < 0) $diff += 7;
                        return date('Y-m-d', strtotime("+$diff days"));
                    }
                    $hariUrut = array('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Spesial');
                    $kelompok = array();
                    foreach ($jadwalList as $j) {
                        $kelompok[$j->hari][] = $j;
                    }
                    ?>
                    <?php if (empty($jadwalList)): ?>
                        <div style="padding: 1rem 0; text-align: center; color: #999;">
                            <i class="bx bx-calendar-x" style="font-size: 2.5rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                            <span style="font-size:0.9rem;">Belum ada jadwal misa untuk gereja ini.</span>
                        </div>
                    <?php else: ?>
                        <?php foreach ($hariUrut as $hari): if (isset($kelompok[$hari])): ?>
                        <div class="jadwal-group">
                            <div class="jadwal-group-header">
                                <div class="jadwal-group-header-left">
                                    <i class="bx bx-calendar"></i>
                                    <span class="jadwal-group-title"><?= $hari == 'Spesial' ? 'Jadwal Khusus / Hari Raya' : 'Hari ' . $hari; ?></span>
                                </div>
                                <?php 
                                $firstBadge = isset($kelompok[$hari][0]) ? $kelompok[$hari][0]->kategori : '';
                                ?>
                                <span class="jadwal-group-badge"><?= $firstBadge; ?></span>
                            </div>
                            <?php foreach ($kelompok[$hari] as $j): ?>
                            <?php
                                $displayDate = $j->tanggal ? $j->tanggal : getNextDate($j->hari);
                            ?>
                            <div class="jadwal-item">
                                <div class="jadwal-item-content">
                                    <div class="jadwal-item-header">
                                        <span class="jadwal-tanggal"><?php $ts = strtotime($displayDate); $bln = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'); echo date('j', $ts) . ' ' . $bln[date('n', $ts)-1] . ' ' . date('Y', $ts); ?></span>
                                        <span class="jadwal-hari"><?= $j->hari; ?></span>
                                        <span class="jadwal-badge"><?= $j->kategori; ?></span>
                                        <span class="jadwal-time"><?= date('H:i', strtotime($j->waktu_mulai)); ?></span>
                                    </div>
                                    <?php if ($j->keterangan): ?>
                                        <span class="jadwal-ket">— <?= htmlspecialchars($j->keterangan); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="info-card">
                <div class="card-header"><i class="bx bx-map card-header-icon"></i>Lokasi</div>
                <div class="map-wrap">
                    <div id="map-detail"></div>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= !empty($gereja->link_maps) ? htmlspecialchars($gereja->link_maps) : 'https://www.google.com/maps?q=' . $mapLat . ',' . $mapLng; ?>" target="_blank" class="btn btn-lokasi w-100">
                            <i class="bx bx-map me-1"></i>Lihat Lokasi di Maps
                        </a>
                        <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $mapLat; ?>,<?= $mapLng; ?>" target="_blank" class="btn btn-navigate w-100">
                            <i class="bx bx-navigation me-1"></i>Arahkan ke Lokasi
                        </a>
                        <a href="<?= BASEURL; ?>saran/<?= $gereja->id; ?>" class="btn btn-saran w-100">
                            <i class="bx bx-flag me-1"></i>Laporkan Kesalahan Jadwal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->view('layouts/guest_bottom_nav'); ?>
<?php $this->view('layouts/guest_closeTag', array('js' => array('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'), 'scripts' => '
<script>
var map = L.map("map-detail").setView([' . $mapLat . ', ' . $mapLng . '], 16);
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "© OpenStreetMap contributors",
    maxZoom: 18
}).addTo(map);
L.marker([' . $mapLat . ', ' . $mapLng . ']).addTo(map)
    .bindPopup("<b>' . htmlspecialchars($gereja->nama_gereja, ENT_QUOTES) . '</b>")
    .openPopup();

</script>')); ?>
