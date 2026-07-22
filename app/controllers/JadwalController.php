<?php
require_once dirname(__FILE__) . '/../models/ModelGereja.php';
require_once dirname(__FILE__) . '/../models/ModelJadwalMisa.php';

class JadwalController extends Controller
{
    public function index()
    {
        $modelGereja = new ModelGereja();
        $modelJadwal = new ModelJadwalMisa();

        $hari = isset($_GET['hari']) ? sanitize($_GET['hari'], 'string') : '';
        $provinsi = isset($_GET['provinsi']) ? sanitize($_GET['provinsi'], 'string') : '';
        $kabupaten = isset($_GET['kabupaten']) ? sanitize($_GET['kabupaten'], 'string') : '';

        $allGereja = $modelGereja->all();

        $gerejaList = array();
        foreach ($allGereja as $g) {
            if (!empty($provinsi) && $g->provinsi !== $provinsi) continue;
            if (!empty($kabupaten) && $g->kabupaten_kota !== $kabupaten) continue;

            $jadwal = $modelJadwal->getByGereja($g->id);
            if (empty($jadwal)) continue;

            if (!empty($hari)) {
                $match = false;
                foreach ($jadwal as $j) {
                    if ($j->hari === $hari) { $match = true; break; }
                }
                if (!$match) continue;
            }

            $g->jadwal_list = $jadwal;
            $gerejaList[] = $g;
        }

        $hariIndo = array('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu', 'Spesial');

        $provinsiList = array();
        $kabupatenList = array();
        foreach ($allGereja as $g) {
            if (!empty($g->provinsi) && !in_array($g->provinsi, $provinsiList)) $provinsiList[] = $g->provinsi;
            if (!empty($g->kabupaten_kota) && !in_array($g->kabupaten_kota, $kabupatenList)) $kabupatenList[] = $g->kabupaten_kota;
        }
        sort($provinsiList);
        sort($kabupatenList);

        $data = array(
            'title' => 'Jadwal Misa',
            'gerejaList' => $gerejaList,
            'selectedHari' => $hari,
            'selectedProvinsi' => $provinsi,
            'selectedKabupaten' => $kabupaten,
            'hariList' => $hariIndo,
            'provinsiList' => $provinsiList,
            'kabupatenList' => $kabupatenList
        );

        $this->view('jadwal/index', $data);
    }
}
