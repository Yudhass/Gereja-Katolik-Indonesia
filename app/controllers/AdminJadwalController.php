<?php
require_once dirname(__FILE__) . '/../models/ModelJadwalMisa.php';
require_once dirname(__FILE__) . '/../models/ModelGereja.php';

class AdminJadwalController extends Controller
{
    public function get($id = null)
    {
        $model = new ModelJadwalMisa();
        $data = $model->find($id);

        if ($data) {
            return jsonResponse(200, 'Data jadwal', $data);
        } else {
            return jsonResponse(404, 'Jadwal tidak ditemukan');
        }
    }

    public function index()
    {
        $model = new ModelJadwalMisa();
        $modelGereja = new ModelGereja();

        $allJadwal = $model->getWithGereja();
        $gerejaList = $modelGereja->all();

        $filterGereja = isset($_GET['gereja']) ? (int)$_GET['gereja'] : 0;
        $filterHari = isset($_GET['hari']) ? sanitize($_GET['hari'], 'string') : '';
        $filterSearch = isset($_GET['q']) ? sanitize($_GET['q'], 'string') : '';

        $jadwalGrouped = array();
        $hariUrut = array('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Spesial');
        foreach ($allJadwal as $j) {
            if ($filterGereja > 0 && $j->gereja_id != $filterGereja) continue;
            if (!empty($filterHari) && $j->hari !== $filterHari) continue;
            if (!empty($filterSearch)) {
                $kw = strtolower($filterSearch);
                $match = false;
                if (stripos($j->nama_gereja, $kw) !== false) $match = true;
                if (stripos($j->hari, $kw) !== false) $match = true;
                if (stripos($j->kategori, $kw) !== false) $match = true;
                if (stripos($j->keterangan, $kw) !== false) $match = true;
                if (stripos($j->tanggal, $kw) !== false) $match = true;
                if (!$match) continue;
            }
            $gid = $j->gereja_id;
            if (!isset($jadwalGrouped[$gid])) {
                $jadwalGrouped[$gid] = array(
                    'nama_gereja' => $j->nama_gereja,
                    'daftar' => array()
                );
            }
            $jadwalGrouped[$gid]['daftar'][] = $j;
        }

        $data = array(
            'title' => 'Data Jadwal Misa',
            'jadwalGrouped' => $jadwalGrouped,
            'gerejaList' => $gerejaList,
            'filterGereja' => $filterGereja,
            'filterHari' => $filterHari,
            'filterSearch' => $filterSearch
        );
        $this->view('admin/jadwal/index', $data);
    }

    public function add()
    {
        if (CSRF_ENABLED && !verify_csrf()) {
            $this->redirectBack('Token CSRF tidak valid.', 'error');
            return;
        }

        $model = new ModelJadwalMisa();
        $data = array(
            'gereja_id' => isset($_POST['gereja_id']) ? (int)$_POST['gereja_id'] : 0,
            'hari' => isset($_POST['hari']) ? sanitize($_POST['hari'], 'string') : '',
            'tanggal' => isset($_POST['tanggal']) && $_POST['tanggal'] !== '' ? $_POST['tanggal'] : null,
            'waktu_mulai' => isset($_POST['waktu_mulai']) ? sanitize($_POST['waktu_mulai'], 'string') : '',
            'kategori' => isset($_POST['kategori']) ? sanitize($_POST['kategori'], 'string') : '',
            'keterangan' => isset($_POST['keterangan']) ? sanitize($_POST['keterangan'], 'string') : '',
        );

        if ($model->insert($data)) {
            $this->redirect('admin/jadwal', 'Jadwal misa berhasil ditambahkan.', 'success');
        } else {
            $this->redirectBack('Gagal menambahkan jadwal.', 'error');
        }
    }

    public function update($id = null)
    {
        if (CSRF_ENABLED && !verify_csrf()) {
            $this->redirectBack('Token CSRF tidak valid.', 'error');
            return;
        }

        $model = new ModelJadwalMisa();
        $data = array(
            'id' => $id,
            'gereja_id' => isset($_POST['gereja_id']) ? (int)$_POST['gereja_id'] : 0,
            'hari' => isset($_POST['hari']) ? sanitize($_POST['hari'], 'string') : '',
            'tanggal' => isset($_POST['tanggal']) && $_POST['tanggal'] !== '' ? $_POST['tanggal'] : null,
            'waktu_mulai' => isset($_POST['waktu_mulai']) ? sanitize($_POST['waktu_mulai'], 'string') : '',
            'kategori' => isset($_POST['kategori']) ? sanitize($_POST['kategori'], 'string') : '',
            'keterangan' => isset($_POST['keterangan']) ? sanitize($_POST['keterangan'], 'string') : '',
        );

        $result = $model->update($data, $id);
        if ($result !== false) {
            $this->redirect('admin/jadwal', 'Jadwal misa berhasil diperbarui.', 'success');
        } else {
            $this->redirectBack('Gagal memperbarui jadwal.', 'error');
        }
    }

    public function delete($id = null)
    {
        if (CSRF_ENABLED && !verify_csrf()) {
            return jsonResponse(403, 'Token CSRF tidak valid');
        }

        $model = new ModelJadwalMisa();
        if ($model->delete($id)) {
            return jsonResponse(200, 'Jadwal berhasil dihapus');
        } else {
            return jsonResponse(500, 'Gagal menghapus jadwal');
        }
    }
}
