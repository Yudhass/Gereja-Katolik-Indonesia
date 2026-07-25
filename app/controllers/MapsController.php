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
        $filterNamaGereja = isset($_GET['nama_gereja']) ? sanitize($_GET['nama_gereja'], 'string') : '';

        $provinsiRaw = $modelGereja->rawQuery('SELECT name FROM provinces ORDER BY name');
        $provinsiList = array();
        foreach ($provinsiRaw as $p) {
            $provinsiList[] = $p->name;
        }

        $allGereja = $modelGereja->all();

        $gerejaList = array();
        $allJadwal = array();
        $allFoto = array();
        $allSocial = array();

        foreach ($allGereja as $g) {
            if (empty($g->latitude) || empty($g->longitude)) continue;

            $gerejaList[] = $g;
            $allJadwal[$g->id] = $modelJadwal->getByGereja($g->id);

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
            'selectedProvinsi' => $filterProvinsi,
            'selectedKabupaten' => $filterKabupaten,
            'selectedKecamatan' => $filterKecamatan,
            'selectedKelurahan' => $filterKelurahan,
            'selectedJamDari' => $filterJamDari,
            'selectedNamaGereja' => $filterNamaGereja
        );

        $this->view('maps/index', $data);
    }

    public function getKabupaten()
    {
        $provinsi = isset($_GET['provinsi']) ? sanitize($_GET['provinsi'], 'string') : '';
        if (empty($provinsi)) {
            jsonResponse(400, 'Provinsi diperlukan');
            return;
        }
        $model = new ModelGereja();
        $data = $model->rawQuery(
            'SELECT DISTINCT kabupaten_kota AS name FROM gereja WHERE provinsi = ? AND kabupaten_kota != "" ORDER BY kabupaten_kota',
            array($provinsi)
        );
        jsonResponse(200, 'OK', $data);
    }

    public function getKecamatan()
    {
        $provinsi = isset($_GET['provinsi']) ? sanitize($_GET['provinsi'], 'string') : '';
        $kabupaten = isset($_GET['kabupaten']) ? sanitize($_GET['kabupaten'], 'string') : '';
        if (empty($provinsi) || empty($kabupaten)) {
            jsonResponse(400, 'Provinsi dan Kabupaten diperlukan');
            return;
        }
        $model = new ModelGereja();
        $data = $model->rawQuery(
            'SELECT DISTINCT kecamatan AS name FROM gereja WHERE provinsi = ? AND kabupaten_kota = ? AND kecamatan != "" ORDER BY kecamatan',
            array($provinsi, $kabupaten)
        );
        jsonResponse(200, 'OK', $data);
    }

    public function getKelurahan()
    {
        $provinsi = isset($_GET['provinsi']) ? sanitize($_GET['provinsi'], 'string') : '';
        $kabupaten = isset($_GET['kabupaten']) ? sanitize($_GET['kabupaten'], 'string') : '';
        $kecamatan = isset($_GET['kecamatan']) ? sanitize($_GET['kecamatan'], 'string') : '';
        if (empty($provinsi) || empty($kabupaten) || empty($kecamatan)) {
            jsonResponse(400, 'Provinsi, Kabupaten, dan Kecamatan diperlukan');
            return;
        }
        $model = new ModelGereja();
        $data = $model->rawQuery(
            'SELECT DISTINCT kelurahan AS name FROM gereja WHERE provinsi = ? AND kabupaten_kota = ? AND kecamatan = ? AND kelurahan != "" ORDER BY kelurahan',
            array($provinsi, $kabupaten, $kecamatan)
        );
        jsonResponse(200, 'OK', $data);
    }
}
