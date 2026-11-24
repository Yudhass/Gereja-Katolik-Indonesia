<?php $this->view('layouts/guest_openTag', array('title' => $title)); ?>
<?php
$detailUrl = BASEURL . 'gereja/' . htmlspecialchars($gereja->slug ? $gereja->slug : $gereja->id);
$flash = function_exists('getFlashMessage') ? getFlashMessage() : null;
?>

<?php $this->view('layouts/guest_navbar'); ?>

<header class="gki-page-header">
    <div class="gki-container">
        <a href="<?= $detailUrl; ?>" class="gki-breadcrumb">
            <i class="bx bx-arrow-back" aria-hidden="true"></i>Kembali ke detail gereja
        </a>
        <h1>Laporkan Koreksi Jadwal</h1>
        <p><?= e_text($gereja->nama_gereja); ?></p>
    </div>
</header>

<main class="gki-container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <?php if ($flash):
                $alertClass = 'gki-alert-info';
                $alertIcon = 'bx bx-info-circle';
                if ($flash['type'] === 'success') { $alertClass = 'gki-alert-success'; $alertIcon = 'bx bx-check-circle'; }
                elseif ($flash['type'] === 'error') { $alertClass = 'gki-alert-error'; $alertIcon = 'bx bx-x-circle'; }
                elseif ($flash['type'] === 'warning') { $alertClass = 'gki-alert-warning'; $alertIcon = 'bx bx-error'; }
            ?>
            <div class="gki-alert <?= $alertClass; ?>" role="alert">
                <i class="<?= $alertIcon; ?>" aria-hidden="true"></i>
                <div><?= htmlspecialchars($flash['message']); ?></div>
            </div>
            <?php endif; ?>

            <section class="gki-panel p-4">
                <h2 class="gki-section-title mb-1">Form Saran Koreksi</h2>
                <p class="small mb-4" style="color: var(--ink-secondary);">Bantu kami menjaga jadwal misa tetap akurat. Saran Anda akan ditinjau admin sebelum ditampilkan.</p>

                <form action="<?= BASEURL; ?>saran/kirim" method="POST">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="gereja_id" value="<?= $gereja->id; ?>">

                    <div class="gki-field mb-3">
                        <label for="nama_pengunjung">Nama Anda (opsional)</label>
                        <input type="text" id="nama_pengunjung" name="nama_pengunjung" class="gki-input" placeholder="Contoh: Antonius Wibowo" maxlength="100">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6 gki-field">
                            <label for="saran_hari">Hari yang Disarankan</label>
                            <select id="saran_hari" name="saran_hari" class="gki-select">
                                <option value="">Pilih hari</option>
                                <?php foreach (array('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Spesial') as $h): ?>
                                <option value="<?= $h; ?>"><?= $h; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 gki-field">
                            <label for="saran_waktu">Waktu yang Disarankan</label>
                            <input type="time" id="saran_waktu" name="saran_waktu" class="gki-input">
                        </div>
                    </div>

                    <div class="gki-field mb-4">
                        <label for="catatan">Catatan atau Alasan</label>
                        <textarea id="catatan" name="catatan" class="gki-textarea" rows="4" placeholder="Contoh: Misa Minggu pagi sekarang mulai pukul 08:00 WIB, bukan 07:00 WIB."></textarea>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <button type="submit" class="gki-btn gki-btn-primary flex-fill">
                            <i class="bx bx-send" aria-hidden="true"></i>
                            <span>Kirim Saran Koreksi</span>
                        </button>
                        <a href="<?= $detailUrl; ?>" class="gki-btn gki-btn-outline">Batal</a>
                    </div>
                </form>
            </section>
        </div>
    </div>
</main>

<?php $this->view('layouts/guest_footer'); ?>
<?php $this->view('layouts/guest_bottom_nav'); ?>
<?php $this->view('layouts/guest_closeTag'); ?>
