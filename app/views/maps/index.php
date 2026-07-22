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
        'lat' => (float)$g->latitude,
        'lng' => (float)$g->longitude,
        'foto' => isset($allFoto[$g->id]) ? $allFoto[$g->id] : '',
        'jadwal' => $jdw
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
    .sheet-body .sheet-foto { width: 100%; height: 160px; object-fit: cover; border-radius: 12px; margin-bottom: 0.5rem; background: #eee; }
    .sheet-body .sheet-foto-placeholder { width: 100%; height: 100px; background: linear-gradient(135deg, var(--primary-light), var(--primary-dark)); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.5rem; }
    .sheet-body .sheet-foto-placeholder i { font-size: 3rem; color: rgba(255,255,255,0.3); }
    .sheet-footer { display: flex; gap: 0.6rem; padding: 0.7rem 1.1rem; padding-bottom: calc(0.7rem + env(safe-area-inset-bottom, 0px)); border-top: 1px solid #f0f0f0; flex-shrink: 0; }
    .sheet-footer .btn-sheet { flex: 1; border-radius: 12px; padding: 0.5rem 1rem; font-weight: 600; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 0.3rem; border: none; cursor: pointer; }
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
        .sheet-body .sheet-foto { height: 130px; }
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

    if (d.foto) {
        body += "<img src=\"" + escAttr(d.foto) + "\" class=\"sheet-foto\" alt=\"" + escAttr(d.nama) + "\" onerror=\"this.style.display=\'none\'\">";
    } else {
        body += "<div class=\"sheet-foto-placeholder\"><i class=\"bx bx-church\"></i></div>";
    }

    body += "<div class=\"info-row\"><i class=\"bx bx-map-pin\"></i><div>" + escHtml(d.alamat) + "</div></div>";
    body += "<div class=\"info-row\"><i class=\"bx bx-map\"></i><div>" + escHtml(d.provinsi) + (d.kabkota ? ", " + escHtml(d.kabkota) : "") + "</div></div>";
    if (d.kecamatan) { body += "<div class=\"info-row\"><i class=\"bx bx-detail\"></i><div>Kec. " + escHtml(d.kecamatan) + "</div></div>"; }
    if (d.kelurahan) { body += "<div class=\"info-row\"><i class=\"bx bx-home\"></i><div>Kel. " + escHtml(d.kelurahan) + "</div></div>"; }
    if (d.telepon) { body += "<div class=\"info-row\"><i class=\"bx bx-phone\"></i><div>" + escHtml(d.telepon) + "</div></div>"; }

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
    document.getElementById("sheetFooter").innerHTML =
        "<a href=\"https://www.google.com/maps/dir/?api=1&destination=" + d.lat + "," + d.lng + "\" target=\"_blank\" class=\"btn-sheet btn-sheet-outline\"><i class=\"bx bx-navigation\"></i> Rute</a>" +
        "<a href=\"" + BASEURL + "gereja/" + encodeURIComponent(d.slug) + "?from=maps\" class=\"btn-sheet btn-sheet-primary\"><i class=\"bx bx-detail\"></i> Halaman Detail</a>";

    document.getElementById("sheetPanel").classList.add("open");
    document.getElementById("sheetOverlay").classList.add("active");
    document.body.style.overflow = "hidden";
}

function closeSheet() {
    document.getElementById("sheetPanel").classList.remove("open");
    document.getElementById("sheetOverlay").classList.remove("active");
    document.body.style.overflow = "";
}

function escHtml(s) { if (!s) return ""; return s.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\"/g, "&quot;"); }
function escAttr(s) { if (!s) return ""; return s.replace(/\"/g, "&quot;").replace(/</g, "&lt;").replace(/>/g, "&gt;"); }
';

foreach ($gerejaList as $i => $g) {
    $lat = $g->latitude;
    $lng = $g->longitude;

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
