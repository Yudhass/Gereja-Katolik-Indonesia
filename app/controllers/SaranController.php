<?php
require_once dirname(__FILE__) . '/../models/ModelSaranJadwal.php';
require_once dirname(__FILE__) . '/../models/ModelGereja.php';

class SaranController extends Controller
{
    public function form($slug = null)
    {
        $modelGereja = new ModelGereja();
        $gereja = $modelGereja->findBySlug($slug);

        if (!$gereja) {
            Show404('Gereja tidak ditemukan');
            return;
        }

        $data = array(
            'title' => 'Laporkan Koreksi Jadwal',
            'gereja' => $gereja
        );

        $this->view('saran/form', $data);
    }

    public function kirim()
    {
        if (CSRF_ENABLED && !verify_csrf()) {
            $this->redirectBack('Token CSRF tidak valid.', 'error');
            return;
        }

        $gerejaId = isset($_POST['gereja_id']) ? (int)$_POST['gereja_id'] : 0;
        $nama = isset($_POST['nama_pengunjung']) ? sanitize($_POST['nama_pengunjung'], 'string') : '';
        $saranHari = isset($_POST['saran_hari']) ? sanitize($_POST['saran_hari'], 'string') : '';
        $saranWaktu = isset($_POST['saran_waktu']) ? sanitize($_POST['saran_waktu'], 'string') : '';
        $catatan = isset($_POST['catatan']) ? sanitize($_POST['catatan'], 'string') : '';

        if (empty($gerejaId)) {
            $this->redirectBack('Data gereja tidak valid.', 'error');
            return;
        }

        $modelSaran = new ModelSaranJadwal();
        $inserted = $modelSaran->insert(array(
            'gereja_id' => $gerejaId,
            'nama_pengunjung' => $nama,
            'saran_hari' => $saranHari,
            'saran_waktu' => $saranWaktu,
            'catatan' => $catatan,
            'status' => 'Pending'
        ));

        if ($inserted) {
            $gereja = $modelGereja->find($gerejaId);
            $slugRedirect = $gereja ? $gereja->slug : $gerejaId;
            $this->redirect('gereja/' . $slugRedirect, 'Terima kasih! Saran Anda telah dikirim dan akan ditinjau oleh admin.', 'success');
        } else {
            $this->redirectBack('Gagal mengirim saran. Silakan coba lagi.', 'error');
        }
    }
}
