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
                    <i class="bx bx-edit" style="color:#2C4463; font-size:1.3rem;"></i>
                    <h5 class="mb-0 fw-bold">Edit Gereja</h5>
                </div>
                <a href="<?= BASEURL; ?>admin/gereja<?= (!empty($filterSearch) || !empty($filterProvinsi)) ? '?q=' . urlencode($filterSearch) . '&provinsi=' . urlencode($filterProvinsi) : ''; ?>" class="btn btn-custom-outline btn-sm">
                    <i class="bx bx-arrow-back me-1"></i>Kembali
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?= BASEURL; ?>admin/gereja/update/<?= $gereja->id; ?>">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="filter_q" value="<?= htmlspecialchars($filterSearch); ?>">
                        <input type="hidden" name="filter_provinsi" value="<?= htmlspecialchars($filterProvinsi); ?>">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Nama Gereja <span class="text-danger">*</span></label>
                                <input type="text" name="nama_gereja" class="form-control" value="<?= htmlspecialchars($gereja->nama_gereja); ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="2"><?= htmlspecialchars($gereja->alamat); ?></textarea>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Provinsi</label>
                                <select name="provinsi" class="form-select edit-select" id="editProvinsi">
                                    <option value="">-- Pilih Provinsi --</option>
                                    <?php foreach ($provinces as $p): ?>
                                    <option value="<?= htmlspecialchars($p->name); ?>" data-id="<?= htmlspecialchars($p->id); ?>" <?= $gereja->provinsi == $p->name ? 'selected' : ''; ?>><?= htmlspecialchars($p->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Kabupaten/Kota</label>
                                <select name="kabupaten_kota" class="form-select edit-select" id="editKabupaten" disabled>
                                    <option value="">-- Pilih Provinsi dulu --</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Kecamatan</label>
                                <select name="kecamatan" class="form-select edit-select" id="editKecamatan" disabled>
                                    <option value="">-- Pilih Kabupaten dulu --</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Kelurahan</label>
                                <select name="kelurahan" class="form-select edit-select" id="editKelurahan" disabled>
                                    <option value="">-- Pilih Kecamatan dulu --</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Link Google Maps</label>
                                <input type="text" name="link_maps" class="form-control" id="editLinkMaps" value="<?= htmlspecialchars($gereja->link_maps); ?>" placeholder="https://maps.google.com/?q=-6.168154,106.833206" oninput="extractLatLng('edit')">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Latitude</label>
                                <input type="text" name="latitude" class="form-control" id="editLatitude" value="<?= htmlspecialchars($gereja->latitude); ?>" placeholder="-6.16815400">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Longitude</label>
                                <input type="text" name="longitude" class="form-control" id="editLongitude" value="<?= htmlspecialchars($gereja->longitude); ?>" placeholder="106.83320600">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Kontak Telepon</label>
                                <input type="text" name="kontak_telepon" class="form-control" value="<?= htmlspecialchars($gereja->kontak_telepon); ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($gereja->deskripsi); ?></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Foto Gereja (URL)</label>
                                <div id="editFotoList">
                                    <?php if (!empty($gereja->foto_list)): ?>
                                    <?php foreach ($gereja->foto_list as $f): ?>
                                    <div class="foto-input-group">
                                        <input type="url" name="foto_urls[]" class="form-control" value="<?= htmlspecialchars($f->foto_url); ?>">
                                        <input type="text" name="foto_keterangan[]" class="form-control" value="<?= htmlspecialchars($f->keterangan); ?>" placeholder="Keterangan">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.remove()"><i class="bx bx-x"></i></button>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <div class="foto-input-group">
                                        <input type="url" name="foto_urls[]" class="form-control" placeholder="https://...">
                                        <input type="text" name="foto_keterangan[]" class="form-control" placeholder="Keterangan (opsional)">
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="tambahFoto('editFotoList')"><i class="bx bx-plus me-1"></i>Tambah Foto</button>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Sosial Media / Website</label>
                                <div id="editSosmedList">
                                    <?php if (!empty($gereja->social_media_list)): ?>
                                    <?php foreach ($gereja->social_media_list as $sm): ?>
                                    <div class="sosmed-input-group d-flex gap-2 mb-1">
                                        <select name="sosmed_platform[]" class="form-select" style="width:140px;flex-shrink:0;">
                                            <option value="website" <?= $sm->platform == 'website' ? 'selected' : ''; ?>>Website</option>
                                            <option value="instagram" <?= $sm->platform == 'instagram' ? 'selected' : ''; ?>>Instagram</option>
                                            <option value="facebook" <?= $sm->platform == 'facebook' ? 'selected' : ''; ?>>Facebook</option>
                                            <option value="twitter" <?= $sm->platform == 'twitter' ? 'selected' : ''; ?>>Twitter</option>
                                            <option value="youtube" <?= $sm->platform == 'youtube' ? 'selected' : ''; ?>>YouTube</option>
                                            <option value="tiktok" <?= $sm->platform == 'tiktok' ? 'selected' : ''; ?>>TikTok</option>
                                            <option value="linkedin" <?= $sm->platform == 'linkedin' ? 'selected' : ''; ?>>LinkedIn</option>
                                            <option value="whatsapp" <?= $sm->platform == 'whatsapp' ? 'selected' : ''; ?>>WhatsApp</option>
                                            <option value="telegram" <?= $sm->platform == 'telegram' ? 'selected' : ''; ?>>Telegram</option>
                                        </select>
                                        <input type="url" name="sosmed_url[]" class="form-control" value="<?= htmlspecialchars($sm->url); ?>" placeholder="https://...">
                                        <button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0" onclick="this.parentElement.remove()"><i class="bx bx-x"></i></button>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php else: ?>
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
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="tambahSosmed('editSosmedList')"><i class="bx bx-plus me-1"></i>Tambah Sosial Media</button>
                            </div>
                        </div>
                        <div class="mt-4 d-flex gap-2">
                            <a href="<?= BASEURL; ?>admin/gereja<?= (!empty($filterSearch) || !empty($filterProvinsi)) ? '?q=' . urlencode($filterSearch) . '&provinsi=' . urlencode($filterProvinsi) : ''; ?>" class="btn btn-secondary"><i class="bx bx-x me-1"></i>Batal</a>
                            <button type="submit" class="btn btn-custom-primary"><i class="bx bx-save me-1"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
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

function loadSelect2(url, selectId, selectedName, placeholder, disabled) {
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
        sel.select2({ width: '100%' });
    });
}

function initSel2(selectId) {
    var sel = $('#' + selectId);
    if (sel.hasClass('select2-hidden-accessible')) {
        sel.select2('destroy');
    }
    sel.select2({ width: '100%' });
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

var editDataCache = {
    kabupaten_kota: '<?= htmlspecialchars($gereja->kabupaten_kota, ENT_QUOTES); ?>',
    kecamatan: '<?= htmlspecialchars($gereja->kecamatan, ENT_QUOTES); ?>',
    kelurahan: '<?= htmlspecialchars($gereja->kelurahan, ENT_QUOTES); ?>'
};

function editLoadCascade(kabName, kecName, kelName) {
    var pid = getSelectedDataId('editProvinsi');
    if (!pid) return;
    loadSelect2('<?= BASEURL; ?>admin/gereja/getRegencies/' + pid, 'editKabupaten', kabName || '', '-- Pilih Kabupaten --');
    var waitForKab = setInterval(function() {
        var kabSel = document.getElementById('editKabupaten');
        if (kabSel && kabSel.options.length > 1) {
            clearInterval(waitForKab);
            if (kabName) { $(kabSel).val(kabName); $(kabSel).trigger('change'); }
            var kid = getSelectedDataId('editKabupaten');
            if (kid && kecName) {
                loadSelect2('<?= BASEURL; ?>admin/gereja/getDistricts/' + kid, 'editKecamatan', kecName || '', '-- Pilih Kecamatan --');
                var waitForKec = setInterval(function() {
                    var kecSel = document.getElementById('editKecamatan');
                    if (kecSel && kecSel.options.length > 1) {
                        clearInterval(waitForKec);
                        if (kecName) { $(kecSel).val(kecName); $(kecSel).trigger('change'); }
                        var kid2 = getSelectedDataId('editKecamatan');
                        if (kid2 && kelName) {
                            loadSelect2('<?= BASEURL; ?>admin/gereja/getVillages/' + kid2, 'editKelurahan', kelName || '', '-- Pilih Kelurahan --');
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
                    initSel2('editProvinsi');

                    if (editDataCache) {
                        editLoadCascade(editDataCache.kabupaten_kota, editDataCache.kecamatan, editDataCache.kelurahan);
                    }

                    $('#editProvinsi').on('change', function() {
                        var pid = getSelectedDataId('editProvinsi');
                        resetSelect2('editKabupaten', '-- Pilih Provinsi dulu --', true);
                        resetSelect2('editKecamatan', '-- Pilih Kabupaten dulu --', true);
                        resetSelect2('editKelurahan', '-- Pilih Kecamatan dulu --', true);
                        if (pid) { loadSelect2('<?= BASEURL; ?>admin/gereja/getRegencies/' + pid, 'editKabupaten', '', '-- Pilih Kabupaten --'); }
                    });

                    $(document).on('change', '#editKabupaten', function() {
                        var kid = getSelectedDataId('editKabupaten');
                        resetSelect2('editKecamatan', '-- Pilih Kabupaten dulu --', true);
                        resetSelect2('editKelurahan', '-- Pilih Kecamatan dulu --', true);
                        if (kid) { loadSelect2('<?= BASEURL; ?>admin/gereja/getDistricts/' + kid, 'editKecamatan', '', '-- Pilih Kecamatan --'); }
                    });

                    $(document).on('change', '#editKecamatan', function() {
                        var kid = getSelectedDataId('editKecamatan');
                        resetSelect2('editKelurahan', '-- Pilih Kecamatan dulu --', true);
                        if (kid) { loadSelect2('<?= BASEURL; ?>admin/gereja/getVillages/' + kid, 'editKelurahan', '', '-- Pilih Kelurahan --'); }
                    });
                });
            };
            document.head.appendChild(s);
        }
    }, 50);
})();
</script>
<?php $this->view('layouts/closeTag'); ?>
