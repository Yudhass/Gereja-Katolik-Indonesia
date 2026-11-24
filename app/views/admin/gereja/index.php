<?php
$provArr = array();
foreach ($provinces as $p) {
    $provArr[] = array('id' => $p->id, 'name' => $p->name);
}
?>
<?php $this->view('layouts/openTag', array(
    'title' => $title,
    'css' => array('assets/plugins/datatable/css/dataTables.bootstrap5.min.css'),
    'css2' => array('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css')
)); ?>
<style>
    .gereja-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--gki-border);
    }
    .gereja-title-row {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
    }
    .gereja-title {
        font-size: clamp(1.25rem, 1.05rem + 0.6vw, 1.55rem);
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--gki-text);
        margin: 0;
    }
    .gereja-count-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.22rem 0.65rem;
        border-radius: 999px;
        background: var(--gki-primary-light);
        color: var(--gki-primary);
        font-size: 0.78rem;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }
    .gereja-subtitle {
        font-size: 0.85rem;
        color: var(--gki-muted);
        margin: 0.2rem 0 0;
    }

    .filter-panel {
        background: var(--gki-surface);
        border: 1px solid var(--gki-border);
        border-radius: 14px;
        padding: 1rem 1.15rem;
        margin-bottom: 1.15rem;
    }
    .filter-panel .form-label {
        font-size: 0.76rem;
        font-weight: 700;
        color: var(--gki-muted);
        margin-bottom: 0.3rem;
    }

    .table-panel {
        background: var(--gki-surface);
        border: 1px solid var(--gki-border);
        border-radius: 14px;
        padding: 1rem 1.15rem;
    }
    #table-gereja thead th {
        background: var(--gki-bg) !important;
        color: var(--gki-muted) !important;
        border-bottom: 1px solid var(--gki-border) !important;
        font-size: 0.73rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.7rem 0.85rem;
    }
    #table-gereja tbody td {
        padding: 0.8rem 0.85rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--gki-bg);
        font-size: 0.85rem;
    }
    .cell-church-name {
        font-weight: 700;
        color: var(--gki-text);
        line-height: 1.35;
    }
    .cell-church-addr {
        font-size: 0.78rem;
        color: var(--gki-muted);
        margin-top: 0.15rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        max-width: 340px;
    }
    .cell-region-main {
        font-weight: 600;
        color: var(--gki-text);
        font-size: 0.83rem;
    }
    .cell-region-sub {
        font-size: 0.76rem;
        color: var(--gki-muted);
        margin-top: 0.12rem;
    }
    .cell-region-empty {
        display: inline-block;
        padding: 0.12rem 0.45rem;
        border-radius: 999px;
        background: #FEF3C7;
        color: #92400E;
        font-size: 0.72rem;
        font-weight: 600;
    }
    .cell-contact-phone {
        font-size: 0.82rem;
        color: var(--gki-text);
        font-variant-numeric: tabular-nums;
    }
    .cell-links {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        flex-wrap: wrap;
        margin-top: 0.25rem;
    }
    .cell-icon-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 6px;
        background: var(--gki-bg);
        color: var(--gki-primary);
        text-decoration: none;
        font-size: 0.95rem;
        transition: background-color 0.14s ease, color 0.14s ease;
    }
    .cell-icon-link:hover {
        background: var(--gki-primary-light);
        color: var(--gki-primary-hover);
    }
    .cell-thumbs {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .cell-thumbs img {
        width: 40px;
        height: 30px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid var(--gki-border);
    }
    .cell-thumb-more {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 26px;
        height: 24px;
        padding: 0 5px;
        border-radius: 6px;
        background: var(--gki-primary-light);
        color: var(--gki-primary);
        font-size: 0.72rem;
        font-weight: 700;
    }
    .dataTables_wrapper .dataTables_processing {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid var(--gki-border);
        border-radius: 10px;
        color: var(--gki-primary);
        font-weight: 600;
        font-size: 0.85rem;
        box-shadow: var(--gki-shadow);
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.active .page-link,
    .dataTables_wrapper .page-item.active .page-link {
        background-color: var(--gki-primary);
        border-color: var(--gki-primary);
        color: #fff;
    }
    .dataTables_wrapper .page-link {
        color: var(--gki-text);
        border-color: var(--gki-border);
        font-size: 0.82rem;
    }
</style>
<body>
<div class="wrapper">
    <?php $this->view('layouts/admin_sidebar', array('sidebar' => 'gereja')); ?>
    <?php $this->view('layouts/navbar'); ?>
    <div class="page-wrapper">
        <div class="page-content">
            <div class="gereja-header">
                <div>
                    <div class="gereja-title-row">
                        <h1 class="gereja-title">Data Gereja</h1>
                        <span class="gereja-count-badge" id="totalGerejaBadge"><?= number_format((int)$totalGereja, 0, ',', '.'); ?> gereja</span>
                    </div>
                    <p class="gereja-subtitle">Kelola direktori gereja, wilayah, foto, dan tautan lokasi secara server-side.</p>
                </div>
                <button type="button" class="btn btn-custom-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
                    <i class="bx bx-plus me-1" aria-hidden="true"></i>Tambah Gereja
                </button>
            </div>

            <section class="filter-panel" aria-label="Filter data gereja">
                <form id="filterGerejaForm" method="GET" action="<?= BASEURL; ?>admin/gereja" class="row g-2 align-items-end">
                    <div class="col-12 col-md-5">
                        <label class="form-label" for="filterSearch">Cari Gereja</label>
                        <input type="text" id="filterSearch" name="q" class="form-control form-control-sm match-select2" placeholder="Nama gereja, alamat, kota, atau telepon..." value="<?= htmlspecialchars($filterSearch); ?>">
                    </div>
                    <div class="col-12 col-sm-7 col-md-4">
                        <label class="form-label" for="filterProvinsi">Provinsi</label>
                        <select name="provinsi" class="form-select form-select-sm" id="filterProvinsi">
                            <option value="">Semua Provinsi</option>
                            <?php foreach ($provinces as $p): ?>
                            <option value="<?= htmlspecialchars($p->name); ?>" <?= $filterProvinsi == $p->name ? 'selected' : ''; ?>><?= htmlspecialchars($p->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-sm-5 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-custom-primary btn-sm flex-fill">
                            <i class="bx bx-filter-alt me-1" aria-hidden="true"></i>Terapkan
                        </button>
                        <button type="button" id="btnResetFilter" class="btn btn-custom-outline btn-sm flex-fill">
                            <i class="bx bx-reset me-1" aria-hidden="true"></i>Reset
                        </button>
                    </div>
                </form>
            </section>

            <section class="table-panel" aria-label="Tabel data gereja">
                <div class="table-responsive">
                    <table id="table-gereja" class="table table-hover mb-0" style="width:100%;">
                        <thead>
                            <tr>
                                <th style="width:44px;">#</th>
                                <th>Gereja &amp; Alamat</th>
                                <th>Wilayah</th>
                                <th>Kontak &amp; Tautan</th>
                                <th style="width:120px;">Foto</th>
                                <th style="width:100px;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?= BASEURL; ?>admin/gereja/add">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-plus-circle me-1"></i>Tambah Gereja</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12"><label class="form-label">Nama Gereja <span class="text-danger">*</span></label><input type="text" name="nama_gereja" class="form-control" required></div>
                        <div class="col-12"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="2"></textarea></div>
                        <div class="col-12 col-sm-6"><label class="form-label">Provinsi</label>
                            <select name="provinsi" class="form-select add-select" id="addProvinsi">
                                <option value="">-- Pilih Provinsi --</option>
                                <?php foreach ($provinces as $p): ?>
                                <option value="<?= htmlspecialchars($p->name); ?>" data-id="<?= htmlspecialchars($p->id); ?>"><?= htmlspecialchars($p->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6"><label class="form-label">Kabupaten/Kota</label>
                            <select name="kabupaten_kota" class="form-select add-select" id="addKabupaten" disabled>
                                <option value="">-- Pilih Provinsi dulu --</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6"><label class="form-label">Kecamatan</label>
                            <select name="kecamatan" class="form-select add-select" id="addKecamatan" disabled>
                                <option value="">-- Pilih Kabupaten dulu --</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6"><label class="form-label">Kelurahan</label>
                            <select name="kelurahan" class="form-select add-select" id="addKelurahan" disabled>
                                <option value="">-- Pilih Kecamatan dulu --</option>
                            </select>
                        </div>
                        <div class="col-12"><label class="form-label">Link Google Maps</label><input type="text" name="link_maps" class="form-control" id="addLinkMaps" placeholder="https://maps.google.com/?q=-6.168154,106.833206" oninput="extractLatLng('add')"></div>
                        <div class="col-12 col-sm-6"><label class="form-label">Latitude</label><input type="text" name="latitude" class="form-control" id="addLatitude" placeholder="-6.16815400"></div>
                        <div class="col-12 col-sm-6"><label class="form-label">Longitude</label><input type="text" name="longitude" class="form-control" id="addLongitude" placeholder="106.83320600"></div>
                        <div class="col-12 col-sm-6"><label class="form-label">Kontak Telepon</label><input type="text" name="kontak_telepon" class="form-control"></div>
                        <div class="col-12"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="3"></textarea></div>
                        <div class="col-12">
                            <label class="form-label">Foto Gereja (URL)</label>
                            <div id="addFotoList">
                                <div class="foto-input-group">
                                    <input type="url" name="foto_urls[]" class="form-control" placeholder="https://...">
                                    <input type="text" name="foto_keterangan[]" class="form-control" placeholder="Keterangan (opsional)">
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="tambahFoto('addFotoList')"><i class="bx bx-plus me-1"></i>Tambah Foto</button>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Sosial Media / Website</label>
                            <div id="addSosmedList">
                                <div class="sosmed-input-group d-flex gap-2 mb-1">
                                    <select name="sosmed_platform[]" class="form-select" style="width:140px;flex-shrink:0;">
                                        <option value="website">Website</option>
                                        <option value="instagram">Instagram</option>
                                        <option value="facebook">Facebook</option>
                                        <option value="twitter">Twitter</option>
                                        <option value="youtube">YouTube</option>
                                        <option value="tiktok">TikTok</option>
                                        <option value="linkedin">LinkedIn</option>
                                        <option value="whatsapp">WhatsApp</option>
                                        <option value="telegram">Telegram</option>
                                    </select>
                                    <input type="url" name="sosmed_url[]" class="form-control" placeholder="https://...">
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="tambahSosmed('addSosmedList')"><i class="bx bx-plus me-1"></i>Tambah Sosial Media</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bx bx-x me-1"></i>Batal</button>
                    <button type="submit" class="btn btn-custom-primary"><i class="bx bx-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var provinsiData = <?php echo json_encode($provArr); ?>;
var dtGereja = null;

function escHtml(s) {
    if (s === null || s === undefined) return '';
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function getSelectedDataId(selectId) {
    var sel = document.getElementById(selectId);
    if (!sel || sel.selectedIndex < 0) return null;
    var opt = sel.options[sel.selectedIndex];
    return opt ? opt.getAttribute('data-id') : null;
}

function resetSelect2(selectId, placeholder, disabled) {
    var sel = $('#' + selectId);
    if (sel.hasClass('select2-hidden-accessible')) {
        sel.select2('destroy');
    }
    sel.empty().append('<option value="">' + placeholder + '</option>');
    sel.prop('disabled', !!disabled);
}

function loadSelect2(url, selectId, selectedName, placeholder, modalId, disabled) {
    var sel = $('#' + selectId);
    if (sel.hasClass('select2-hidden-accessible')) {
        sel.select2('destroy');
    }
    sel.prop('disabled', true);
    sel.empty().append('<option value="">' + placeholder + '</option>');
    if (!url) return;
    $.get(url, function(res) {
        if (res.status == 200 && res.data) {
            $.each(res.data, function(i, item) {
                var s = (item.name == selectedName) ? ' selected' : '';
                sel.append('<option value="' + escHtml(item.name) + '" data-id="' + item.id + '"' + s + '>' + escHtml(item.name) + '</option>');
            });
        }
        if (!disabled) sel.prop('disabled', false);
        sel.select2({ dropdownParent: $('#' + modalId), width: '100%' });
    });
}

function initSel2(selectId, modalId) {
    var sel = $('#' + selectId);
    if (sel.hasClass('select2-hidden-accessible')) {
        sel.select2('destroy');
    }
    sel.select2({ dropdownParent: $('#' + modalId), width: '100%' });
}

function extractLatLng(prefix) {
    var url = document.getElementById(prefix + 'LinkMaps').value.trim();
    var latInput = document.getElementById(prefix + 'Latitude');
    var lngInput = document.getElementById(prefix + 'Longitude');
    var lat = null, lng = null;
    if (!url) return;

    var lat3d = url.match(/!3d(-?\d+\.?\d*)/g);
    var lng4d = url.match(/!4d(-?\d+\.?\d*)/g);
    if (lat3d && lng4d && lat3d.length > 0 && lng4d.length > 0) {
        var li = Math.min(lat3d.length, lng4d.length) - 1;
        lat = lat3d[li].replace('!3d', '');
        lng = lng4d[li].replace('!4d', '');
    }
    if (!lat) { var m = url.match(/@(-?\d+\.?\d*),(-?\d+\.?\d*)/); if (m) { lat = m[1]; lng = m[2]; } }
    if (!lat) { var m = url.match(/[?&]q=(-?\d+\.?\d*),(-?\d+\.?\d*)/); if (m) { lat = m[1]; lng = m[2]; } }
    if (!lat) { var m = url.match(/[?&]ll=(-?\d+\.?\d*),(-?\d+\.?\d*)/); if (m) { lat = m[1]; lng = m[2]; } }

    if (lat && lng) { latInput.value = lat; lngInput.value = lng; }
}

function tambahFoto(containerId) {
    var html = '<div class="foto-input-group" style="margin-top:0.5rem;">';
    html += '<input type="url" name="foto_urls[]" class="form-control" placeholder="https://...">';
    html += '<input type="text" name="foto_keterangan[]" class="form-control" placeholder="Keterangan (opsional)">';
    html += '<button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.remove()"><i class="bx bx-x"></i></button>';
    html += '</div>';
    document.getElementById(containerId).insertAdjacentHTML('beforeend', html);
}

var sosmedPlatforms = ['website','instagram','facebook','twitter','youtube','tiktok','linkedin','whatsapp','telegram'];
function tambahSosmed(containerId) {
    var opts = sosmedPlatforms.map(function(p) { return '<option value="' + p + '">' + p.charAt(0).toUpperCase() + p.slice(1) + '</option>'; }).join('');
    var html = '<div class="sosmed-input-group d-flex gap-2 mb-1">';
    html += '<select name="sosmed_platform[]" class="form-select" style="width:140px;flex-shrink:0;">' + opts + '</select>';
    html += '<input type="url" name="sosmed_url[]" class="form-control" placeholder="https://...">';
    html += '<button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0" onclick="this.parentElement.remove()"><i class="bx bx-x"></i></button>';
    html += '</div>';
    document.getElementById(containerId).insertAdjacentHTML('beforeend', html);
}

function hapusGereja(id) {
    Swal.fire({
        title: 'Hapus Gereja',
        text: 'Yakin ingin menghapus gereja ini? Semua jadwal, foto, dan saran terkait juga akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#B91C1C',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then(function(result) {
        if (!result.isConfirmed) return;
        $.post('<?= BASEURL; ?>admin/gereja/delete/' + id, {csrf_token: '<?= csrf_token(); ?>'}, function(res) {
            if (res.status == 200) {
                Swal.fire({title:'Terhapus!', text:'Gereja berhasil dihapus.', icon:'success', timer:1300, showConfirmButton:false});
                if (dtGereja) dtGereja.ajax.reload(null, false);
                else location.reload();
            } else {
                Swal.fire({title:'Gagal!', text:res.message, icon:'error'});
            }
        });
    });
}

function getSocialIcon(platform) {
    var icons = {
        website: 'bx bx-globe',
        instagram: 'bx bxl-instagram',
        facebook: 'bx bxl-facebook',
        twitter: 'bx bxl-twitter',
        youtube: 'bx bxl-youtube',
        tiktok: 'bx bxl-tiktok',
        linkedin: 'bx bxl-linkedin',
        whatsapp: 'bx bxl-whatsapp',
        telegram: 'bx bxl-telegram'
    };
    return icons[platform] || 'bx bx-link';
}

function syncUrlFilter() {
    var q = ($('#filterSearch').val() || '').trim();
    var prov = ($('#filterProvinsi').val() || '').trim();
    var params = new URLSearchParams();
    if (q) params.set('q', q);
    if (prov) params.set('provinsi', prov);
    var qs = params.toString();
    window.history.replaceState(null, '', '<?= BASEURL; ?>admin/gereja' + (qs ? '?' + qs : ''));
}

(function() {
    var waitJq = setInterval(function() {
        if (typeof $ !== 'undefined' && $.fn) {
            clearInterval(waitJq);
            var s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
            s.onload = function() {
                $(function() {
                    $('#filterProvinsi').select2({ width: '100%', containerCssClass: 'filter-sm', dropdownCssClass: 'filter-sm-dropdown' });
                    initSel2('addProvinsi', 'modalAdd');

                    $('#addProvinsi').on('change', function() {
                        var pid = getSelectedDataId('addProvinsi');
                        var m = 'modalAdd';
                        resetSelect2('addKabupaten', '-- Pilih Provinsi dulu --', true);
                        resetSelect2('addKecamatan', '-- Pilih Kabupaten dulu --', true);
                        resetSelect2('addKelurahan', '-- Pilih Kecamatan dulu --', true);
                        if (pid) { loadSelect2('<?= BASEURL; ?>admin/gereja/getRegencies/' + pid, 'addKabupaten', '', '-- Pilih Kabupaten --', m); }
                    });

                    $('#addKabupaten').on('change', function() {
                        var kid = getSelectedDataId('addKabupaten');
                        var m = 'modalAdd';
                        resetSelect2('addKecamatan', '-- Pilih Kabupaten dulu --', true);
                        resetSelect2('addKelurahan', '-- Pilih Kecamatan dulu --', true);
                        if (kid) { loadSelect2('<?= BASEURL; ?>admin/gereja/getDistricts/' + kid, 'addKecamatan', '', '-- Pilih Kecamatan --', m); }
                    });

                    $('#addKecamatan').on('change', function() {
                        var kid = getSelectedDataId('addKecamatan');
                        var m = 'modalAdd';
                        resetSelect2('addKelurahan', '-- Pilih Kecamatan dulu --', true);
                        if (kid) { loadSelect2('<?= BASEURL; ?>admin/gereja/getVillages/' + kid, 'addKelurahan', '', '-- Pilih Kelurahan --', m); }
                    });

                    $('#modalAdd').on('shown.bs.modal', function() {
                        initSel2('addProvinsi', 'modalAdd');
                    });

                    $('#modalAdd').on('hidden.bs.modal', function() {
                        $('.add-select').each(function() {
                            if ($(this).hasClass('select2-hidden-accessible')) { $(this).select2('destroy'); }
                        });
                    });
                });
            };
            document.head.appendChild(s);
        }
    }, 50);
})();
</script>
<script defer src="<?= BASEURL; ?>assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script defer src="<?= BASEURL; ?>assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
<script>
(function() {
    var waitDt = setInterval(function() {
        if (typeof $ !== 'undefined' && $.fn && $.fn.dataTable) {
            clearInterval(waitDt);

            dtGereja = $('#table-gereja').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[1, 'asc']],
                autoWidth: false,
                ajax: {
                    url: '<?= BASEURL; ?>admin/gereja/datatable',
                    type: 'GET',
                    data: function(d) {
                        d.q = ($('#filterSearch').val() || '').trim();
                        d.provinsi = ($('#filterProvinsi').val() || '').trim();
                    },
                    dataSrc: function(json) {
                        if (json && typeof json.recordsTotal === 'number') {
                            $('#totalGerejaBadge').text(json.recordsTotal.toLocaleString('id-ID') + ' gereja');
                        }
                        return json && json.data ? json.data : [];
                    }
                },
                language: {
                    processing: 'Memuat data gereja...',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    zeroRecords: 'Tidak ada gereja yang cocok dengan filter.',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ gereja',
                    infoEmpty: 'Menampilkan 0 data',
                    infoFiltered: '(disaring dari _MAX_ total gereja)',
                    paginate: {
                        first: 'Awal',
                        last: 'Akhir',
                        next: 'Berikutnya',
                        previous: 'Sebelumnya'
                    }
                },
                columns: [
                    {
                        data: 'no',
                        orderable: false,
                        className: 'text-center text-muted'
                    },
                    {
                        data: 'nama_gereja',
                        render: function(data, type, row) {
                            var addr = row.alamat ? '<div class="cell-church-addr" title="' + escHtml(row.alamat) + '">' + escHtml(row.alamat) + '</div>' : '';
                            return '<div class="cell-church-name">' + escHtml(row.nama_gereja) + '</div>' + addr;
                        }
                    },
                    {
                        data: 'provinsi',
                        render: function(data, type, row) {
                            var mainParts = [row.kabupaten_kota, row.provinsi].filter(Boolean);
                            var subParts = [];
                            if (row.kecamatan) subParts.push('Kec. ' + row.kecamatan);
                            if (row.kelurahan) subParts.push('Kel. ' + row.kelurahan);
                            if (!mainParts.length && !subParts.length) {
                                return '<span class="cell-region-empty">Wilayah kosong</span>';
                            }
                            var html = '<div class="cell-region-main">' + escHtml(mainParts.join(', ') || '-') + '</div>';
                            if (subParts.length) {
                                html += '<div class="cell-region-sub">' + escHtml(subParts.join(' · ')) + '</div>';
                            }
                            return html;
                        }
                    },
                    {
                        data: 'kontak_telepon',
                        render: function(data, type, row) {
                            var html = '<div class="cell-contact-phone">' + escHtml(row.kontak_telepon || '-') + '</div>';
                            var links = '';
                            if (row.link_maps) {
                                links += '<a href="' + escHtml(row.link_maps) + '" target="_blank" rel="noopener noreferrer" class="cell-icon-link" title="Buka Google Maps"><i class="bx bx-map-pin"></i></a>';
                            }
                            if (row.social_media_list && row.social_media_list.length) {
                                row.social_media_list.forEach(function(sm) {
                                    var label = sm.platform ? (sm.platform.charAt(0).toUpperCase() + sm.platform.slice(1)) : 'Tautan';
                                    links += '<a href="' + escHtml(sm.url) + '" target="_blank" rel="noopener noreferrer" class="cell-icon-link" title="' + escHtml(label) + '"><i class="' + getSocialIcon(sm.platform) + '"></i></a>';
                                });
                            }
                            if (links) html += '<div class="cell-links">' + links + '</div>';
                            return html;
                        }
                    },
                    {
                        data: 'foto_list',
                        orderable: false,
                        render: function(data) {
                            if (!data || !data.length) return '<span class="text-muted small">-</span>';
                            var html = '<div class="cell-thumbs">';
                            data.slice(0, 2).forEach(function(f) {
                                html += '<img src="' + escHtml(f.foto_url) + '" alt="" title="' + escHtml(f.keterangan || '') + '" loading="lazy">';
                            });
                            if (data.length > 2) {
                                html += '<span class="cell-thumb-more">+' + (data.length - 2) + '</span>';
                            }
                            html += '</div>';
                            return html;
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        className: 'text-end text-nowrap',
                        render: function(id) {
                            var q = encodeURIComponent(($('#filterSearch').val() || '').trim());
                            var prov = encodeURIComponent(($('#filterProvinsi').val() || '').trim());
                            var editUrl = '<?= BASEURL; ?>admin/gereja/edit/' + id + '?q=' + q + '&provinsi=' + prov;
                            return '<a href="' + editUrl + '" class="btn btn-sm btn-custom-outline me-1" title="Edit gereja"><i class="bx bx-edit"></i></a>' +
                                   '<button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusGereja(' + id + ')" title="Hapus gereja"><i class="bx bx-trash"></i></button>';
                        }
                    }
                ]
            });

            $('#filterGerejaForm').on('submit', function(e) {
                e.preventDefault();
                syncUrlFilter();
                dtGereja.ajax.reload();
            });

            var searchTimer = null;
            $('#filterSearch').on('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    syncUrlFilter();
                    dtGereja.ajax.reload();
                }, 350);
            });

            $('#filterProvinsi').on('change', function() {
                syncUrlFilter();
                dtGereja.ajax.reload();
            });

            $('#btnResetFilter').on('click', function() {
                $('#filterSearch').val('');
                $('#filterProvinsi').val('').trigger('change');
                syncUrlFilter();
                dtGereja.ajax.reload();
            });
        }
    }, 50);
})();
</script>
<?php $this->view('layouts/closeTag'); ?>
