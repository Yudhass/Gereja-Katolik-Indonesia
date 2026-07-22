<?php $this->view('layouts/openTag', array('title' => $title, 'css' => array('assets/plugins/datatable/css/dataTables.bootstrap5.min.css'))); ?>
<style>
    .btn-custom-primary { background:#fff; border:2px solid #2C4463; color:#2C4463 !important; font-weight:600; }
    .btn-custom-primary:hover { background:rgba(44,68,99,0.08); border-color:#1A2D47; color:#1A2D47 !important; }
    .btn-custom-outline { color:#2C4463; border-color:#2C4463; }
    .btn-custom-outline:hover { background:#2C4463; color:#fff; }

</style>
<body>
<div class="wrapper">
    <?php $this->view('layouts/admin_sidebar', array('sidebar' => 'jadwal')); ?>
    <?php $this->view('layouts/navbar'); ?>
    <div class="page-wrapper">
        <div class="page-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bx bx-calendar-event" style="color:#2C4463; font-size:1.3rem;"></i>
                    <h5 class="mb-0 fw-bold">Jadwal Misa</h5>
                </div>
                <button type="button" class="btn btn-custom-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
                    <i class="bx bx-plus me-1"></i>Tambah Jadwal
                </button>
            </div>

            <div class="card mb-3">
                <div class="card-body py-2">
                    <form method="GET" action="<?= BASEURL; ?>admin/jadwal" class="row g-2 align-items-end">
                        <div class="col-12 col-md-4">
                            <label class="form-label small mb-1">Cari</label>
                            <input type="text" name="q" class="form-control form-control-sm" placeholder="Nama gereja, hari, kategori..." value="<?= htmlspecialchars($filterSearch); ?>">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small mb-1">Gereja</label>
                            <select name="gereja" class="form-select form-select-sm">
                                <option value="">Semua Gereja</option>
                                <?php foreach ($gerejaList as $g): ?>
                                <option value="<?= $g->id; ?>" <?= $filterGereja == $g->id ? 'selected' : ''; ?>><?= htmlspecialchars($g->nama_gereja); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small mb-1">Hari</label>
                            <select name="hari" class="form-select form-select-sm">
                                <option value="">Semua Hari</option>
                                <?php foreach (array('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Spesial') as $h): ?>
                                <option value="<?= $h; ?>" <?= $filterHari == $h ? 'selected' : ''; ?>><?= $h; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <button type="submit" class="btn btn-custom-primary btn-sm w-100"><i class="bx bx-filter me-1"></i>Filter</button>
                        </div>
                        <div class="col-6 col-md-2">
                            <a href="<?= BASEURL; ?>admin/jadwal" class="btn btn-outline-secondary btn-sm w-100"><i class="bx bx-reset me-1"></i>Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (empty($jadwalGrouped)): ?>
            <div class="text-center py-5">
                <i class="bx bx-calendar-x" style="font-size:3rem; color:#bbb;"></i>
                <p class="mt-2 text-muted">Tidak ada jadwal ditemukan.</p>
            </div>
            <?php else: ?>
            <p class="text-muted small mb-2">Menampilkan jadwal dari <strong><?= count($jadwalGrouped); ?></strong> gereja</p>
            <?php foreach ($jadwalGrouped as $gid => $group): ?>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center" style="background:#2C4463; color:#fff;">
                    <h6 class="mb-0 fw-bold"><i class="bx bx-church me-1"></i><?= htmlspecialchars($group['nama_gereja']); ?></h6>
                    <span class="badge bg-light text-dark"><?= count($group['daftar']); ?> jadwal</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                    <table class="table table-hover mb-0 jadwal-group" style="font-size:0.85rem;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Hari</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Kategori</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; foreach ($group['daftar'] as $j): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $j->hari; ?></td>
                                <td><?= $j->tanggal ? date('d/m/Y', strtotime($j->tanggal)) : '-'; ?></td>
                                <td><?= date('H:i', strtotime($j->waktu_mulai)); ?></td>
                                <td><span class="badge" style="background:rgba(44,68,99,.1); color:#2C4463;"><?= $j->kategori; ?></span></td>
                                <td><?= htmlspecialchars($j->keterangan ? $j->keterangan : '-'); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-custom-outline" onclick="editJadwal(<?= $j->id; ?>)"><i class="bx bx-edit"></i></button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="hapusJadwal(<?= $j->id; ?>)"><i class="bx bx-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= BASEURL; ?>admin/jadwal/add">
                <?= csrf_field(); ?>
                <div class="modal-header" style="background:#2C4463; color:#fff;">
                    <h5 class="modal-title"><i class="bx bx-plus-circle me-1"></i>Tambah Jadwal Misa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Gereja <span class="text-danger">*</span></label>
                        <select name="gereja_id" class="form-select" required>
                            <option value="">-- Pilih Gereja --</option>
                            <?php foreach ($gerejaList as $g): ?>
                            <option value="<?= $g->id; ?>"><?= htmlspecialchars($g->nama_gereja); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hari <span class="text-danger">*</span></label>
                        <select name="hari" class="form-select" required>
                            <?php foreach (array('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Spesial') as $h): ?>
                            <option value="<?= $h; ?>"><?= $h; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal (kosongkan jika rutin)</label>
                        <input type="date" name="tanggal" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                        <input type="time" name="waktu_mulai" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select">
                            <option value="Harian">Harian</option>
                            <option value="Mingguan" selected>Mingguan</option>
                            <option value="Hari Raya">Hari Raya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Misa Bahasa Inggris">
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
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" id="formEdit">
                <?= csrf_field(); ?>
                <div class="modal-header" style="background:#2C4463; color:#fff;">
                    <h5 class="modal-title"><i class="bx bx-edit me-1"></i>Edit Jadwal Misa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="editBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-custom-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editJadwal(id) {
    $.get('<?= BASEURL; ?>admin/jadwal/' + id, function(res) {
        var d = res.data;
        var gerejaOptions = '';
        <?php foreach ($gerejaList as $g): ?>
        gerejaOptions += '<option value="<?= $g->id; ?>" ' + (d.gereja_id == <?= $g->id; ?> ? 'selected' : '') + '><?= htmlspecialchars($g->nama_gereja); ?></option>';
        <?php endforeach; ?>
        var hariOptions = '';
        <?php foreach (array('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Spesial') as $h): ?>
        hariOptions += '<option value="<?= $h; ?>" ' + (d.hari == '<?= $h; ?>' ? 'selected' : '') + '><?= $h; ?></option>';
        <?php endforeach; ?>
        var html = '<div class="mb-3"><label class="form-label">Gereja</label><select name="gereja_id" class="form-select">' + gerejaOptions + '</select></div>';
        html += '<div class="mb-3"><label class="form-label">Hari</label><select name="hari" class="form-select">' + hariOptions + '</select></div>';
        html += '<div class="mb-3"><label class="form-label">Tanggal (kosongkan jika rutin)</label><input type="date" name="tanggal" class="form-control" value="' + (d.tanggal || '') + '"></div>';
        html += '<div class="mb-3"><label class="form-label">Waktu Mulai</label><input type="time" name="waktu_mulai" class="form-control" value="' + d.waktu_mulai.substring(0,5) + '"></div>';
        html += '<div class="mb-3"><label class="form-label">Kategori</label><select name="kategori" class="form-select">';
        ['Harian','Mingguan','Hari Raya'].forEach(function(k) { html += '<option value="' + k + '" ' + (d.kategori == k ? 'selected' : '') + '>' + k + '</option>'; });
        html += '</select></div>';
        html += '<div class="mb-3"><label class="form-label">Keterangan</label><input type="text" name="keterangan" class="form-control" value="' + escHtml(d.keterangan) + '"></div>';
        $('#editBody').html(html);
        $('#formEdit').attr('action', '<?= BASEURL; ?>admin/jadwal/update/' + id);
        $('#modalEdit').modal('show');
    });
}

function hapusJadwal(id) {
    Swal.fire({
        title: 'Hapus Jadwal',
        text: 'Yakin ingin menghapus jadwal ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post('<?= BASEURL; ?>admin/jadwal/delete/' + id, {csrf_token: '<?= csrf_token(); ?>'}, function(res) {
            if (res.status == 200) {
                Swal.fire({title:'Terhapus!', text:'Jadwal berhasil dihapus.', icon:'success', timer:1500, showConfirmButton:false})
                    .then(() => location.reload());
            } else {
                Swal.fire({title:'Gagal!', text:res.message, icon:'error'});
            }
        });
    });
}

function escHtml(s) {
    if (!s) return '';
    return $('<div>').text(s).html();
}
</script>
<?php $this->view('layouts/closeTag'); ?>
