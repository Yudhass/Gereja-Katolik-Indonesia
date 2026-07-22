<?php $this->view('layouts/guest_openTag', array('title' => $title, 'css' => array('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'))); ?>

<style>
    .detail-header { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; padding: 1rem; border-radius: 0 0 2rem 2rem; margin-bottom: 1.5rem; }
    .detail-header .header-row { display: flex; align-items: center; justify-content: center; padding: 0.5rem 0; position: relative; }
    .detail-header .back-btn { color: white; font-size: 1.6rem; text-decoration: none; line-height: 1; position: absolute; left: 0; }
    .detail-header .header-title { font-size: 1.15rem; font-weight: 700; color: white; margin: 0; }
    .info-card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 1rem; overflow: hidden; }
    .info-card .card-header { background: var(--primary); color: white; font-weight: 700; border: none; padding: 0.75rem 1rem; }
    .info-card .card-body { padding: 1rem; }
    .info-item { display: flex; gap: 0.75rem; padding: 0.5rem 0; border-bottom: 1px solid #f0f0f0; }
    .info-item:last-child { border-bottom: none; }
    .info-item i { color: var(--primary); font-size: 1.2rem; width: 1.5rem; text-align: center; margin-top: 0.15rem; }
    .info-item strong { color: #333; }
    .info-item div { color: #333; }
    .jadwal-group { margin-bottom: 1rem; }
    .jadwal-group h6 { color: #5a0000; font-weight: 700; border-bottom: 2px solid var(--primary); padding-bottom: 0.35rem; margin-bottom: 0.5rem; }
    .jadwal-item { padding: 0.5rem 0; border-bottom: 1px dashed #ddd; display: flex; flex-wrap: wrap; gap: 0.3rem; }
    .jadwal-item:last-child { border-bottom: none; }
    .jadwal-tanggal { font-size: 0.8rem; color: #555; font-weight: 600; min-width: 3rem; }
    .jadwal-hari { font-weight: 600; color: #2C4463; min-width: 4rem; }
    .jadwal-time { font-weight: 700; color: #2C4463; min-width: 4rem; }
    .jadwal-ket { color: #444; font-size: 0.85rem; }
    .btn-lokasi { background: #1565C0; color: white; border: none; border-radius: 50px; padding: 0.6rem 1.5rem; font-weight: 600; }
    .btn-lokasi:hover { background: #0D47A1; color: white; }
    .btn-navigate { background: #2E7D32; color: white; border: none; border-radius: 50px; padding: 0.6rem 1.5rem; font-weight: 600; }
    .btn-navigate:hover { background: #1B5E20; color: white; }
    .btn-saran { background: transparent; color: var(--primary); border: 2px solid var(--primary); border-radius: 50px; padding: 0.6rem 1.5rem; font-weight: 600; }
    .btn-saran:hover { background: var(--primary); color: white; }
    #map-detail { height: 250px; border-radius: 12px; overflow: hidden; }
    .foto-carousel { border-radius: 12px; overflow: hidden; }
    .foto-carousel .carousel-item img { height: 300px; object-fit: cover; width: 100%; }
    .foto-carousel .carousel-item .placeholder-img { height: 300px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--primary-light), var(--primary-dark)); }
    .foto-carousel .carousel-item .placeholder-img i { font-size: 5rem; color: rgba(255,255,255,0.3); }
    .foto-thumbs { display: flex; gap: 0.5rem; margin-top: 0.5rem; overflow-x: auto; padding-bottom: 0.5rem; }
    .foto-thumbs img { width: 60px; height: 45px; object-fit: cover; border-radius: 6px; cursor: pointer; opacity: 0.6; border: 2px solid transparent; transition: all 0.2s; flex-shrink: 0; }
    .foto-thumbs img:hover, .foto-thumbs img.active { opacity: 1; border-color: var(--primary); }
    @media (max-width: 767px) {
        .detail-header { padding: 0.75rem; }
        .detail-header .header-title { font-size: 1rem; }
        .foto-carousel .carousel-item img { height: 200px; }
        .foto-carousel .carousel-item .placeholder-img { height: 200px; }
    }
</style>

<div class="detail-header">
    <div class="container">
        <div class="header-row">
            <a href="<?= BASEURL; ?>" class="back-btn"><i class="bx bx-arrow-back"></i></a>
            <span class="header-title">Detail Gereja</span>
        </div>
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
                <div class="card-header"><i class="bx bx-info-circle me-1"></i>Informasi Gereja</div>
                <div class="card-body">
                    <div class="info-item">
                        <i class="bx bx-map-pin"></i>
                        <div><strong>Alamat</strong><br><?= htmlspecialchars($gereja->alamat); ?></div>
                    </div>
                    <div class="info-item">
                        <i class="bx bx-map"></i>
                        <div><strong>Provinsi</strong><br><?= htmlspecialchars($gereja->provinsi); ?></div>
                    </div>
                    <div class="info-item">
                        <i class="bx bx-building"></i>
                        <div><strong>Kabupaten/Kota</strong><br><?= htmlspecialchars($gereja->kabupaten_kota); ?></div>
                    </div>
                    <?php if ($gereja->kontak_telepon): ?>
                    <div class="info-item">
                        <i class="bx bx-phone"></i>
                        <div><strong>Kontak</strong><br><?= htmlspecialchars($gereja->kontak_telepon); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ($gereja->deskripsi): ?>
                    <div class="info-item">
                        <i class="bx bx-book"></i>
                        <div><strong>Deskripsi</strong><br><?= htmlspecialchars($gereja->deskripsi); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($socialList)): ?>
                    <div class="info-item">
                        <i class="bx bx-share-alt"></i>
                        <div><strong>Sosial Media & Website</strong><br>
                            <div class="d-flex gap-2 mt-1 flex-wrap">
                            <?php foreach ($socialList as $sm): ?>
                                <a href="<?= htmlspecialchars($sm->url); ?>" target="_blank" class="btn btn-sm px-2 py-1" style="border:1px solid #ddd; border-radius:8px; font-size:0.8rem; text-decoration:none;">
                                    <?php if ($sm->platform == 'website'): ?><i class="bx bx-globe" style="color:#2C4463;"></i>
                                    <?php elseif ($sm->platform == 'instagram'): ?><i class="bx bxl-instagram" style="color:#E4405F;"></i>
                                    <?php elseif ($sm->platform == 'facebook'): ?><i class="bx bxl-facebook" style="color:#1877F2;"></i>
                                    <?php elseif ($sm->platform == 'twitter'): ?><i class="bx bxl-twitter" style="color:#1DA1F2;"></i>
                                    <?php elseif ($sm->platform == 'youtube'): ?><i class="bx bxl-youtube" style="color:#FF0000;"></i>
                                    <?php elseif ($sm->platform == 'tiktok'): ?><i class="bx bxl-tiktok"></i>
                                    <?php elseif ($sm->platform == 'linkedin'): ?><i class="bx bxl-linkedin" style="color:#0A66C2;"></i>
                                    <?php elseif ($sm->platform == 'whatsapp'): ?><i class="bx bxl-whatsapp" style="color:#25D366;"></i>
                                    <?php elseif ($sm->platform == 'telegram'): ?><i class="bx bxl-telegram" style="color:#0088CC;"></i>
                                    <?php else: ?><i class="bx bx-link" style="color:#2C4463;"></i>
                                    <?php endif; ?>
                                    <span class="ms-1" style="color:#333;"><?= htmlspecialchars(ucfirst($sm->platform)); ?></span>
                                </a>
                            <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="info-card">
                <div class="card-header"><i class="bx bx-calendar-event me-1"></i>Jadwal Misa</div>
                <div class="card-body">
                    <?php
                    $hariMap = array('Minggu'=>0,'Senin'=>1,'Selasa'=>2,'Rabu'=>3,'Kamis'=>4,'Jumat'=>5,'Sabtu'=>6);
                    $hariIniNum = (int)date('w');
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
                    $kategoriUrut = array('Harian','Mingguan','Hari Raya');
                    ?>
                    <?php if (empty($jadwalList)): ?>
                        <p class="text-muted mb-0">Belum ada jadwal misa untuk gereja ini.</p>
                    <?php else: ?>
                        <?php foreach ($hariUrut as $hari): if (isset($kelompok[$hari])): ?>
                        <div class="jadwal-group">
                            <h6><?= $hari == 'Spesial' ? 'Jadwal Khusus / Hari Raya' : 'Hari ' . $hari; ?></h6>
                            <?php foreach ($kelompok[$hari] as $j): ?>
                            <?php
                                $displayDate = $j->tanggal ? $j->tanggal : getNextDate($j->hari);
                            ?>
                            <div class="jadwal-item align-items-center">
                                <span class="jadwal-tanggal"><i class="bx bx-calendar me-1"></i><?= date('d/m/Y', strtotime($displayDate)); ?></span>
                                <span class="jadwal-hari"><?= $j->hari; ?></span>
                                <span class="jadwal-time"><?= date('H:i', strtotime($j->waktu_mulai)); ?></span>
                                <?php if ($j->keterangan): ?>
                                    <span class="jadwal-ket"><?= htmlspecialchars($j->keterangan); ?></span>
                                <?php endif; ?>
                                <span class="badge ms-auto" style="background:rgba(44,68,99,0.1); color:#2C4463; font-weight:700; font-size:0.7rem; white-space:nowrap;">
                                    <?= $j->kategori; ?>
                                </span>
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
                <div class="card-header"><i class="bx bx-map me-1"></i>Lokasi</div>
                <div class="card-body p-0">
                    <div id="map-detail"></div>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="https://www.google.com/maps?q=<?= $gereja->latitude; ?>,<?= $gereja->longitude; ?>" target="_blank" class="btn btn-lokasi w-100">
                            <i class="bx bx-map me-1"></i>Lihat Lokasi di Maps
                        </a>
                        <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $gereja->latitude; ?>,<?= $gereja->longitude; ?>" target="_blank" class="btn btn-navigate w-100">
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
var map = L.map("map-detail").setView([' . $gereja->latitude . ', ' . $gereja->longitude . '], 16);
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "© OpenStreetMap contributors",
    maxZoom: 18
}).addTo(map);
L.marker([' . $gereja->latitude . ', ' . $gereja->longitude . ']).addTo(map)
    .bindPopup("<b>' . htmlspecialchars($gereja->nama_gereja, ENT_QUOTES) . '</b>")
    .openPopup();
</script>')); ?>
