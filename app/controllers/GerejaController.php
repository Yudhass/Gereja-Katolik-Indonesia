<?php
require_once dirname(__FILE__) . '/../models/ModelGereja.php';
require_once dirname(__FILE__) . '/../models/ModelGerejaFoto.php';
require_once dirname(__FILE__) . '/../models/ModelGerejaSocialMedia.php';
require_once dirname(__FILE__) . '/../models/ModelJadwalMisa.php';

class GerejaController extends Controller
{
    public function detail($slug = null)
    {
        $modelGereja = new ModelGereja();
        $modelJadwal = new ModelJadwalMisa();
        $modelFoto = new ModelGerejaFoto();
        $modelSocial = new ModelGerejaSocialMedia();

        $gereja = $modelGereja->findBySlug($slug);
        if (!$gereja) {
            Show404('Gereja tidak ditemukan');
            return;
        }

        $jadwalList = $modelJadwal->getByGereja($gereja->id);
        $fotoList = $modelFoto->getByGereja($gereja->id);
        $socialList = $modelSocial->getByGereja($gereja->id);

        $data = array(
            'title' => $gereja->nama_gereja,
            'gereja' => $gereja,
            'jadwalList' => $jadwalList,
            'fotoList' => $fotoList,
            'socialList' => $socialList
        );

        $this->view('gereja/detail', $data);
    }
}
