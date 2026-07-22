<?php $this->view('layouts/guest_openTag', array('title' => $title)); ?>

<style>
    .page-header { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; padding: 1.5rem 1rem; text-align: center; }
    .page-header h1 { font-size: 1.4rem; font-weight: 700; color: #fff; }
    .form-card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 1rem; overflow: hidden; }
    .form-card .card-header { background: var(--primary); color: white; font-weight: 700; border: none; padding: 0.75rem 1rem; }
    .form-card .card-body { padding: 1rem; }
    .form-label { font-weight: 600; font-size: 0.85rem; color: #222; margin-bottom: 0.25rem; }
    .form-control-custom { border-radius: 10px; border: 1px solid #bbb; padding: 0.6rem 0.85rem; font-size: 0.9rem; }
    .form-control-custom:focus { border-color: var(--primary); box-shadow: 0 0 0 0.2rem rgba(44,68,99,0.1); }
    .btn-submit { background: var(--primary); color: white; border: none; border-radius: 50px; padding: 0.65rem 2rem; font-weight: 600; }
    .btn-submit:hover { background: var(--primary-dark); color: white; }
    .btn-batal { border: 2px solid #bbb; color: #444; border-radius: 50px; padding: 0.65rem 2rem; font-weight: 600; }
    .btn-batal:hover { background: #f5f5f5; }
</style>

<div class="page-header">
    <h1><i class="bx bx-flag me-1"></i>Laporkan Koreksi Jadwal</h1>
    <p class="mb-0 small" style="color:#fff;"><?= htmlspecialchars($gereja->nama_gereja); ?></p>
</div>

<div class="container px-3 px-md-4 py-3 pb-5 mb-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="form-card">
                <div class="card-header"><i class="bx bx-edit me-1"></i>Form Saran Koreksi</div>
                <div class="card-body">
                    <form action="<?= BASEURL; ?>saran/kirim" method="POST">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="gereja_id" value="<?= $gereja->id; ?>">

                        <div class="mb-3">
                            <label class="form-label">Nama Anda (opsional)</label>
                            <input type="text" name="nama_pengunjung" class="form-control form-control-custom" placeholder="Masukkan nama Anda" maxlength="100">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hari yang Disarankan</label>
                            <select name="saran_hari" class="form-select form-control-custom">
                                <option value="">-- Pilih Hari --</option>
                                <?php foreach (array('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Spesial') as $h): ?>
                                <option value="<?= $h; ?>"><?= $h; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Waktu yang Disarankan</label>
                            <input type="time" name="saran_waktu" class="form-control form-control-custom">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan / Alasan</label>
                            <textarea name="catatan" class="form-control form-control-custom" rows="4" placeholder="Contoh: Misa minggu pagi sekarang jam 08:00, bukan jam 07:00"></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-submit"><i class="bx bx-send me-1"></i>Kirim Saran</button>
                            <a href="<?= BASEURL; ?>gereja/<?= htmlspecialchars($gereja->slug ? $gereja->slug : $gereja->id); ?>" class="btn btn-batal">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->view('layouts/guest_bottom_nav'); ?>
<?php $this->view('layouts/guest_closeTag'); ?>
