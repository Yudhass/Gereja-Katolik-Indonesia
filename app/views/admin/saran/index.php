<?php $this->view('layouts/openTag', array('title' => $title, 'css' => array('assets/plugins/datatable/css/dataTables.bootstrap5.min.css'))); ?>
<style>
    .btn-custom-primary { background:#fff; border:2px solid #2C4463; color:#2C4463 !important; font-weight:600; }
    .btn-custom-primary:hover { background:rgba(44,68,99,0.08); border-color:#1A2D47; color:#1A2D47 !important; }
    .btn-approve { background:#198754; border-color:#198754; color:#fff; }
    .btn-approve:hover { background:#157347; color:#fff; }
    .btn-reject { background:#dc3545; border-color:#dc3545; color:#fff; }
    .btn-reject:hover { background:#bb2d3b; color:#fff; }
</style>
<body>
<div class="wrapper">
    <?php $this->view('layouts/admin_sidebar', array('sidebar' => 'saran')); ?>
    <?php $this->view('layouts/navbar'); ?>
    <div class="page-wrapper">
        <div class="page-content">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bx bx-message-dots" style="color:#2C4463; font-size:1.3rem;"></i>
                <h5 class="mb-0 fw-bold">Kotak Saran Jadwal</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                    <table id="table-saran" class="table table-hover" style="font-size:0.85rem;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Gereja</th>
                                <th>Pengunjung</th>
                                <th>Saran Hari</th>
                                <th>Saran Waktu</th>
                                <th>Catatan</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; foreach ($saranList as $s): ?>
                            <tr class="<?= $s->status == 'Pending' ? 'table-warning' : ''; ?>">
                                <td><?= $no++; ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($s->nama_gereja); ?></td>
                                <td><?= htmlspecialchars($s->nama_pengunjung ? $s->nama_pengunjung : '-'); ?></td>
                                <td><?= htmlspecialchars($s->saran_hari ? $s->saran_hari : '-'); ?></td>
                                <td><?= $s->saran_waktu ? date('H:i', strtotime($s->saran_waktu)) : '-'; ?></td>
                                <td style="max-width:200px;"><?= htmlspecialchars($s->catatan ? $s->catatan : '-'); ?></td>
                                <td>
                                    <?php if ($s->status == 'Pending'): ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php elseif ($s->status == 'Approved'): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size:0.8rem;"><?= date('d/m/Y H:i', strtotime($s->created_at)); ?></td>
                                <td>
                                    <?php if ($s->status == 'Pending'): ?>
                                    <div class="d-flex gap-1">
                                        <form method="POST" action="<?= BASEURL; ?>admin/saran/approve/<?= $s->id; ?>" style="display:inline;" class="saran-form">
                                            <?= csrf_field(); ?>
                                            <button type="button" class="btn btn-sm btn-approve" onclick="confirmSaran(this, 'setujui', 'Setujui saran ini? Jadwal akan diperbarui.')"><i class="bx bx-check"></i></button>
                                        </form>
                                        <form method="POST" action="<?= BASEURL; ?>admin/saran/reject/<?= $s->id; ?>" style="display:inline;" class="saran-form">
                                            <?= csrf_field(); ?>
                                            <button type="button" class="btn btn-sm btn-reject" onclick="confirmSaran(this, 'tolak', 'Tolak saran ini?')"><i class="bx bx-x"></i></button>
                                        </form>
                                    </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($saranList)): ?>
                            <tr><td colspan="9" class="text-center text-muted py-4">Belum ada saran masuk</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function confirmSaran(btn, label, msg) {
    Swal.fire({
        title: label === 'setujui' ? 'Setujui Saran' : 'Tolak Saran',
        text: msg,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: label === 'setujui' ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, ' + label + '!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) btn.closest('form').submit();
    });
}
</script>
<?php $this->view('layouts/closeTag'); ?>
