<?php
require_once dirname(__FILE__) . '/../models/ModelGereja.php';
require_once dirname(__FILE__) . '/../models/ModelJadwalMisa.php';

class HomeController extends Controller
{
    public function index()
    {
        $modelGereja = new ModelGereja();

        $provinsiList = $modelGereja->rawQuery("SELECT DISTINCT provinsi FROM gereja ORDER BY provinsi");
        $selectedProvinsi = isset($_GET['provinsi']) ? sanitize($_GET['provinsi'], 'string') : '';
        $selectedKabupaten = isset($_GET['kabupaten']) ? sanitize($_GET['kabupaten'], 'string') : '';

        $kabupatenList = array();
        if (!empty($selectedProvinsi)) {
            $kabupatenList = $modelGereja->rawQuery(
                "SELECT DISTINCT kabupaten_kota FROM gereja WHERE provinsi = ? ORDER BY kabupaten_kota",
                array($selectedProvinsi)
            );
        }

        $gerejaList = $modelGereja->all();
        if (!empty($selectedProvinsi)) {
            $filtered = array();
            foreach ($gerejaList as $g) {
                if ($g->provinsi !== $selectedProvinsi) continue;
                if (!empty($selectedKabupaten) && $g->kabupaten_kota !== $selectedKabupaten) continue;
                $filtered[] = $g;
            }
            $gerejaList = $filtered;
        }

        $fotoByGereja = array();
        if (!empty($gerejaList)) {
            $ids = array();
            foreach ($gerejaList as $g) { $ids[] = $g->id; }
            $fotoRows = $modelGereja->rawQuery(
                "SELECT gereja_id, foto_url FROM gereja_foto WHERE gereja_id IN (" . implode(',', $ids) . ") ORDER BY urutan ASC, id ASC"
            );
            foreach ($fotoRows as $f) {
                if (!isset($fotoByGereja[$f->gereja_id])) {
                    $fotoByGereja[$f->gereja_id] = $f->foto_url;
                }
            }
        }

        $data = array(
            'title' => 'Beranda',
            'gerejaList' => $gerejaList,
            'totalGereja' => count($gerejaList),
            'provinsiList' => $provinsiList,
            'kabupatenList' => $kabupatenList,
            'selectedProvinsi' => $selectedProvinsi,
            'selectedKabupaten' => $selectedKabupaten,
            'fotoByGereja' => $fotoByGereja
        );

        $this->view('home/index', $data);
    }
}
