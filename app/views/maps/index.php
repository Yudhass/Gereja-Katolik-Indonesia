<?php $this->view('layouts/guest_openTag', array('title' => $title, 'css' => array('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'))); ?>
<?php
$hasFilter = $selectedProvinsi || $selectedKabupaten || $selectedKecamatan || $selectedKelurahan || $selectedJamDari;
$hariUrut = array('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Spesial');
$gerejaJson = array();
$BASEURL_JS = BASEURL;

foreach ($gerejaList as $g) {
    $jdw = array();
    if (isset($allJadwal[$g->id])) {
        foreach ($allJadwal[$g->id] as $j) {
            $jdw[] = array(
                'hari' => $j->hari,
                'tanggal' => $j->tanggal,
                'waktu' => date('H:i', strtotime($j->waktu_mulai)),
                'kategori' => $j->kategori,
                'keterangan' => $j->keterangan
            );
        }
    }
    $mapLat = (float)$g->latitude;
    $mapLng = (float)$g->longitude;
    
    $socmedData = array();
    if (isset($allSocial[$g->id])) {
        foreach ($allSocial[$g->id] as $s) {
            $socmedData[] = array('platform' => $s->platform, 'url' => $s->url);
        }
    }
    $gerejaJson[] = array(
        'id' => $g->id,
        'nama' => $g->nama_gereja,
        'alamat' => $g->alamat,
        'provinsi' => $g->provinsi,
        'kabkota' => $g->kabupaten_kota,
        'kecamatan' => $g->kecamatan,
        'kelurahan' => $g->kelurahan,
        'telepon' => $g->kontak_telepon,
        'deskripsi' => $g->deskripsi,
        'slug' => $g->slug ? $g->slug : $g->id,
        'lat' => $mapLat,
        'lng' => $mapLng,
        'link_maps' => $g->link_maps,
        'foto' => isset($allFoto[$g->id]) ? $allFoto[$g->id] : array(),
        'jadwal' => $jdw,
        'social' => $socmedData
    );
}
$gerejaJsonEncoded = json_encode($gerejaJson);
?>

<style>
    html, body { height: 100%; margin: 0; padding: 0; }
    body { background: #e8e8e8; overflow: hidden; padding-bottom: 0 !important; }
    #map { position: fixed; top: 48px; left: 0; right: 0; bottom: 0; z-index: 1; }
    .map-header {
        position: fixed; top: 0; left: 0; right: 0;
        z-index: 1000;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 50%, var(--primary-dark) 100%);
        color: white; padding: 0.5rem 0.75rem;
        display: flex; align-items: center; gap: 0.5rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.2);
    }
    .map-header .header-left { display: flex; align-items: center; gap: 0.5rem; flex: 1; min-width: 0; }
    .map-header .header-left h1 { font-size: 0.95rem; font-weight: 700; color: #fff; margin: 0; white-space: nowrap; }
    .map-header .header-left span { font-size: 0.7rem; opacity: 0.85; display: block; }
    .map-header .header-left .header-text { min-width: 0; }
    .map-header .btn-header { background: rgba(255,255,255,0.15); color: #fff; border: none; border-radius: 10px; padding: 0.35rem 0.65rem; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem; flex-shrink: 0; transition: background 0.2s; cursor: pointer; }
    .map-header .btn-header:hover { background: rgba(255,255,255,0.25); color: #fff; }
    .map-header .btn-header.active-filter { background: #CFA969; }
    .map-header .btn-header.active-filter:hover { background: #b8954f; }

    .leaflet-popup-content-wrapper { border-radius: 12px; }
    .leaflet-popup-content { margin: 0.5rem 0.7rem; font-size: 0.85rem; line-height: 1.4; }
    .leaflet-popup-content strong { color: var(--primary); }
    .leaflet-popup-content .popup-addr { color: #555; font-size: 0.78rem; }
    .leaflet-popup-content .popup-link { display: inline-block; margin-top: 0.3rem; background: var(--primary); color: #fff; padding: 0.2rem 0.6rem; border-radius: 50px; font-size: 0.75rem; text-decoration: none; cursor: pointer; border: none; }
    .leaflet-popup-content .popup-link:hover { background: var(--primary-dark); }

    .filter-overlay, .sheet-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 1055;
        opacity: 0; visibility: hidden; transition: opacity .3s, visibility .3s;
    }
    .filter-overlay.active, .sheet-overlay.active { opacity: 1; visibility: visible; }

    .filter-drawer {
        position: fixed; top: 0; right: -320px; bottom: 0;
        width: 320px; max-width: 85vw;
        background: #fff; z-index: 1060;
        transition: right .3s ease;
        box-shadow: -4px 0 20px rgba(0,0,0,0.15);
        display: flex; flex-direction: column;
    }
    .filter-drawer.open { right: 0; }
    .filter-drawer-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.85rem 1.1rem; border-bottom: 1px solid #eee; flex-shrink: 0;
    }
    .filter-drawer-header h6 { font-weight: 700; color: var(--primary); margin: 0; font-size: 0.95rem; }
    .filter-drawer-header .btn-close { font-size: 0.85rem; background: none; border: none; padding: 0.3rem; cursor: pointer; color: #444; }
    .filter-drawer-body { flex: 1; overflow-y: auto; padding: 1.1rem; padding-bottom: 80px; }
    .filter-drawer-body label { font-weight: 600; font-size: 0.8rem; color: #333; margin-bottom: 0.25rem; display: block; }
    .filter-drawer-body select, .filter-drawer-body input { border-radius: 10px; border: 1px solid #bbb; padding: 0.45rem 0.7rem; font-size: 0.85rem; width: 100%; }
    .filter-drawer-body select:focus, .filter-drawer-body input:focus { border-color: var(--primary); box-shadow: 0 0 0 0.2rem rgba(44,68,99,0.1); outline: none; }
    .filter-drawer-body .mb-3 { margin-bottom: 0.9rem; }
    .filter-drawer-body .btn-filter { background: var(--primary); color: #fff; border: none; border-radius: 10px; padding: 0.55rem 1.2rem; font-weight: 600; font-size: 0.88rem; width: 100%; }
    .filter-drawer-body .btn-filter:hover { background: var(--primary-dark); }
    .filter-drawer-body .btn-reset { background: transparent; color: #444; border: 1px solid #bbb; border-radius: 10px; padding: 0.55rem 1rem; font-weight: 600; font-size: 0.85rem; text-decoration: none; display: block; text-align: center; width: 100%; }
    .filter-drawer-body .btn-reset:hover { background: #f5f5f5; }

    .sheet-panel {
        position: fixed; left: 0; right: 0; bottom: 0;
        z-index: 1060;
        background: #fff;
        border-radius: 20px 20px 0 0;
        box-shadow: 0 -4px 24px rgba(0,0,0,0.15);
        transform: translateY(100%);
        transition: transform .35s ease, visibility .35s ease;
        max-height: 75vh;
        display: flex;
        flex-direction: column;
        visibility: hidden;
        pointer-events: none;
    }
    .sheet-panel.open { transform: translateY(0); visibility: visible; pointer-events: auto; }
    .sheet-handle { width: 36px; height: 4px; background: #ccc; border-radius: 4px; margin: 10px auto 6px; flex-shrink: 0; }
    .sheet-header { display: flex; align-items: flex-start; justify-content: space-between; padding: 0.25rem 1.1rem 0.6rem; border-bottom: 1px solid #f0f0f0; flex-shrink: 0; }
    .sheet-header h3 { font-size: 1rem; font-weight: 700; color: var(--primary); margin: 0; line-height: 1.3; padding-right: 0.5rem; }
    .sheet-header .btn-close { background: none; border: none; padding: 0.3rem; cursor: pointer; color: #888; font-size: 1.2rem; flex-shrink: 0; }
    .sheet-body { flex: 1; overflow-y: auto; padding: 0.75rem 1.1rem; }
    .sheet-body .info-row { display: flex; gap: 0.6rem; padding: 0.4rem 0; }
    .sheet-body .info-row i { color: var(--primary); width: 1.2rem; font-size: 1.05rem; flex-shrink: 0; margin-top: 0.1rem; }
    .sheet-body .info-row div { font-size: 0.85rem; color: #444; line-height: 1.5; }
    .sheet-body .section-label { font-weight: 700; font-size: 0.8rem; color: var(--primary); margin: 0.7rem 0 0.4rem; padding-bottom: 0.3rem; border-bottom: 1.5px solid var(--primary); }
    .sheet-body .jadwal-sheet { display: flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0; border-bottom: 1px solid #f5f5f5; font-size: 0.82rem; }
    .sheet-body .jadwal-sheet:last-child { border-bottom: none; }
    .sheet-body .jadwal-sheet .jw-day { font-weight: 600; color: #2C4463; flex: 0 0 3rem; }
    .sheet-body .jadwal-sheet .jw-time { font-weight: 700; color: var(--primary); flex: 0 0 2.6rem; background: rgba(44,68,99,0.08); padding: 0.05rem 0.3rem; border-radius: 4px; text-align: center; font-size: 0.78rem; }
    .sheet-body .jadwal-sheet .jw-ket { color: #555; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.78rem; }
    .sheet-body .jadwal-sheet .jw-badge { background: rgba(44,68,99,0.1); color: #2C4463; font-weight: 700; font-size: 0.58rem; padding: 0.1rem 0.35rem; border-radius: 3px; flex-shrink: 0; }
    .sheet-body .sheet-deskripsi { font-size: 0.83rem; color: #444; line-height: 1.6; margin-top: 0.3rem; }
    .sheet-body .sheet-foto-wrap { position: relative; border-radius: 12px; overflow: hidden; margin-bottom: 0.5rem; }
    .sheet-body .sheet-foto-wrap img { width: 100%; height: 160px; object-fit: cover; background: #eee; display: none; }
    .sheet-body .sheet-foto-wrap img.active { display: block; }
    .sheet-body .sheet-foto-nav { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.35); color: #fff; border: none; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1rem; transition: background .2s; z-index: 2; }
    .sheet-body .sheet-foto-nav:hover { background: rgba(0,0,0,0.6); }
    .sheet-body .sheet-foto-nav.prev { left: 6px; }
    .sheet-body .sheet-foto-nav.next { right: 6px; }
    .sheet-body .sheet-foto-dots { position: absolute; bottom: 6px; left: 50%; transform: translateX(-50%); display: flex; gap: 4px; }
    .sheet-body .sheet-foto-dots span { width: 6px; height: 6px; border-radius: 50%; background: rgba(255,255,255,0.5); cursor: pointer; }
    .sheet-body .sheet-foto-dots span.active { background: #fff; }
    .sheet-body .sheet-foto-placeholder { width: 100%; height: 100px; background: linear-gradient(135deg, var(--primary-light), var(--primary-dark)); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.5rem; }
    .sheet-body .sheet-foto-placeholder i { font-size: 3rem; color: rgba(255,255,255,0.3); }
    .sheet-body .socmed-sheet { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.3rem; }
    .sheet-footer { display: flex; gap: 0.6rem; padding: 0.7rem 1.1rem; padding-bottom: calc(0.7rem + env(safe-area-inset-bottom, 0px)); border-top: 1px solid #f0f0f0; flex-shrink: 0; }
    .sheet-footer .btn-sheet { flex: 1; border-radius: 12px; padding: 0.5rem 1rem; font-weight: 600; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 0.3rem; border: none; cursor: pointer; }
    .socmed-sheet-btn { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.3rem 0.65rem; border-radius: 8px; font-size: 0.72rem; font-weight: 600; text-decoration: none; transition: all .2s; border: none; cursor: pointer; }
    .socmed-sheet-btn:hover { transform: translateY(-1px); }
    .socmed-sheet-btn .bx, .socmed-sheet-btn .bxl { font-size: 1rem; }
    .ssocmed-website { background: #2C4463; color: #fff; }
    .ssocmed-website:hover { background: #1A2D47; color: #fff; }
    .ssocmed-instagram { background: linear-gradient(135deg, #833AB4, #FD1D1D, #F77737); color: #fff; }
    .ssocmed-instagram:hover { color: #fff; }
    .ssocmed-facebook { background: #1877F2; color: #fff; }
    .ssocmed-facebook:hover { background: #0f6bd6; color: #fff; }
    .ssocmed-twitter { background: #1DA1F2; color: #fff; }
    .ssocmed-twitter:hover { background: #0d8bda; color: #fff; }
    .ssocmed-youtube { background: #FF0000; color: #fff; }
    .ssocmed-youtube:hover { background: #d60000; color: #fff; }
    .ssocmed-tiktok { background: #010101; color: #fff; }
    .ssocmed-tiktok:hover { background: #333; color: #fff; }
    .ssocmed-linkedin { background: #0A66C2; color: #fff; }
    .ssocmed-linkedin:hover { background: #004182; color: #fff; }
    .ssocmed-whatsapp { background: #25D366; color: #fff; }
    .ssocmed-whatsapp:hover { background: #1da851; color: #fff; }
    .ssocmed-telegram { background: #0088CC; color: #fff; }
    .ssocmed-telegram:hover { background: #0077b3; color: #fff; }
    .ssocmed-other { background: #6c757d; color: #fff; }
    .ssocmed-other:hover { background: #5a6268; color: #fff; }
    .sheet-footer .btn-sheet-primary { background: var(--primary); color: #fff; }
    .sheet-footer .btn-sheet-primary:hover { background: var(--primary-dark); color: #fff; }
    .sheet-footer .btn-sheet-outline { background: transparent; border: 1.5px solid var(--primary); color: var(--primary); }
    .sheet-footer .btn-sheet-outline:hover { background: var(--primary); color: #fff; }

    @media (min-width: 768px) {
        .map-header { padding: 0.65rem 1.2rem; }
        .map-header .header-left h1 { font-size: 1.05rem; }
        .map-header .header-left span { font-size: 0.75rem; }
        .map-header .btn-header { padding: 0.4rem 0.8rem; font-size: 0.88rem; }
        .sheet-panel { max-width: 440px; left: auto; right: 1rem; bottom: 1rem; border-radius: 20px; max-height: 80vh; }
        .sheet-panel.open { transform: translateY(0); }
        .sheet-handle { display: none; }
    }
    @media (max-width: 767px) {
        .map-header .header-left h1 { font-size: 0.85rem; }
        .map-header .header-left span { font-size: 0.65rem; }
        .map-header .btn-header { font-size: 0.78rem; padding: 0.3rem 0.5rem; }
        .leaflet-control-zoom { margin-bottom: 60px !important; }
        .leaflet-control-attribution { margin-bottom: 52px !important; font-size: 0.6rem !important; }
        .filter-drawer { width: 290px; }
        .sheet-panel { max-height: 68vh; }
        .sheet-body .sheet-foto-wrap img { height: 130px; }
    }
</style>

<div class="filter-overlay" id="filterOverlay" onclick="toggleFilter()"></div>
<div class="filter-drawer" id="filterDrawer">
    <div class="filter-drawer-header">
        <h6><i class="bx bx-filter-alt me-1"></i>Filter Peta</h6>
        <button type="button" class="btn-close" onclick="toggleFilter()"><i class="bx bx-x fs-4"></i></button>
    </div>
    <div class="filter-drawer-body">
        <form method="GET" action="<?= BASEURL; ?>maps">
            <div class="mb-3">
                <label><i class="bx bx-map me-1"></i>Provinsi</label>
                <select name="provinsi">
                    <option value="">Semua Provinsi</option>
                    <?php foreach ($provinsiList as $p): ?>
                    <option value="<?= htmlspecialchars($p); ?>" <?= $selectedProvinsi == $p ? 'selected' : ''; ?>><?= htmlspecialchars($p); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label><i class="bx bx-building me-1"></i>Kabupaten/Kota</label>
                <select name="kabupaten">
                    <option value="">Semua Kabupaten/Kota</option>
                    <?php foreach ($kabupatenList as $k): ?>
                    <option value="<?= htmlspecialchars($k); ?>" <?= $selectedKabupaten == $k ? 'selected' : ''; ?>><?= htmlspecialchars($k); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label><i class="bx bx-detail me-1"></i>Kecamatan</label>
                <select name="kecamatan">
                    <option value="">Semua Kecamatan</option>
                    <?php foreach ($kecamatanList as $k): ?>
                    <option value="<?= htmlspecialchars($k); ?>" <?= $selectedKecamatan == $k ? 'selected' : ''; ?>><?= htmlspecialchars($k); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label><i class="bx bx-home me-1"></i>Kelurahan</label>
                <select name="kelurahan">
                    <option value="">Semua Kelurahan</option>
                    <?php foreach ($kelurahanList as $k): ?>
                    <option value="<?= htmlspecialchars($k); ?>" <?= $selectedKelurahan == $k ? 'selected' : ''; ?>><?= htmlspecialchars($k); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label><i class="bx bx-time me-1"></i>Jam Mulai Misa &ge;</label>
                <input type="time" name="jam_dari" value="<?= htmlspecialchars($selectedJamDari); ?>">
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-filter flex-fill"><i class="bx bx-filter me-1"></i>Terapkan</button>
                <a href="<?= BASEURL; ?>maps" class="btn-reset flex-fill"><i class="bx bx-x me-1"></i>Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="sheet-overlay" id="sheetOverlay" onclick="closeSheet()"></div>
<div class="sheet-panel" id="sheetPanel">
    <div class="sheet-handle"></div>
    <div class="sheet-header">
        <h3 id="sheetTitle">-</h3>
        <button type="button" class="btn-close" onclick="closeSheet()"><i class="bx bx-x"></i></button>
    </div>
    <div class="sheet-body" id="sheetBody"></div>
    <div class="sheet-footer" id="sheetFooter"></div>
</div>

<div class="map-header">
    <div class="header-left">
        <a href="<?= BASEURL; ?>" class="btn-header"><i class="bx bx-arrow-back"></i></a>
        <i class="bx bx-map" style="font-size:1.2rem; flex-shrink:0;"></i>
        <div class="header-text">
            <h1>Peta Gereja Katolik</h1>
            <span><?= count($gerejaList); ?> gereja ditampilkan</span>
        </div>
    </div>
    <button class="btn-header <?= $hasFilter ? 'active-filter' : ''; ?>" onclick="toggleFilter()">
        <i class="bx bx-filter-alt"></i>
        <?php if ($hasFilter): ?><i class="bx bx-check"></i><?php endif; ?>
    </button>
</div>

<div id="map"></div>

<?php
$mapScripts = '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>';
$mapScripts .= '<script>
var BASEURL = "' . $BASEURL_JS . '";
var HARI_URUT = ["Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu","Spesial"];

var map = L.map("map", {
    center: [-2.5489, 118.0149],
    zoom: 5,
    zoomControl: true
});

var osm = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {maxZoom: 19, attribution: "&copy; OpenStreetMap"});
var carto = L.tileLayer("https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png", {maxZoom: 19, attribution: "&copy; CARTO"});
var dark = L.tileLayer("https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png", {maxZoom: 19, attribution: "&copy; CARTO"});
var satellite = L.tileLayer("https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}", {maxZoom: 19, attribution: "&copy; Esri"});
var cyclosm = L.tileLayer("https://{s}.tile-cyclosm.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png", {maxZoom: 20, attribution: "&copy; OpenStreetMap contributors &amp; <a href=\"https://www.cyclosm.org\">CyclOSM</a>"});
var hot = L.tileLayer("https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png", {maxZoom: 20, attribution: "&copy; OpenStreetMap contributors, Humanitarian OpenStreetMap Team"});
var shortbread = L.tileLayer("https://tiles.shortbread.io/{z}/{x}/{y}.png", {maxZoom: 19, attribution: "&copy; OpenStreetMap contributors, Shortbread"});

osm.addTo(map);
L.control.layers({
    "OpenStreetMap": osm, "Carto Light": carto, "Dark": dark,
    "Satelit": satellite, "CyclOSM": cyclosm, "HOT": hot, "Shortbread": shortbread
}, null, {position: "topright"}).addTo(map);

var markers = [];
var bounds = [];
var gerejaData = ' . $gerejaJsonEncoded . ';

function showGerejaDetail(idx) {
    var d = gerejaData[idx];
    if (!d) return;
    document.getElementById("sheetTitle").textContent = d.nama;

    var body = "";

    var fotoList = (Array.isArray(d.foto) && d.foto.length > 0) ? d.foto : [];
    if (fotoList.length > 0) {
        body += "<div class=\"sheet-foto-wrap\" id=\"fotoWrap\">";
        for (var fi = 0; fi < fotoList.length; fi++) {
            body += "<img src=\"" + escAttr(fotoList[fi]) + "\" class=\"" + (fi === 0 ? "active" : "") + "\" alt=\"" + escAttr(d.nama) + "\" onerror=\"this.style.display=\'none\'\">";
        }
        if (fotoList.length > 1) {
            body += "<button class=\"sheet-foto-nav prev\" onclick=\"fotoPrev()\"><i class=\"bx bx-chevron-left\"></i></button>";
            body += "<button class=\"sheet-foto-nav next\" onclick=\"fotoNext()\"><i class=\"bx bx-chevron-right\"></i></button>";
            body += "<div class=\"sheet-foto-dots\" id=\"fotoDots\">";
            for (var fi = 0; fi < fotoList.length; fi++) {
                body += "<span class=\"" + (fi === 0 ? "active" : "") + "\" onclick=\"fotoGo(" + fi + ")\"></span>";
            }
            body += "</div>";
        }
        body += "</div>";
    } else {
        body += "<div class=\"sheet-foto-placeholder\"><i class=\"bx bx-church\"></i></div>";
    }

    body += "<div class=\"info-row\"><i class=\"bx bx-map-pin\"></i><div>" + escHtml(d.alamat) + "</div></div>";
    body += "<div class=\"info-row\"><i class=\"bx bx-map\"></i><div>" + escHtml(d.provinsi) + (d.kabkota ? ", " + escHtml(d.kabkota) : "") + "</div></div>";
    if (d.kecamatan) { body += "<div class=\"info-row\"><i class=\"bx bx-detail\"></i><div>Kec. " + escHtml(d.kecamatan) + "</div></div>"; }
    if (d.kelurahan) { body += "<div class=\"info-row\"><i class=\"bx bx-home\"></i><div>Kel. " + escHtml(d.kelurahan) + "</div></div>"; }
    if (d.telepon) { body += "<div class=\"info-row\"><i class=\"bx bx-phone\"></i><div>" + escHtml(d.telepon) + "</div></div>"; }

    if (d.social && d.social.length > 0) {
        body += "<div class=\"section-label\"><i class=\"bx bx-share-alt me-1\"></i>Sosial Media</div>";
        body += "<div class=\"socmed-sheet\">";
        for (var si = 0; si < d.social.length; si++) {
            var s = d.social[si];
            var sCls = "ssocmed-other";
            var sIcon = "bx bx-link";
            if (s.platform == "website") { sCls = "ssocmed-website"; sIcon = "bx bx-globe"; }
            else if (s.platform == "instagram") { sCls = "ssocmed-instagram"; sIcon = "bx bxl-instagram"; }
            else if (s.platform == "facebook") { sCls = "ssocmed-facebook"; sIcon = "bx bxl-facebook"; }
            else if (s.platform == "twitter") { sCls = "ssocmed-twitter"; sIcon = "bx bxl-twitter"; }
            else if (s.platform == "youtube") { sCls = "ssocmed-youtube"; sIcon = "bx bxl-youtube"; }
            else if (s.platform == "tiktok") { sCls = "ssocmed-tiktok"; sIcon = "bx bxl-tiktok"; }
            else if (s.platform == "linkedin") { sCls = "ssocmed-linkedin"; sIcon = "bx bxl-linkedin"; }
            else if (s.platform == "whatsapp") { sCls = "ssocmed-whatsapp"; sIcon = "bx bxl-whatsapp"; }
            else if (s.platform == "telegram") { sCls = "ssocmed-telegram"; sIcon = "bx bxl-telegram"; }
            body += "<a href=\"" + escAttr(s.url) + "\" target=\"_blank\" rel=\"noopener\" class=\"socmed-sheet-btn " + sCls + "\"><i class=\"" + sIcon + "\"></i>" + escHtml(s.platform.charAt(0).toUpperCase() + s.platform.slice(1)) + "</a>";
        }
        body += "</div>";
    }

    if (d.deskripsi) {
        body += "<div class=\"section-label\"><i class=\"bx bx-info-circle me-1\"></i>Tentang</div>";
        body += "<div class=\"sheet-deskripsi\">" + escHtml(d.deskripsi) + "</div>";
    }

    if (d.jadwal && d.jadwal.length > 0) {
        body += "<div class=\"section-label\"><i class=\"bx bx-calendar-event me-1\"></i>Jadwal Misa</div>";
        var grouped = {};
        for (var k = 0; k < d.jadwal.length; k++) {
            var j = d.jadwal[k];
            if (!grouped[j.hari]) grouped[j.hari] = [];
            grouped[j.hari].push(j);
        }
        for (var h = 0; h < HARI_URUT.length; h++) {
            var hariNama = HARI_URUT[h];
            if (grouped[hariNama]) {
                for (var jj = 0; jj < grouped[hariNama].length; jj++) {
                    var item = grouped[hariNama][jj];
                    body += "<div class=\"jadwal-sheet\">";
                    body += "<span class=\"jw-day\">" + escHtml(item.hari) + "</span>";
                    body += "<span class=\"jw-time\">" + escHtml(item.waktu) + "</span>";
                    if (item.keterangan) body += "<span class=\"jw-ket\">" + escHtml(item.keterangan) + "</span>";
                    body += "<span class=\"jw-badge\">" + escHtml(item.kategori) + "</span>";
                    body += "</div>";
                }
            }
        }
    }

    document.getElementById("sheetBody").innerHTML = body;
    var mapsUrl = d.link_maps ? d.link_maps : ("https://www.google.com/maps?q=" + d.lat + "," + d.lng);
    document.getElementById("sheetFooter").innerHTML =
        "<a href=\"" + mapsUrl + "\" target=\"_blank\" class=\"btn-sheet btn-sheet-outline\"><i class=\"bx bx-map\"></i> Lihat Lokasi</a>" +
        "<a href=\"https://www.google.com/maps/dir/?api=1&destination=" + d.lat + "," + d.lng + "\" target=\"_blank\" class=\"btn-sheet btn-sheet-outline\"><i class=\"bx bx-navigation\"></i> Rute</a>" +
        "<a href=\"" + BASEURL + "gereja/" + encodeURIComponent(d.slug) + "?from=maps\" class=\"btn-sheet btn-sheet-primary\"><i class=\"bx bx-detail\"></i> Detail</a>";

    fotoIdx = 0;
    document.getElementById("sheetPanel").classList.add("open");
    document.getElementById("sheetOverlay").classList.add("active");
    document.body.style.overflow = "hidden";
}

var fotoIdx = 0;
function fotoShow(n) {
    var wrap = document.getElementById("fotoWrap");
    if (!wrap) return;
    var imgs = wrap.querySelectorAll("img");
    var dots = wrap.querySelectorAll(".sheet-foto-dots span");
    for (var i = 0; i < imgs.length; i++) imgs[i].classList.remove("active");
    for (var i = 0; i < dots.length; i++) dots[i].classList.remove("active");
    if (imgs[n]) imgs[n].classList.add("active");
    if (dots[n]) dots[n].classList.add("active");
}
function fotoPrev() { var wrap = document.getElementById("fotoWrap"); if (!wrap) return; var imgs = wrap.querySelectorAll("img"); fotoIdx = (fotoIdx - 1 + imgs.length) % imgs.length; fotoShow(fotoIdx); }
function fotoNext() { var wrap = document.getElementById("fotoWrap"); if (!wrap) return; var imgs = wrap.querySelectorAll("img"); fotoIdx = (fotoIdx + 1) % imgs.length; fotoShow(fotoIdx); }
function fotoGo(n) { fotoIdx = n; fotoShow(n); }

function closeSheet() {
    document.getElementById("sheetPanel").classList.remove("open");
    document.getElementById("sheetOverlay").classList.remove("active");
    document.body.style.overflow = "";
}

function escHtml(s) { if (!s) return ""; return s.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\"/g, "&quot;"); }
function escAttr(s) { if (!s) return ""; return s.replace(/\"/g, "&quot;").replace(/</g, "&lt;").replace(/>/g, "&gt;"); }
';

foreach ($gerejaList as $i => $g) {
    $lat = (float)$g->latitude;
    $lng = (float)$g->longitude;

    $mapScripts .= "
var icon = L.divIcon({html: '<div style=\"background:#2C4463;color:#fff;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.3);font-size:1rem;\"><i class=\"bx bx-church\"></i></div>', className: '', iconSize: [32, 32], iconAnchor: [16, 16], popupAnchor: [0, -18]});
var marker = L.marker([$lat, $lng], {icon: icon}).addTo(map);
marker.bindPopup('<strong>' + escHtml(gerejaData[$i].nama) + '</strong><br><span class=\"popup-addr\"><i class=\"bx bx-map-pin\"></i> ' + escHtml(gerejaData[$i].alamat) + '</span><br><button class=\"popup-link\" onclick=\"showGerejaDetail($i)\"><i class=\"bx bx-detail\"></i> Detail</button>');
markers.push(marker);
bounds.push([$lat, $lng]);
";
}

$mapScripts .= '
if (bounds.length > 0) {
    map.fitBounds(bounds, {padding: [30, 30], maxZoom: 14});
}

function toggleFilter() {
    var drawer = document.getElementById("filterDrawer");
    var overlay = document.getElementById("filterOverlay");
    drawer.classList.toggle("open");
    overlay.classList.toggle("active");
    document.body.style.overflow = drawer.classList.contains("open") ? "hidden" : "";
}
</script>';

$this->view('layouts/guest_bottom_nav', array('activeMenu' => 'maps'));
?>
<?php $this->view('layouts/guest_closeTag', array('scripts' => $mapScripts)); ?>
