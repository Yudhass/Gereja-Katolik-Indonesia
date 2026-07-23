<?php
require_once dirname(__FILE__) . '/../models/ModelGereja.php';
require_once dirname(__FILE__) . '/../models/ModelJadwalMisa.php';
require_once dirname(__FILE__) . '/../models/ModelGerejaFoto.php';
require_once dirname(__FILE__) . '/../models/ModelGerejaSocialMedia.php';

class MapsController extends Controller
{
    public function index()
    {
        $modelGereja = new ModelGereja();
        $modelJadwal = new ModelJadwalMisa();
        $modelFoto = new ModelGerejaFoto();
        $modelSocial = new ModelGerejaSocialMedia();

        $filterProvinsi = isset($_GET['provinsi']) ? sanitize($_GET['provinsi'], 'string') : '';
        $filterKabupaten = isset($_GET['kabupaten']) ? sanitize($_GET['kabupaten'], 'string') : '';
        $filterKecamatan = isset($_GET['kecamatan']) ? sanitize($_GET['kecamatan'], 'string') : '';
        $filterKelurahan = isset($_GET['kelurahan']) ? sanitize($_GET['kelurahan'], 'string') : '';
        $filterJamDari = isset($_GET['jam_dari']) ? sanitize($_GET['jam_dari'], 'string') : '';

        $allGereja = $modelGereja->all();

        $provinsiList = array();
        $kabupatenList = array();
        $kecamatanList = array();
        $kelurahanList = array();
        foreach ($allGereja as $g) {
            if (!empty($g->provinsi) && !in_array($g->provinsi, $provinsiList)) $provinsiList[] = $g->provinsi;
            if (!empty($g->kabupaten_kota) && !in_array($g->kabupaten_kota, $kabupatenList)) $kabupatenList[] = $g->kabupaten_kota;
            if (!empty($g->kecamatan) && !in_array($g->kecamatan, $kecamatanList)) $kecamatanList[] = $g->kecamatan;
            if (!empty($g->kelurahan) && !in_array($g->kelurahan, $kelurahanList)) $kelurahanList[] = $g->kelurahan;
        }
        sort($provinsiList);
        sort($kabupatenList);
        sort($kecamatanList);
        sort($kelurahanList);

        $gerejaList = array();
        $allJadwal = array();
        $allFoto = array();
        $allSocial = array();

        foreach ($allGereja as $g) {
            if (empty($g->latitude) || empty($g->longitude)) continue;
            if (!empty($filterProvinsi) && $g->provinsi !== $filterProvinsi) continue;
            if (!empty($filterKabupaten) && $g->kabupaten_kota !== $filterKabupaten) continue;
            if (!empty($filterKecamatan) && $g->kecamatan !== $filterKecamatan) continue;
            if (!empty($filterKelurahan) && $g->kelurahan !== $filterKelurahan) continue;

            $jadwal = $modelJadwal->getByGereja($g->id);

            if (!empty($filterJamDari)) {
                $match = false;
                foreach ($jadwal as $j) {
                    if ($j->waktu_mulai >= $filterJamDari) {
                        $match = true;
                        break;
                    }
                }
                if (!$match) continue;
            }

            $gerejaList[] = $g;
            $allJadwal[$g->id] = $jadwal;

            $fotoList = $modelFoto->getByGereja($g->id);
            $fotoUrls = array();
            foreach ($fotoList as $f) {
                $fotoUrls[] = $f->foto_url;
            }
            $allFoto[$g->id] = $fotoUrls;

            $socialList = $modelSocial->getByGereja($g->id);
            $allSocial[$g->id] = $socialList;
        }

        $data = array(
            'title' => 'Peta Gereja',
            'gerejaList' => $gerejaList,
            'allJadwal' => $allJadwal,
            'allFoto' => $allFoto,
            'allSocial' => $allSocial,
            'provinsiList' => $provinsiList,
            'kabupatenList' => $kabupatenList,
            'kecamatanList' => $kecamatanList,
            'kelurahanList' => $kelurahanList,
            'selectedProvinsi' => $filterProvinsi,
            'selectedKabupaten' => $filterKabupaten,
            'selectedKecamatan' => $filterKecamatan,
            'selectedKelurahan' => $filterKelurahan,
            'selectedJamDari' => $filterJamDari
        );

        $this->view('maps/index', $data);
    }
}
