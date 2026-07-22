<?php
require_once dirname(__FILE__) . '/../models/ModelSaranJadwal.php';
require_once dirname(__FILE__) . '/../models/ModelJadwalMisa.php';

class AdminSaranController extends Controller
{
    public function index()
    {
        $model = new ModelSaranJadwal();
        $data = array(
            'title' => 'Kotak Saran Jadwal',
            'saranList' => $model->getAllWithGereja()
        );
        $this->view('admin/saran/index', $data);
    }

    public function approve($id = null)
    {
        if (CSRF_ENABLED && !verify_csrf()) {
            $this->redirectBack('Token CSRF tidak valid.', 'error');
            return;
        }

        $modelSaran = new ModelSaranJadwal();
        $saran = $modelSaran->find($id);

        if (!$saran || $saran->status !== 'Pending') {
            $this->redirectBack('Saran tidak ditemukan atau sudah diproses.', 'error');
            return;
        }

        $modelJadwal = new ModelJadwalMisa();
        $jadwalData = array(
            'gereja_id' => $saran->gereja_id,
            'hari' => $saran->saran_hari,
            'waktu_mulai' => $saran->saran_waktu,
            'kategori' => 'Mingguan',
            'keterangan' => 'Diperbarui berdasarkan saran'
        );

        if ($saran->jadwal_id) {
            $modelJadwal->update($jadwalData, $saran->jadwal_id);
        } else {
            $modelJadwal->insert($jadwalData);
        }

        $modelSaran->update(array('id' => $id, 'status' => 'Approved'), $id);
        $this->redirect('admin/saran', 'Saran berhasil disetujui dan jadwal diperbarui.', 'success');
    }

    public function reject($id = null)
    {
        if (CSRF_ENABLED && !verify_csrf()) {
            $this->redirectBack('Token CSRF tidak valid.', 'error');
            return;
        }

        $modelSaran = new ModelSaranJadwal();
        $saran = $modelSaran->find($id);

        if (!$saran || $saran->status !== 'Pending') {
            $this->redirectBack('Saran tidak ditemukan atau sudah diproses.', 'error');
            return;
        }

        $modelSaran->update(array('id' => $id, 'status' => 'Rejected'), $id);
        $this->redirect('admin/saran', 'Saran berhasil ditolak.', 'success');
    }
}
