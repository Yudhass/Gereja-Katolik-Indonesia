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
        $selectedKecamatan = isset($_GET['kecamatan']) ? sanitize($_GET['kecamatan'], 'string') : '';
        $selectedKelurahan = isset($_GET['kelurahan']) ? sanitize($_GET['kelurahan'], 'string') : '';

        $kabupatenList = array();
        if (!empty($selectedProvinsi)) {
            $kabupatenList = $modelGereja->rawQuery(
                "SELECT DISTINCT kabupaten_kota FROM gereja WHERE provinsi = ? ORDER BY kabupaten_kota",
                array($selectedProvinsi)
            );
        }

        $kecamatanList = array();
        if (!empty($selectedKabupaten)) {
            $kecamatanList = $modelGereja->rawQuery(
                "SELECT DISTINCT kecamatan FROM gereja WHERE provinsi = ? AND kabupaten_kota = ? AND kecamatan IS NOT NULL AND kecamatan != '' ORDER BY kecamatan",
                array($selectedProvinsi, $selectedKabupaten)
            );
        }

        $kelurahanList = array();
        if (!empty($selectedKecamatan)) {
            $kelurahanList = $modelGereja->rawQuery(
                "SELECT DISTINCT kelurahan FROM gereja WHERE provinsi = ? AND kabupaten_kota = ? AND kecamatan = ? AND kelurahan IS NOT NULL AND kelurahan != '' ORDER BY kelurahan",
                array($selectedProvinsi, $selectedKabupaten, $selectedKecamatan)
            );
        }

        $selectedTglDari = isset($_GET['tgl_dari']) ? sanitize($_GET['tgl_dari'], 'string') : '';
        $selectedTglSampai = isset($_GET['tgl_sampai']) ? sanitize($_GET['tgl_sampai'], 'string') : '';
        $selectedJamDari = isset($_GET['jam_dari']) ? sanitize($_GET['jam_dari'], 'string') : '';
        $selectedJamSampai = isset($_GET['jam_sampai']) ? sanitize($_GET['jam_sampai'], 'string') : '';

        $sql = "SELECT * FROM gereja WHERE 1=1";
        $params = array();
        if (!empty($selectedProvinsi)) {
            $sql .= " AND provinsi = :prov";
            $params['prov'] = $selectedProvinsi;
        }
        if (!empty($selectedKabupaten)) {
            $sql .= " AND kabupaten_kota = :kab";
            $params['kab'] = $selectedKabupaten;
        }
        if (!empty($selectedKecamatan)) {
            $sql .= " AND kecamatan = :kec";
            $params['kec'] = $selectedKecamatan;
        }
        if (!empty($selectedKelurahan)) {
            $sql .= " AND kelurahan = :kel";
            $params['kel'] = $selectedKelurahan;
        }
        $sql .= " ORDER BY nama_gereja";
        $gerejaList = $modelGereja->rawQuery($sql, $params);

        $hasJadwalFilter = !empty($selectedTglDari) || !empty($selectedTglSampai) || !empty($selectedJamDari) || !empty($selectedJamSampai);
        $jadwalByGereja = array();
        if ($hasJadwalFilter) {
            $modelJadwal = new ModelJadwalMisa();
            $filtered = array();
            foreach ($gerejaList as $g) {
                $jadwalList = $modelJadwal->getByGereja($g->id);
                $matched = array();
                foreach ($jadwalList as $j) {
                    if (!empty($selectedTglDari) && !empty($j->tanggal) && $j->tanggal < $selectedTglDari) continue;
                    if (!empty($selectedTglSampai) && !empty($j->tanggal) && $j->tanggal > $selectedTglSampai) continue;
                    if (!empty($selectedJamDari) && $j->waktu_mulai < $selectedJamDari) continue;
                    if (!empty($selectedJamSampai) && $j->waktu_mulai > $selectedJamSampai) continue;
                    $matched[] = $j;
                }
                if (!empty($matched)) {
                    $jadwalByGereja[$g->id] = $matched;
                    $filtered[] = $g;
                }
            }
            $gerejaList = $filtered;
        }

        $totalAll = count($gerejaList);
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? max(1, min(50, (int)$_GET['limit'])) : 10;
        $totalPages = max(1, ceil($totalAll / $limit));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $limit;
        $gerejaPage = array_slice($gerejaList, $offset, $limit);

        $fotoByGereja = array();
        if (!empty($gerejaPage)) {
            $ids = array();
            foreach ($gerejaPage as $g) { $ids[] = $g->id; }
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
            'gerejaList' => $gerejaPage,
            'totalGereja' => $totalAll,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit,
            'provinsiList' => $provinsiList,
            'kabupatenList' => $kabupatenList,
            'kecamatanList' => $kecamatanList,
            'kelurahanList' => $kelurahanList,
            'selectedProvinsi' => $selectedProvinsi,
            'selectedKabupaten' => $selectedKabupaten,
            'selectedKecamatan' => $selectedKecamatan,
            'selectedKelurahan' => $selectedKelurahan,
            'selectedTglDari' => $selectedTglDari,
            'selectedTglSampai' => $selectedTglSampai,
            'selectedJamDari' => $selectedJamDari,
            'selectedJamSampai' => $selectedJamSampai,
            'jadwalByGereja' => $jadwalByGereja,
            'hasJadwalFilter' => $hasJadwalFilter,
            'fotoByGereja' => $fotoByGereja
        );

        $this->view('home/index', $data);
    }
}
