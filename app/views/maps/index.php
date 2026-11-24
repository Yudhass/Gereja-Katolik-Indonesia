<?php
$hasMapsFilter = !empty($selectedProvinsi) || !empty($selectedKabupaten) || !empty($selectedKecamatan) || !empty($selectedKelurahan) || !empty($selectedJamDari) || !empty($selectedNamaGereja);
$mapsSeoDesc = 'Peta interaktif lokasi gereja Katolik di seluruh Indonesia. Filter berdasarkan provinsi, kabupaten/kota, kecamatan, atau jam misa untuk menemukan gereja terdekat.';
$mapsSeo = array(
    'title' => 'Peta Gereja Katolik Indonesia — Lokasi Paroki Terdekat | Info Gereja',
    'description' => $mapsSeoDesc,
    'keywords' => 'peta gereja katolik, lokasi gereja katolik terdekat, maps gereja katolik indonesia, rute gereja katolik',
    'canonical' => BASEURL . 'maps',
    'robots' => $hasMapsFilter ? 'noindex, follow' : 'index, follow',
    'schema' => array(
        array(
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => 'Peta Gereja Katolik Indonesia',
            'url' => BASEURL . 'maps',
            'description' => $mapsSeoDesc,
        ),
        array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array(
                array('@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => BASEURL),
                array('@type' => 'ListItem', 'position' => 2, 'name' => 'Peta Gereja', 'item' => BASEURL . 'maps'),
            ),
        ),
    ),
);
?>
<?php $this->view('layouts/guest_openTag', array(
    'title' => $title,
    'seo' => $mapsSeo,
    'bodyClass' => 'gki-maps-page',
    'css' => array(
        'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
        'https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css',
        'https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css'
    )
)); ?>
<?php
$hasFilter = $selectedProvinsi || $selectedKabupaten || $selectedKecamatan || $selectedKelurahan || $selectedJamDari || $selectedNamaGereja;
$hariUrut = array('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Spesial');
$gerejaJson = array();
$BASEURL_JS = BASEURL;

foreach ($gerejaList as $g) {
    $jdw = array();
    if (isset($allJadwal[$g->id])) {
        foreach ($allJadwal[$g->id] as $j) {
            $jdw[] = array(
                'hari' => decode_text($j->hari),
                'tanggal' => $j->tanggal,
                'waktu' => date('H:i', strtotime($j->waktu_mulai)),
                'kategori' => decode_text($j->kategori),
                'keterangan' => isset($j->keterangan) ? decode_text($j->keterangan) : ''
            );
        }
    }
    $mapLat = (float)$g->latitude;
    $mapLng = (float)$g->longitude;

    $socmedData = array();
    if (isset($allSocial[$g->id])) {
        foreach ($allSocial[$g->id] as $s) {
            if (is_object($s)) {
                $socmedData[] = array('platform' => $s->platform, 'url' => $s->url);
            } elseif (is_array($s)) {
                $socmedData[] = array('platform' => $s['platform'], 'url' => $s['url']);
            }
        }
    }

    $fotoUrls = array();
    if (isset($allFoto[$g->id]) && is_array($allFoto[$g->id])) {
        foreach (array_slice($allFoto[$g->id], 0, 5) as $f) {
            $fotoUrls[] = is_object($f) ? $f->foto_url : $f;
        }
    }

    $deskripsi = isset($g->deskripsi) ? decode_text($g->deskripsi) : '';
    if (function_exists('mb_strlen') && mb_strlen($deskripsi, 'UTF-8') > 800) {
        $deskripsi = mb_substr($deskripsi, 0, 800, 'UTF-8');
    } elseif (strlen($deskripsi) > 800) {
        $deskripsi = substr($deskripsi, 0, 800);
    }

    $gerejaJson[] = array(
        'id' => $g->id,
        'nama' => decode_text($g->nama_gereja),
        'alamat' => decode_text($g->alamat),
        'provinsi' => decode_text($g->provinsi),
        'kabkota' => decode_text($g->kabupaten_kota),
        'kecamatan' => decode_text($g->kecamatan),
        'kelurahan' => decode_text($g->kelurahan),
        'telepon' => decode_text($g->kontak_telepon),
        'deskripsi' => $deskripsi,
        'slug' => $g->slug ? $g->slug : $g->id,
        'lat' => $mapLat,
        'lng' => $mapLng,
        'link_maps' => $g->link_maps,
        'foto' => $fotoUrls,
        'jadwal' => $jdw,
        'social' => $socmedData
    );
}
$gerejaJsonEncoded = json_encode($gerejaJson);
if ($gerejaJsonEncoded === false) {
    $gerejaJsonEncoded = '[]';
}
$totalGereja = isset($gerejaJson) ? count($gerejaJson) : 0;
unset($gerejaJson, $gerejaList, $allJadwal, $allFoto, $allSocial);

ob_start();
?>
<span class="gki-count-pill d-none d-sm-inline-flex" id="mapCountPill"><?= (int)$totalGereja; ?> gereja</span>
<button type="button" class="gki-btn gki-btn-outline gki-btn-sm d-md-none <?= $hasFilter ? 'is-active' : ''; ?>" onclick="toggleFilter()" aria-label="Filter peta">
    <i class="bx bx-filter-alt" aria-hidden="true"></i>
    <span>Filter</span>
</button>
<?php
$mapsNavActions = ob_get_clean();
?>

<style>
    body.gki-maps-page {
        height: 100dvh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        padding-bottom: 0;
    }
    .maps-shell {
        flex: 1 1 auto;
        min-height: 0;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .maps-filter-bar {
        background: var(--surface);
        border-bottom: 1px solid var(--border);
        padding: 0.65rem 1rem;
        flex-shrink: 0;
        z-index: 1010;
    }
    .maps-filter-form {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr)) auto;
        gap: 0.5rem;
        align-items: end;
        max-width: 1400px;
        margin-inline: auto;
    }
    .maps-filter-bar .gki-field label {
        font-size: 0.72rem;
        margin-bottom: 0.18rem;
    }
    .maps-filter-bar .gki-select,
    .maps-filter-bar .gki-input {
        min-height: 34px;
        padding: 0.25rem 0.55rem;
        font-size: 0.8rem;
    }
    .maps-filter-actions {
        display: flex;
        gap: 0.35rem;
    }
    .maps-stage {
        flex: 1 1 auto;
        min-height: 0;
        position: relative;
    }
    #map {
        position: absolute;
        inset: 0;
        z-index: 1;
    }
    @media (max-width: 1199px) and (min-width: 768px) {
        .maps-filter-form {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .maps-filter-actions {
            grid-column: 1 / -1;
            justify-content: flex-end;
        }
    }
    @media (max-width: 767px) {
        .maps-filter-bar {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 320px;
            max-width: 88vw;
            height: 100dvh;
            padding: 0;
            border-bottom: none;
            border-radius: 16px 0 0 16px;
            box-shadow: -6px 0 28px rgba(17, 29, 24, 0.18);
            transform: translateX(100%);
            transition: transform 0.24s ease;
            z-index: 1060;
            display: flex;
            flex-direction: column;
        }
        .maps-filter-bar.open {
            transform: translateX(0);
        }
        .maps-filter-drawer-body {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 1.1rem calc(1.5rem + env(safe-area-inset-bottom, 0px));
        }
        .maps-filter-form {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            align-items: stretch;
        }
        .maps-filter-bar .gki-select,
        .maps-filter-bar .gki-input {
            min-height: 42px;
            font-size: 16px;
            padding: 0.45rem 0.7rem;
        }
        .maps-stage {
            margin-bottom: calc(58px + env(safe-area-inset-bottom, 0px));
        }
    }

    /* Leaflet popup styling */
    .leaflet-popup-content-wrapper {
        border-radius: 12px;
        box-shadow: var(--shadow-md);
    }
    .leaflet-popup-content {
        margin: 0.65rem 0.8rem;
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        font-size: 0.84rem;
        line-height: 1.45;
    }
    .leaflet-popup-content strong {
        color: var(--ink);
        font-weight: 700;
    }
    .leaflet-popup-content .popup-addr {
        color: var(--ink-secondary);
        font-size: 0.78rem;
    }
    .leaflet-popup-content .popup-link {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        margin-top: 0.45rem;
        background: var(--primary);
        color: #fff;
        padding: 0.3rem 0.75rem;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
    }
    .leaflet-popup-content .popup-link:hover {
        background: var(--primary-hover);
    }

    /* Detail Sheet Panel */
    .sheet-overlay {
        position: fixed;
        inset: 0;
        background: rgba(17, 29, 24, 0.42);
        z-index: 1055;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.24s ease, visibility 0.24s ease;
    }
    .sheet-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    .sheet-panel {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1060;
        background: var(--surface);
        border-radius: 20px 20px 0 0;
        box-shadow: 0 -6px 28px rgba(17, 29, 24, 0.16);
        transform: translateY(100%);
        transition: transform 0.28s ease, visibility 0.28s ease;
        max-height: 78vh;
        display: flex;
        flex-direction: column;
        visibility: hidden;
        pointer-events: none;
    }
    .sheet-panel.open {
        transform: translateY(0);
        visibility: visible;
        pointer-events: auto;
    }
    @media (min-width: 768px) {
        .sheet-panel {
            max-width: 430px;
            left: auto;
            right: 1rem;
            bottom: 1rem;
            border-radius: 16px;
            border: 1px solid var(--border);
            max-height: calc(100dvh - 140px);
        }
        .sheet-handle {
            display: none;
        }
    }
    .sheet-handle {
        width: 38px;
        height: 4px;
        background: var(--border-strong);
        border-radius: 4px;
        margin: 10px auto 6px;
        flex-shrink: 0;
    }
    .sheet-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.85rem 1.1rem;
        border-bottom: 1px solid var(--border);
        flex-shrink: 0;
    }
    .sheet-header h3 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--ink);
        margin: 0;
        line-height: 1.35;
    }
    .sheet-body {
        flex: 1;
        overflow-y: auto;
        padding: 0.9rem 1.1rem;
    }
    .sheet-body .info-row {
        display: flex;
        gap: 0.55rem;
        padding: 0.35rem 0;
        font-size: 0.84rem;
        color: var(--ink-secondary);
    }
    .sheet-body .info-row i {
        color: var(--primary);
        font-size: 1rem;
        flex-shrink: 0;
        margin-top: 0.15rem;
    }
    .sheet-body .section-label {
        font-weight: 700;
        font-size: 0.82rem;
        color: var(--ink);
        margin: 0.85rem 0 0.45rem;
        padding-bottom: 0.35rem;
        border-bottom: 1px solid var(--border);
    }
    .sheet-body .jadwal-sheet {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.38rem 0;
        border-bottom: 1px solid var(--surface-subtle);
        font-size: 0.8rem;
        font-variant-numeric: tabular-nums;
    }
    .sheet-body .jadwal-sheet:last-child { border-bottom: none; }
    .sheet-body .jadwal-sheet .jw-day { font-weight: 700; color: var(--ink); flex: 0 0 3.4rem; }
    .sheet-body .jadwal-sheet .jw-time { font-weight: 700; color: var(--primary); background: var(--primary-soft); padding: 0.1rem 0.45rem; border-radius: 5px; }
    .sheet-body .jadwal-sheet .jw-ket { color: var(--ink-secondary); flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .sheet-body .jadwal-sheet .jw-badge { background: var(--surface-subtle); color: var(--ink-secondary); font-weight: 700; font-size: 0.68rem; padding: 0.12rem 0.45rem; border-radius: var(--radius-pill); flex-shrink: 0; }
    .sheet-body .sheet-deskripsi { font-size: 0.83rem; color: var(--ink-secondary); line-height: 1.6; }
    .sheet-body .sheet-foto-wrap { position: relative; border-radius: 12px; overflow: hidden; margin-bottom: 0.65rem; background: var(--primary-dark); }
    .sheet-body .sheet-foto-wrap img { width: 100%; height: 170px; object-fit: cover; display: none; }
    .sheet-body .sheet-foto-wrap img.active { display: block; }
    .sheet-body .sheet-foto-nav { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(17, 29, 24, 0.55); color: #fff; border: none; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 2; }
    .sheet-body .sheet-foto-nav.prev { left: 8px; }
    .sheet-body .sheet-foto-nav.next { right: 8px; }
    .sheet-body .sheet-foto-dots { position: absolute; bottom: 8px; left: 50%; transform: translateX(-50%); display: flex; gap: 5px; }
    .sheet-body .sheet-foto-dots span { width: 7px; height: 7px; border-radius: 50%; background: rgba(255,255,255,0.55); cursor: pointer; }
    .sheet-body .sheet-foto-dots span.active { background: #fff; }
    .sheet-body .sheet-foto-placeholder { width: 100%; height: 110px; background: linear-gradient(145deg, var(--primary), var(--primary-dark)); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.65rem; color: rgba(255,255,255,0.35); font-size: 2.75rem; }
    .sheet-body .socmed-sheet { display: flex; flex-wrap: wrap; gap: 0.4rem; }
    .socmed-sheet-btn { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.3rem 0.65rem; border-radius: 8px; font-size: 0.75rem; font-weight: 600; border: 1px solid var(--border-strong); background: var(--surface); color: var(--ink); text-decoration: none; }
    .socmed-sheet-btn:hover { background: var(--primary-soft); border-color: var(--primary); color: var(--primary); }
    .sheet-footer {
        display: flex;
        gap: 0.5rem;
        padding: 0.8rem 1.1rem calc(0.8rem + env(safe-area-inset-bottom, 0px));
        border-top: 1px solid var(--border);
        flex-shrink: 0;
    }
    .sheet-footer .gki-btn {
        flex: 1;
        min-height: 38px;
        font-size: 0.82rem;
        padding: 0.4rem 0.65rem;
    }
</style>

<?php $this->view('layouts/guest_navbar', array('activeMenu' => 'maps', 'mobileActions' => $mapsNavActions)); ?>

<div class="maps-shell">
    <div class="gki-drawer-overlay d-md-none" id="filterOverlay" onclick="toggleFilter()"></div>

    <aside class="maps-filter-bar" id="filterDrawer" aria-label="Filter peta gereja">
        <div class="gki-drawer-header d-md-none">
            <h2><i class="bx bx-filter-alt" aria-hidden="true"></i>Filter Peta</h2>
            <button type="button" class="btn-close" onclick="toggleFilter()" aria-label="Tutup filter"></button>
        </div>
        <div class="maps-filter-drawer-body">
            <form method="GET" action="<?= BASEURL; ?>maps" class="maps-filter-form" onsubmit="event.preventDefault(); applyFilter();">
                <div class="gki-field">
                    <label for="filterProvinsi">Provinsi</label>
                    <select name="provinsi" id="filterProvinsi" class="gki-select" onchange="loadKabupaten()">
                        <option value="">Semua Provinsi</option>
                        <?php foreach ($provinsiList as $p): ?>
                        <option value="<?= htmlspecialchars($p); ?>" <?= $selectedProvinsi == $p ? 'selected' : ''; ?>><?= htmlspecialchars($p); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="gki-field">
                    <label for="filterKabupaten">Kabupaten/Kota</label>
                    <select name="kabupaten" id="filterKabupaten" class="gki-select" onchange="loadKecamatan()">
                        <option value="">Semua Kabupaten/Kota</option>
                    </select>
                </div>
                <div class="gki-field">
                    <label for="filterKecamatan">Kecamatan</label>
                    <select name="kecamatan" id="filterKecamatan" class="gki-select" onchange="loadKelurahan()">
                        <option value="">Semua Kecamatan</option>
                    </select>
                </div>
                <div class="gki-field">
                    <label for="filterKelurahan">Kelurahan</label>
                    <select name="kelurahan" id="filterKelurahan" class="gki-select" onchange="if(window.innerWidth>=768) applyFilter();">
                        <option value="">Semua Kelurahan</option>
                    </select>
                </div>
                <div class="gki-field">
                    <label for="filterJamDari">Jam Misa &ge;</label>
                    <input type="time" name="jam_dari" id="filterJamDari" class="gki-input" value="<?= htmlspecialchars($selectedJamDari); ?>">
                </div>
                <div class="gki-field">
                    <label for="filterNamaGereja">Nama Gereja</label>
                    <input type="text" name="nama_gereja" id="filterNamaGereja" class="gki-input" placeholder="Cth: Katedral..." value="<?= htmlspecialchars($selectedNamaGereja); ?>">
                </div>
                <div class="maps-filter-actions">
                    <button type="button" class="gki-btn gki-btn-primary gki-btn-sm flex-fill" onclick="applyFilter(); if(window.innerWidth<768) toggleFilter();">
                        <i class="bx bx-filter-alt" aria-hidden="true"></i>
                        <span>Terapkan</span>
                    </button>
                    <a href="<?= BASEURL; ?>maps" class="gki-btn gki-btn-outline gki-btn-sm" title="Reset filter">Reset</a>
                </div>
            </form>
        </div>
    </aside>

    <div class="maps-stage">
        <div id="map"></div>
    </div>
</div>

<div class="sheet-overlay" id="sheetOverlay" onclick="closeSheet()"></div>
<div class="sheet-panel" id="sheetPanel" role="dialog" aria-modal="true" aria-labelledby="sheetTitle">
    <div class="sheet-handle"></div>
    <div class="sheet-header">
        <h3 id="sheetTitle">-</h3>
        <button type="button" class="btn-close" onclick="closeSheet()" aria-label="Tutup detail"></button>
    </div>
    <div class="sheet-body" id="sheetBody"></div>
    <div class="sheet-footer" id="sheetFooter"></div>
</div>

<script>var gerejaData = <?php echo $gerejaJsonEncoded; unset($gerejaJsonEncoded); ?>;</script>

<?php
$mapScripts = '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>';
$mapScripts .= '<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>';
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

var markerClusterGroup = L.markerClusterGroup({
    iconCreateFunction: function(cluster) {
        var count = cluster.getChildCount();
        var bg = "#14533C";
        if (count >= 10) bg = "#1D6B4F";
        if (count >= 100) bg = "#B8862D";
        return L.divIcon({
            html: "<div style=\"background:" + bg + ";color:#fff;width:44px;height:44px;border-radius:50%;display:flex;flex-direction:column;align-items:center;justify-content:center;border:3px solid #fff;box-shadow:0 2px 10px rgba(17,29,24,0.28);\"><i class=\"bx bx-church\" style=\"font-size:1.05rem;line-height:1;\"></i><span style=\"font-size:0.65rem;font-weight:700;line-height:1;margin-top:2px;\">" + count + "</span></div>",
            className: "",
            iconSize: [44, 44],
            iconAnchor: [22, 22]
        });
    }
});

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
            body += "<button class=\"sheet-foto-nav prev\" onclick=\"fotoPrev()\" aria-label=\"Foto sebelumnya\"><i class=\"bx bx-chevron-left\"></i></button>";
            body += "<button class=\"sheet-foto-nav next\" onclick=\"fotoNext()\" aria-label=\"Foto berikutnya\"><i class=\"bx bx-chevron-right\"></i></button>";
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
    if (d.kecamatan) { body += "<div class=\"info-row\"><i class=\"bx bx-compass\"></i><div>Kec. " + escHtml(d.kecamatan) + (d.kelurahan ? ", Kel. " + escHtml(d.kelurahan) : "") + "</div></div>"; }
    if (d.telepon) { body += "<div class=\"info-row\"><i class=\"bx bx-phone\"></i><div>" + escHtml(d.telepon) + "</div></div>"; }

    if (d.social && d.social.length > 0) {
        body += "<div class=\"section-label\">Website &amp; Media Sosial</div>";
        body += "<div class=\"socmed-sheet\">";
        for (var si = 0; si < d.social.length; si++) {
            var s = d.social[si];
            var sIcon = "bx bx-link";
            if (s.platform == "website") sIcon = "bx bx-globe";
            else if (s.platform == "instagram") sIcon = "bx bxl-instagram";
            else if (s.platform == "facebook") sIcon = "bx bxl-facebook";
            else if (s.platform == "twitter") sIcon = "bx bxl-twitter";
            else if (s.platform == "youtube") sIcon = "bx bxl-youtube";
            else if (s.platform == "tiktok") sIcon = "bx bxl-tiktok";
            else if (s.platform == "linkedin") sIcon = "bx bxl-linkedin";
            else if (s.platform == "whatsapp") sIcon = "bx bxl-whatsapp";
            else if (s.platform == "telegram") sIcon = "bx bxl-telegram";
            body += "<a href=\"" + escAttr(s.url) + "\" target=\"_blank\" rel=\"noopener noreferrer\" class=\"socmed-sheet-btn\"><i class=\"" + sIcon + "\"></i>" + escHtml(s.platform.charAt(0).toUpperCase() + s.platform.slice(1)) + "</a>";
        }
        body += "</div>";
    }

    if (d.deskripsi) {
        body += "<div class=\"section-label\">Tentang Gereja</div>";
        body += "<div class=\"sheet-deskripsi\">" + escHtml(d.deskripsi) + "</div>";
    }

    if (d.jadwal && d.jadwal.length > 0) {
        body += "<div class=\"section-label\">Jadwal Misa</div>";
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
        "<a href=\"" + escAttr(mapsUrl) + "\" target=\"_blank\" rel=\"noopener noreferrer\" class=\"gki-btn gki-btn-outline\"><i class=\"bx bx-map\"></i> Maps</a>" +
        "<a href=\"https://www.google.com/maps/dir/?api=1&destination=" + d.lat + "," + d.lng + "\" target=\"_blank\" rel=\"noopener noreferrer\" class=\"gki-btn gki-btn-outline\"><i class=\"bx bx-navigation\"></i> Rute</a>" +
        "<a href=\"" + BASEURL + "gereja/" + encodeURIComponent(d.slug) + "?from=maps\" class=\"gki-btn gki-btn-primary\"><i class=\"bx bx-detail\"></i> Detail</a>";

    fotoIdx = 0;
    document.getElementById("sheetPanel").classList.add("open");
    document.getElementById("sheetOverlay").classList.add("active");
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
}

function escHtml(s) { if (!s) return ""; return s.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\"/g, "&quot;"); }
function escAttr(s) { if (!s) return ""; return s.replace(/\"/g, "&quot;").replace(/</g, "&lt;").replace(/>/g, "&gt;"); }

map.addLayer(markerClusterGroup);

function createGerejaMarker(data, idx) {
    var icon = L.divIcon({
        html: "<div style=\"background:#14533C;color:#fff;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid #fff;box-shadow:0 2px 8px rgba(17,29,24,0.3);font-size:1rem;\"><i class=\"bx bx-church\"></i></div>",
        className: "",
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        popupAnchor: [0, -18]
    });
    var marker = L.marker([data.lat, data.lng], {icon: icon});
    marker.bindPopup(
        "<strong>" + escHtml(data.nama) + "</strong><br>" +
        "<span class=\"popup-addr\">" + escHtml(data.alamat) + "</span><br>" +
        "<button class=\"popup-link\" onclick=\"showGerejaDetail(" + idx + ")\"><i class=\"bx bx-detail\"></i> Lihat Detail</button>"
    );
    return marker;
}

function applyFilter() {
    var p = document.getElementById("filterProvinsi").value;
    var k = document.getElementById("filterKabupaten").value;
    var c = document.getElementById("filterKecamatan").value;
    var l = document.getElementById("filterKelurahan").value;
    var n = document.getElementById("filterNamaGereja") ? document.getElementById("filterNamaGereja").value : "";
    var j = document.getElementById("filterJamDari").value;

    markerClusterGroup.clearLayers();
    var bnds = [];
    var cnt = 0;
    var nLower = n.toLowerCase();

    for (var i = 0; i < gerejaData.length; i++) {
        var d = gerejaData[i];
        if (!d.lat || !d.lng) continue;
        if (p && d.provinsi !== p) continue;
        if (k && d.kabkota !== k) continue;
        if (c && d.kecamatan !== c) continue;
        if (l && d.kelurahan !== l) continue;
        if (n) {
            var words = nLower.split(/\s+/);
            var haystack = (d.nama + " " + d.alamat + " " + d.provinsi + " " + d.kabkota + " " + d.kecamatan + " " + d.kelurahan).toLowerCase();
            var matchAll = true;
            for (var w = 0; w < words.length; w++) {
                if (words[w] && haystack.indexOf(words[w]) === -1) { matchAll = false; break; }
            }
            if (!matchAll) continue;
        }
        if (j) {
            var match = false;
            if (d.jadwal) {
                for (var q = 0; q < d.jadwal.length; q++) {
                    if (d.jadwal[q].waktu >= j) { match = true; break; }
                }
            }
            if (!match) continue;
        }
        markerClusterGroup.addLayer(createGerejaMarker(d, i));
        bnds.push([d.lat, d.lng]);
        cnt++;
    }

    var pill = document.getElementById("mapCountPill");
    if (pill) pill.textContent = cnt + " gereja";

    if (bnds.length > 0) {
        map.flyToBounds(bnds, {padding: [30, 30], maxZoom: 14, duration: 0.7});
    }

    var params = new URLSearchParams();
    if (p) params.set("provinsi", p);
    if (k) params.set("kabupaten", k);
    if (c) params.set("kecamatan", c);
    if (l) params.set("kelurahan", l);
    if (n) params.set("nama_gereja", n);
    if (j) params.set("jam_dari", j);
    var qs = params.toString();
    var newUrl = BASEURL + "maps" + (qs ? "?" + qs : "");
    window.history.replaceState(null, "", newUrl);
}

function toggleFilter() {
    var drawer = document.getElementById("filterDrawer");
    var overlay = document.getElementById("filterOverlay");
    drawer.classList.toggle("open");
    overlay.classList.toggle("active");
}

document.addEventListener("keydown", function(e) {
    if (e.key === "Escape") {
        closeSheet();
        var drawer = document.getElementById("filterDrawer");
        if (drawer && drawer.classList.contains("open")) toggleFilter();
    }
});

var filterProvinsi = "' . htmlspecialchars($selectedProvinsi, ENT_QUOTES) . '";
var filterKabupaten = "' . htmlspecialchars($selectedKabupaten, ENT_QUOTES) . '";
var filterKecamatan = "' . htmlspecialchars($selectedKecamatan, ENT_QUOTES) . '";
var filterKelurahan = "' . htmlspecialchars($selectedKelurahan, ENT_QUOTES) . '";
var filterNamaGereja = "' . htmlspecialchars($selectedNamaGereja, ENT_QUOTES) . '";
var filterJamDari = "' . htmlspecialchars($selectedJamDari, ENT_QUOTES) . '";
var isInit = true;

function loadKabupaten() {
    var p = document.getElementById("filterProvinsi").value;
    var sel = document.getElementById("filterKabupaten");
    sel.innerHTML = "<option value=\"\">Semua Kabupaten/Kota</option>";
    document.getElementById("filterKecamatan").innerHTML = "<option value=\"\">Semua Kecamatan</option>";
    document.getElementById("filterKelurahan").innerHTML = "<option value=\"\">Semua Kelurahan</option>";
    if (!p) { isInit = false; applyFilter(); return; }
    var xhr = new XMLHttpRequest();
    xhr.open("GET", BASEURL + "maps/filter/kabupaten?provinsi=" + encodeURIComponent(p), true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var res = JSON.parse(xhr.responseText);
                if (res.status === 200 && res.data) {
                    for (var i = 0; i < res.data.length; i++) {
                        var opt = document.createElement("option");
                        opt.value = res.data[i].name;
                        opt.textContent = res.data[i].name;
                        if (isInit && res.data[i].name === filterKabupaten) opt.selected = true;
                        sel.appendChild(opt);
                    }
                    if (isInit && filterKabupaten) { loadKecamatan(); return; }
                }
            } catch(e) {}
        }
        isInit = false;
        applyFilter();
    };
    xhr.send();
}

function loadKecamatan() {
    var p = document.getElementById("filterProvinsi").value;
    var k = document.getElementById("filterKabupaten").value;
    var sel = document.getElementById("filterKecamatan");
    sel.innerHTML = "<option value=\"\">Semua Kecamatan</option>";
    document.getElementById("filterKelurahan").innerHTML = "<option value=\"\">Semua Kelurahan</option>";
    if (!p || !k) { isInit = false; applyFilter(); return; }
    var xhr = new XMLHttpRequest();
    xhr.open("GET", BASEURL + "maps/filter/kecamatan?provinsi=" + encodeURIComponent(p) + "&kabupaten=" + encodeURIComponent(k), true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var res = JSON.parse(xhr.responseText);
                if (res.status === 200 && res.data) {
                    for (var i = 0; i < res.data.length; i++) {
                        var opt = document.createElement("option");
                        opt.value = res.data[i].name;
                        opt.textContent = res.data[i].name;
                        if (isInit && res.data[i].name === filterKecamatan) opt.selected = true;
                        sel.appendChild(opt);
                    }
                    if (isInit && filterKecamatan) { loadKelurahan(); return; }
                }
            } catch(e) {}
        }
        isInit = false;
        applyFilter();
    };
    xhr.send();
}

function loadKelurahan() {
    var p = document.getElementById("filterProvinsi").value;
    var k = document.getElementById("filterKabupaten").value;
    var c = document.getElementById("filterKecamatan").value;
    var sel = document.getElementById("filterKelurahan");
    sel.innerHTML = "<option value=\"\">Semua Kelurahan</option>";
    if (!p || !k || !c) { isInit = false; applyFilter(); return; }
    var xhr = new XMLHttpRequest();
    xhr.open("GET", BASEURL + "maps/filter/kelurahan?provinsi=" + encodeURIComponent(p) + "&kabupaten=" + encodeURIComponent(k) + "&kecamatan=" + encodeURIComponent(c), true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var res = JSON.parse(xhr.responseText);
                if (res.status === 200 && res.data) {
                    for (var i = 0; i < res.data.length; i++) {
                        var opt = document.createElement("option");
                        opt.value = res.data[i].name;
                        opt.textContent = res.data[i].name;
                        if (isInit && res.data[i].name === filterKelurahan) opt.selected = true;
                        sel.appendChild(opt);
                    }
                }
            } catch(e) {}
        }
        isInit = false;
        applyFilter();
    };
    xhr.send();
}

function initFilter() {
    document.getElementById("filterProvinsi").value = filterProvinsi;
    if (filterNamaGereja) document.getElementById("filterNamaGereja").value = filterNamaGereja;
    if (filterJamDari) document.getElementById("filterJamDari").value = filterJamDari;
    if (filterProvinsi) loadKabupaten(); else { isInit = false; applyFilter(); }

    var namaInput = document.getElementById("filterNamaGereja");
    if (namaInput) {
        var timer;
        namaInput.addEventListener("input", function() {
            clearTimeout(timer);
            timer = setTimeout(function() {
                if (window.innerWidth >= 768) applyFilter();
            }, 300);
        });
    }
    var jamInput = document.getElementById("filterJamDari");
    if (jamInput) {
        jamInput.addEventListener("change", function() {
            if (window.innerWidth >= 768) applyFilter();
        });
    }
}

initFilter();
</script>';

$this->view('layouts/guest_bottom_nav', array('activeMenu' => 'maps'));
$this->view('layouts/guest_closeTag', array('scripts' => $mapScripts));
?>
