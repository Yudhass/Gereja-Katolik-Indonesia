<?php
$provArr = array();
foreach ($provinces as $p) {
    $provArr[] = array('id' => $p->id, 'name' => $p->name);
}
?>
<?php $this->view('layouts/openTag', array('title' => $title, 'css' => array('assets/plugins/datatable/css/dataTables.bootstrap5.min.css'), 'css2' => array('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'))); ?>
<style>
    .btn-custom-primary { background:#fff; border:2px solid #2C4463; color:#2C4463 !important; font-weight:600; }
    .btn-custom-primary:hover { background:rgba(44,68,99,0.08); border-color:#1A2D47; color:#1A2D47 !important; }
    .btn-custom-outline { color:#2C4463; border-color:#2C4463; }
    .btn-custom-outline:hover { background:#2C4463; color:#fff; }
    .foto-list img { width:40px; height:30px; object-fit:cover; border-radius:4px; margin-right:2px; }
    @media (max-width:576px) {
        .foto-input-group { flex-wrap: wrap; }
        .foto-input-group input[type="url"] { min-width: 160px; }
    }
    .foto-input-group { display:flex; gap:0.5rem; margin-bottom:0.5rem; align-items:center; }
    .foto-input-group input[type="url"] { flex:1; }
    .foto-input-group input[type="text"] { width:150px; }
    .select2-container--default .select2-selection--single {
        border-radius: 8px !important;
        border: 1px solid #dee2e6 !important;
        min-height: 38px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: var(--gki-primary) !important;
        box-shadow: 0 0 0 0.2rem rgba(44, 68, 99, 0.12) !important;
    }
</style>
<body>
<div class="wrapper">
    <?php $this->view('layouts/admin_sidebar', array('sidebar' => 'gereja')); ?>
    <?php $this->view('layouts/navbar'); ?>
    <div class="page-wrapper">
        <div class="page-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bx bx-church" style="color:#2C4463; font-size:1.3rem;"></i>
                    <h5 class="mb-0 fw-bold">Data Gereja</h5>
                </div>
                <button type="button" class="btn btn-custom-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
                    <i class="bx bx-plus me-1"></i>Tambah Gereja
                </button>
            </div>

            <div class="card mb-3">
                <div class="card-body py-2">
                    <form method="GET" action="<?= BASEURL; ?>admin/gereja" class="row g-2 align-items-end">
                        <div class="col-12 col-md-5">
                            <label class="form-label small mb-1">Cari</label>
                            <input type="text" name="q" class="form-control form-control-sm" placeholder="Nama gereja, alamat, provinsi..." value="<?= htmlspecialchars($filterSearch); ?>">
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label small mb-1">Provinsi</label>
                            <select name="provinsi" class="form-select form-select-sm">
                                <option value="">Semua Provinsi</option>
                                <?php foreach ($provinces as $p): ?>
                                <option value="<?= htmlspecialchars($p->name); ?>" <?= $filterProvinsi == $p->name ? 'selected' : ''; ?>><?= htmlspecialchars($p->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-custom-primary btn-sm flex-fill"><i class="bx bx-filter me-1"></i>Filter</button>
                            <a href="<?= BASEURL; ?>admin/gereja" class="btn btn-custom-outline btn-sm flex-fill"><i class="bx bx-reset me-1"></i>Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                    <table id="table-gereja" class="table table-hover" style="font-size:0.85rem;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Gereja</th>
                                <th>Wilayah</th>
                                <th>Kontak</th>
                                <th>Foto</th>
                                <th>Sosial Media</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; foreach ($gerejaList as $g): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($g->nama_gereja); ?></td>
                                <td><?= htmlspecialchars($g->provinsi); ?>, <?= htmlspecialchars($g->kabupaten_kota); ?><?= $g->kecamatan ? ', ' . htmlspecialchars($g->kecamatan) : ''; ?><?= $g->kelurahan ? ', ' . htmlspecialchars($g->kelurahan) : ''; ?></td>
                                <td><?= htmlspecialchars($g->kontak_telepon ? $g->kontak_telepon : '-'); ?></td>
                                <td>
                                    <?php if (!empty($g->foto_list)): ?>
                                    <div class="foto-list">
                                    <?php foreach ($g->foto_list as $f): ?>
                                        <img src="<?= htmlspecialchars($f->foto_url); ?>" alt="" title="<?= htmlspecialchars($f->keterangan); ?>">
                                    <?php endforeach; ?>
                                    </div>
                                    <?php else: ?>
                                    -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($g->social_media_list)): ?>
                                    <div class="d-flex gap-1 flex-wrap">
                                    <?php foreach ($g->social_media_list as $sm): ?>
                                        <a href="<?= htmlspecialchars($sm->url); ?>" target="_blank" class="btn btn-sm px-1 py-0" style="border:none;font-size:1.1rem;" title="<?= htmlspecialchars(ucfirst($sm->platform)); ?>">
                                            <?php if ($sm->platform == 'website'): ?><i class="bx bx-globe" style="color:#2C4463;"></i>
                                            <?php elseif ($sm->platform == 'instagram'): ?><i class="bx bxl-instagram" style="color:#E4405F;"></i>
                                            <?php elseif ($sm->platform == 'facebook'): ?><i class="bx bxl-facebook" style="color:#1877F2;"></i>
                                            <?php elseif ($sm->platform == 'twitter'): ?><i class="bx bxl-twitter" style="color:#1DA1F2;"></i>
                                            <?php elseif ($sm->platform == 'youtube'): ?><i class="bx bxl-youtube" style="color:#FF0000;"></i>
                                            <?php elseif ($sm->platform == 'tiktok'): ?><i class="bx bxl-tiktok" style="color:#000;"></i>
                                            <?php elseif ($sm->platform == 'linkedin'): ?><i class="bx bxl-linkedin" style="color:#0A66C2;"></i>
                                            <?php elseif ($sm->platform == 'whatsapp'): ?><i class="bx bxl-whatsapp" style="color:#25D366;"></i>
                                            <?php elseif ($sm->platform == 'telegram'): ?><i class="bx bxl-telegram" style="color:#0088CC;"></i>
                                            <?php else: ?><i class="bx bx-link" style="color:#2C4463;"></i>
                                            <?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>
                                    </div>
                                    <?php else: ?>
                                    -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-custom-outline" onclick="editGereja(<?= $g->id; ?>)"><i class="bx bx-edit"></i></button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="hapusGereja(<?= $g->id; ?>)"><i class="bx bx-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?= BASEURL; ?>admin/gereja/add">
                <?= csrf_field(); ?>
                <div class="modal-header" style="background:#2C4463; color:#fff;">
                    <h5 class="modal-title"><i class="bx bx-plus-circle me-1"></i>Tambah Gereja</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12"><label class="form-label">Nama Gereja <span class="text-danger">*</span></label><input type="text" name="nama_gereja" class="form-control" required></div>
                        <div class="col-12"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="2"></textarea></div>
                        <div class="col-6"><label class="form-label">Provinsi</label>
                            <select name="provinsi" class="form-select add-select" id="addProvinsi">
                                <option value="">-- Pilih Provinsi --</option>
                                <?php foreach ($provinces as $p): ?>
                                <option value="<?= htmlspecialchars($p->name); ?>" data-id="<?= htmlspecialchars($p->id); ?>"><?= htmlspecialchars($p->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6"><label class="form-label">Kabupaten/Kota</label>
                            <select name="kabupaten_kota" class="form-select add-select" id="addKabupaten" disabled>
                                <option value="">-- Pilih Provinsi dulu --</option>
                            </select>
                        </div>
                        <div class="col-6"><label class="form-label">Kecamatan</label>
                            <select name="kecamatan" class="form-select add-select" id="addKecamatan" disabled>
                                <option value="">-- Pilih Kabupaten dulu --</option>
                            </select>
                        </div>
                        <div class="col-6"><label class="form-label">Kelurahan</label>
                            <select name="kelurahan" class="form-select add-select" id="addKelurahan" disabled>
                                <option value="">-- Pilih Kecamatan dulu --</option>
                            </select>
                        </div>
                        <div class="col-12"><label class="form-label">Link Google Maps</label><input type="text" name="link_maps" class="form-control" id="addLinkMaps" placeholder="https://maps.google.com/?q=-6.168154,106.833206" oninput="extractLatLng('add')"></div>
                        <div class="col-6"><label class="form-label">Latitude</label><input type="text" name="latitude" class="form-control" id="addLatitude" placeholder="-6.16815400"></div>
                        <div class="col-6"><label class="form-label">Longitude</label><input type="text" name="longitude" class="form-control" id="addLongitude" placeholder="106.83320600"></div>
                        <div class="col-6"><label class="form-label">Kontak Telepon</label><input type="text" name="kontak_telepon" class="form-control"></div>
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

<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="" id="formEdit">
                <?= csrf_field(); ?>
                <div class="modal-header" style="background:#2C4463; color:#fff;">
                    <h5 class="modal-title"><i class="bx bx-edit me-1"></i>Edit Gereja</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="editBody"></div>
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

function escHtml(s) {
    if (!s) return '';
    return $('<div>').text(s).html();
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

    // 1. !3d / !4d = actual pin coordinates (take last occurrence)
    var lat3d = url.match(/!3d(-?\d+\.?\d*)/g);
    var lng4d = url.match(/!4d(-?\d+\.?\d*)/g);
    if (lat3d && lng4d && lat3d.length > 0 && lng4d.length > 0) {
        var li = Math.min(lat3d.length, lng4d.length) - 1;
        lat = lat3d[li].replace('!3d', '');
        lng = lng4d[li].replace('!4d', '');
    }

    // 2. @lat,lng = viewport center
    if (!lat) { var m = url.match(/@(-?\d+\.?\d*),(-?\d+\.?\d*)/); if (m) { lat = m[1]; lng = m[2]; } }

    // 3. ?q=lat,lng
    if (!lat) { var m = url.match(/[?&]q=(-?\d+\.?\d*),(-?\d+\.?\d*)/); if (m) { lat = m[1]; lng = m[2]; } }

    // 4. ?ll=lat,lng
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
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post('<?= BASEURL; ?>admin/gereja/delete/' + id, {csrf_token: '<?= csrf_token(); ?>'}, function(res) {
            if (res.status == 200) {
                Swal.fire({title:'Terhapus!', text:'Gereja berhasil dihapus.', icon:'success', timer:1500, showConfirmButton:false})
                    .then(() => location.reload());
            } else {
                Swal.fire({title:'Gagal!', text:res.message, icon:'error'});
            }
        });
    });
}

function editGereja(id) {
    $.get('<?= BASEURL; ?>admin/gereja/' + id, function(res) {
        var d = res.data;
        editDataCache = d;
        var fotoHtml = '';
        if (d.foto_list && d.foto_list.length > 0) {
            for (var i = 0; i < d.foto_list.length; i++) {
                var f = d.foto_list[i];
                fotoHtml += '<div class="foto-input-group">';
                fotoHtml += '<input type="url" name="foto_urls[]" class="form-control" value="' + escHtml(f.foto_url) + '">';
                fotoHtml += '<input type="text" name="foto_keterangan[]" class="form-control" value="' + escHtml(f.keterangan) + '" placeholder="Keterangan">';
                fotoHtml += '<button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.remove()"><i class="bx bx-x"></i></button>';
                fotoHtml += '</div>';
            }
        } else {
            fotoHtml = '<div class="foto-input-group"><input type="url" name="foto_urls[]" class="form-control" placeholder="https://..."><input type="text" name="foto_keterangan[]" class="form-control" placeholder="Keterangan (opsional)"></div>';
        }

        var provOpts = '<option value="">-- Pilih Provinsi --</option>';
        for (var i = 0; i < provinsiData.length; i++) {
            var s = (provinsiData[i].name == d.provinsi) ? ' selected' : '';
            provOpts += '<option value="' + escHtml(provinsiData[i].name) + '" data-id="' + provinsiData[i].id + '"' + s + '>' + escHtml(provinsiData[i].name) + '</option>';
        }

        var html = '<div class="row g-3">';
        html += '<div class="col-12"><label class="form-label">Nama Gereja</label><input type="text" name="nama_gereja" class="form-control" value="' + escHtml(d.nama_gereja) + '" required></div>';
        html += '<div class="col-12"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="2">' + escHtml(d.alamat) + '</textarea></div>';
        html += '<div class="col-6"><label class="form-label">Provinsi</label><select name="provinsi" class="form-select edit-select" id="editProvinsi">' + provOpts + '</select></div>';
        html += '<div class="col-6"><label class="form-label">Kabupaten/Kota</label><select name="kabupaten_kota" class="form-select edit-select" id="editKabupaten" disabled><option value="">-- Pilih Provinsi dulu --</option></select></div>';
        html += '<div class="col-6"><label class="form-label">Kecamatan</label><select name="kecamatan" class="form-select edit-select" id="editKecamatan" disabled><option value="">-- Pilih Kabupaten dulu --</option></select></div>';
        html += '<div class="col-6"><label class="form-label">Kelurahan</label><select name="kelurahan" class="form-select edit-select" id="editKelurahan" disabled><option value="">-- Pilih Kecamatan dulu --</option></select></div>';
        html += '<div class="col-12"><label class="form-label">Link Google Maps</label><input type="text" name="link_maps" class="form-control" id="editLinkMaps" value="' + escHtml(d.link_maps) + '" oninput="extractLatLng(\'edit\')"></div>';
        html += '<div class="col-6"><label class="form-label">Latitude</label><input type="text" name="latitude" class="form-control" id="editLatitude" value="' + d.latitude + '"></div>';
        html += '<div class="col-6"><label class="form-label">Longitude</label><input type="text" name="longitude" class="form-control" id="editLongitude" value="' + d.longitude + '"></div>';
        html += '<div class="col-6"><label class="form-label">Kontak Telepon</label><input type="text" name="kontak_telepon" class="form-control" value="' + escHtml(d.kontak_telepon) + '"></div>';
        html += '<div class="col-12"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="3">' + escHtml(d.deskripsi) + '</textarea></div>';
        html += '<div class="col-12"><label class="form-label">Foto Gereja (URL)</label><div id="editFotoList">' + fotoHtml + '</div>';
        html += '<button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="tambahFoto(\'editFotoList\')"><i class="bx bx-plus me-1"></i>Tambah Foto</button></div>';

        var sosmedHtml = '';
        if (d.social_media_list && d.social_media_list.length > 0) {
            for (var i = 0; i < d.social_media_list.length; i++) {
                var s = d.social_media_list[i];
                sosmedHtml += '<div class="sosmed-input-group d-flex gap-2 mb-1">';
                sosmedHtml += '<select name="sosmed_platform[]" class="form-select" style="width:140px;flex-shrink:0;">';
                for (var j = 0; j < sosmedPlatforms.length; j++) {
                    var sel = (sosmedPlatforms[j] == s.platform) ? ' selected' : '';
                    sosmedHtml += '<option value="' + sosmedPlatforms[j] + '"' + sel + '>' + sosmedPlatforms[j].charAt(0).toUpperCase() + sosmedPlatforms[j].slice(1) + '</option>';
                }
                sosmedHtml += '</select>';
                sosmedHtml += '<input type="url" name="sosmed_url[]" class="form-control" value="' + escHtml(s.url) + '" placeholder="https://...">';
                sosmedHtml += '<button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0" onclick="this.parentElement.remove()"><i class="bx bx-x"></i></button>';
                sosmedHtml += '</div>';
            }
        } else {
            sosmedHtml = '<div class="sosmed-input-group d-flex gap-2 mb-1">';
            sosmedHtml += '<select name="sosmed_platform[]" class="form-select" style="width:140px;flex-shrink:0;"><option value="website">Website</option><option value="instagram">Instagram</option><option value="facebook">Facebook</option><option value="twitter">Twitter</option><option value="youtube">YouTube</option><option value="tiktok">TikTok</option><option value="linkedin">LinkedIn</option><option value="whatsapp">WhatsApp</option><option value="telegram">Telegram</option></select>';
            sosmedHtml += '<input type="url" name="sosmed_url[]" class="form-control" placeholder="https://...">';
            sosmedHtml += '</div>';
        }
        html += '<div class="col-12"><label class="form-label">Sosial Media / Website</label><div id="editSosmedList">' + sosmedHtml + '</div>';
        html += '<button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="tambahSosmed(\'editSosmedList\')"><i class="bx bx-plus me-1"></i>Tambah Sosial Media</button></div>';
        html += '</div>';
        $('#editBody').html(html);
        $('#formEdit').attr('action', '<?= BASEURL; ?>admin/gereja/update/' + id);
        $('#modalEdit').modal('show');
    });
}

var editDataCache = null;

function editLoadCascade(kabName, kecName, kelName) {
    var pid = getSelectedDataId('editProvinsi');
    var m = 'modalEdit';
    if (!pid) return;
    loadSelect2('<?= BASEURL; ?>admin/gereja/getRegencies/' + pid, 'editKabupaten', kabName || '', '-- Pilih Kabupaten --', m);
    var waitForKab = setInterval(function() {
        var kabSel = document.getElementById('editKabupaten');
        if (kabSel && kabSel.options.length > 1) {
            clearInterval(waitForKab);
            if (kabName) { $(kabSel).val(kabName); $(kabSel).trigger('change'); }
            var kid = getSelectedDataId('editKabupaten');
            if (kid && kecName) {
                loadSelect2('<?= BASEURL; ?>admin/gereja/getDistricts/' + kid, 'editKecamatan', kecName || '', '-- Pilih Kecamatan --', m);
                var waitForKec = setInterval(function() {
                    var kecSel = document.getElementById('editKecamatan');
                    if (kecSel && kecSel.options.length > 1) {
                        clearInterval(waitForKec);
                        if (kecName) { $(kecSel).val(kecName); $(kecSel).trigger('change'); }
                        var kid2 = getSelectedDataId('editKecamatan');
                        if (kid2 && kelName) {
                            loadSelect2('<?= BASEURL; ?>admin/gereja/getVillages/' + kid2, 'editKelurahan', kelName || '', '-- Pilih Kelurahan --', m);
                            var waitForKel = setInterval(function() {
                                var kelSel = document.getElementById('editKelurahan');
                                if (kelSel && kelSel.options.length > 1) {
                                    clearInterval(waitForKel);
                                    if (kelName) { $(kelSel).val(kelName); }
                                }
                            }, 200);
                        }
                    }
                }, 200);
            }
        }
    }, 200);
}

(function() {
    var waitJq = setInterval(function() {
        if (typeof $ !== 'undefined' && $.fn) {
            clearInterval(waitJq);
            var s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
            s.onload = function() {
                $(function() {
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

                    $('#modalAdd').on('hidden.bs.modal', function() {
                        $('.add-select').each(function() {
                            if ($(this).hasClass('select2-hidden-accessible')) { $(this).select2('destroy'); }
                        });
                    });

                    $('#modalEdit').on('shown.bs.modal', function() {
                        if (editDataCache) {
                            initSel2('editProvinsi', 'modalEdit');
                            editLoadCascade(editDataCache.kabupaten_kota, editDataCache.kecamatan, editDataCache.kelurahan);
                            editDataCache = null;
                        }
                    });

                    $(document).on('change', '#editProvinsi', function() {
                        var pid = getSelectedDataId('editProvinsi');
                        var m = 'modalEdit';
                        resetSelect2('editKabupaten', '-- Pilih Provinsi dulu --', true);
                        resetSelect2('editKecamatan', '-- Pilih Kabupaten dulu --', true);
                        resetSelect2('editKelurahan', '-- Pilih Kecamatan dulu --', true);
                        if (pid) { loadSelect2('<?= BASEURL; ?>admin/gereja/getRegencies/' + pid, 'editKabupaten', '', '-- Pilih Kabupaten --', m); }
                    });

                    $(document).on('change', '#editKabupaten', function() {
                        var kid = getSelectedDataId('editKabupaten');
                        var m = 'modalEdit';
                        resetSelect2('editKecamatan', '-- Pilih Kabupaten dulu --', true);
                        resetSelect2('editKelurahan', '-- Pilih Kecamatan dulu --', true);
                        if (kid) { loadSelect2('<?= BASEURL; ?>admin/gereja/getDistricts/' + kid, 'editKecamatan', '', '-- Pilih Kecamatan --', m); }
                    });

                    $(document).on('change', '#editKecamatan', function() {
                        var kid = getSelectedDataId('editKecamatan');
                        var m = 'modalEdit';
                        resetSelect2('editKelurahan', '-- Pilih Kecamatan dulu --', true);
                        if (kid) { loadSelect2('<?= BASEURL; ?>admin/gereja/getVillages/' + kid, 'editKelurahan', '', '-- Pilih Kelurahan --', m); }
                    });

                    $('#modalEdit').on('hidden.bs.modal', function() {
                        $('#editBody').empty();
                        $('.edit-select').each(function() {
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
<?php $this->view('layouts/closeTag'); ?>
