<?php
require_once dirname(__FILE__) . '/../models/ModelGereja.php';
require_once dirname(__FILE__) . '/../models/ModelJadwalMisa.php';

class CariController extends Controller
{
    public function index()
    {
        $modelGereja = new ModelGereja();
        $modelJadwal = new ModelJadwalMisa();

        $keyword = isset($_GET['q']) ? sanitize($_GET['q'], 'string') : '';
        $hari = isset($_GET['hari']) ? sanitize($_GET['hari'], 'string') : '';
        $kategori = isset($_GET['kategori']) ? sanitize($_GET['kategori'], 'string') : '';

        $gerejaList = array();
        $allGereja = $modelGereja->all();

        if (!empty($keyword)) {
            foreach ($allGereja as $g) {
                if (stripos($g->nama_gereja, $keyword) !== false || stripos($g->alamat, $keyword) !== false) {
                    $gerejaList[] = $g;
                }
            }
        } else {
            $gerejaList = $allGereja;
        }

        if (!empty($hari) || !empty($kategori)) {
            $filtered = array();
            foreach ($gerejaList as $g) {
                $jadwal = $modelJadwal->getByGereja($g->id);
                foreach ($jadwal as $j) {
                    $matchHari = empty($hari) || $j->hari === $hari;
                    $matchKategori = empty($kategori) || $j->kategori === $kategori;
                    if ($matchHari && $matchKategori) {
                        $filtered[$g->id] = $g;
                        break;
                    }
                }
            }
            $gerejaList = array_values($filtered);
        }

        $hariList = array('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu', 'Spesial');
        $kategoriList = array('Harian', 'Mingguan', 'Hari Raya');

        $data = array(
            'title' => 'Cari Gereja',
            'gerejaList' => $gerejaList,
            'keyword' => $keyword,
            'selectedHari' => $hari,
            'selectedKategori' => $kategori,
            'hariList' => $hariList,
            'kategoriList' => $kategoriList
        );

        $this->view('cari/index', $data);
    }
}
