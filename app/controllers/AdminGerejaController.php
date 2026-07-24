<?php
require_once dirname(__FILE__) . '/../models/ModelGereja.php';
require_once dirname(__FILE__) . '/../models/ModelGerejaFoto.php';
require_once dirname(__FILE__) . '/../models/ModelGerejaSocialMedia.php';

class AdminGerejaController extends Controller
{
    public function get($id = null)
    {
        $model = new ModelGereja();
        $modelFoto = new ModelGerejaFoto();
        $modelSocial = new ModelGerejaSocialMedia();

        $data = $model->find($id);
        if ($data) {
            $data->foto_list = $modelFoto->getByGereja($id);
            $data->social_media_list = $modelSocial->getByGereja($id);
            return jsonResponse(200, 'Data gereja', $data);
        } else {
            return jsonResponse(404, 'Gereja tidak ditemukan');
        }
    }

    public function index()
    {
        $model = new ModelGereja();
        $modelFoto = new ModelGerejaFoto();
        $modelSocial = new ModelGerejaSocialMedia();

        $filterSearch = isset($_GET['q']) ? sanitize($_GET['q'], 'string') : '';
        $filterProvinsi = isset($_GET['provinsi']) ? sanitize($_GET['provinsi'], 'string') : '';

        $sql = "SELECT * FROM gereja WHERE 1=1";
        $params = array();

        if (!empty($filterSearch)) {
            $sql .= " AND (nama_gereja LIKE :q OR alamat LIKE :q2 OR provinsi LIKE :q3 OR kabupaten_kota LIKE :q4 OR kontak_telepon LIKE :q5)";
            $like = "%{$filterSearch}%";
            $params['q'] = $like;
            $params['q2'] = $like;
            $params['q3'] = $like;
            $params['q4'] = $like;
            $params['q5'] = $like;
        }

        if (!empty($filterProvinsi)) {
            $sql .= " AND provinsi = :provinsi";
            $params['provinsi'] = $filterProvinsi;
        }

        $sql .= " ORDER BY nama_gereja";

        $gerejaList = $model->rawQuery($sql, $params);
        foreach ($gerejaList as $g) {
            $g->foto_list = $modelFoto->getByGereja($g->id);
            $g->social_media_list = $modelSocial->getByGereja($g->id);
        }

        $provinces = $model->rawQuery('SELECT id, name FROM provinces ORDER BY name');

        $data = array(
            'title' => 'Data Gereja',
            'gerejaList' => $gerejaList,
            'provinces' => $provinces,
            'filterSearch' => $filterSearch,
            'filterProvinsi' => $filterProvinsi,
        );
        $this->view('admin/gereja/index', $data);
    }

    private function saveFotos($gerejaId)
    {
        $modelFoto = new ModelGerejaFoto();
        $fotoUrls = isset($_POST['foto_urls']) ? $_POST['foto_urls'] : array();
        $fotoKets = isset($_POST['foto_keterangan']) ? $_POST['foto_keterangan'] : array();

        if (!is_array($fotoUrls) || empty($fotoUrls)) {
            return;
        }

        $modelFoto->deleteByGereja($gerejaId);

        $urutan = 0;
        foreach ($fotoUrls as $i => $url) {
            $url = trim($url);
            if (empty($url)) continue;
            $modelFoto->insert(array(
                'gereja_id' => $gerejaId,
                'foto_url' => $url,
                'keterangan' => isset($fotoKets[$i]) ? sanitize($fotoKets[$i], 'string') : '',
                'urutan' => $urutan
            ));
            $urutan++;
        }
    }

    private function saveSocialMedia($gerejaId)
    {
        $modelSocial = new ModelGerejaSocialMedia();
        $platforms = isset($_POST['sosmed_platform']) ? $_POST['sosmed_platform'] : array();
        $urls = isset($_POST['sosmed_url']) ? $_POST['sosmed_url'] : array();

        $modelSocial->deleteByGereja($gerejaId);

        if (!is_array($platforms) || !is_array($urls)) return;

        $urutan = 0;
        foreach ($platforms as $i => $platform) {
            $platform = trim($platform);
            $url = isset($urls[$i]) ? trim($urls[$i]) : '';
            if (empty($platform) || empty($url)) continue;
            $modelSocial->insert(array(
                'gereja_id' => $gerejaId,
                'platform' => sanitize($platform, 'string'),
                'url' => sanitize($url, 'url'),
                'urutan' => $urutan
            ));
            $urutan++;
        }
    }

    public function add()
    {
        if (CSRF_ENABLED && !verify_csrf()) {
            $this->redirectBack('Token CSRF tidak valid.', 'error');
            return;
        }

        $model = new ModelGereja();
        $namaGereja = isset($_POST['nama_gereja']) ? sanitize($_POST['nama_gereja'], 'string') : '';

        $existing = $model->findByName($namaGereja);
        if ($existing) {
            $this->redirectBack('Data sudah ada di database.', 'error');
            return;
        }

        $slug = generateSlug($namaGereja);
        $data = array(
            'slug' => $slug,
            'nama_gereja' => $namaGereja,
            'alamat' => isset($_POST['alamat']) ? sanitize($_POST['alamat'], 'string') : '',
            'provinsi' => isset($_POST['provinsi']) ? sanitize($_POST['provinsi'], 'string') : '',
            'kabupaten_kota' => isset($_POST['kabupaten_kota']) ? sanitize($_POST['kabupaten_kota'], 'string') : '',
            'kecamatan' => isset($_POST['kecamatan']) ? sanitize($_POST['kecamatan'], 'string') : '',
            'kelurahan' => isset($_POST['kelurahan']) ? sanitize($_POST['kelurahan'], 'string') : '',
            'link_maps' => isset($_POST['link_maps']) ? sanitize($_POST['link_maps'], 'url') : '',
            'latitude' => isset($_POST['latitude']) ? (float)$_POST['latitude'] : 0,
            'longitude' => isset($_POST['longitude']) ? (float)$_POST['longitude'] : 0,
            'kontak_telepon' => isset($_POST['kontak_telepon']) ? sanitize($_POST['kontak_telepon'], 'string') : '',
            'deskripsi' => isset($_POST['deskripsi']) ? sanitize($_POST['deskripsi'], 'string') : '',
        );

        $result = $model->insert($data);
        if ($result) {
            $this->saveFotos($result->id);
            $this->saveSocialMedia($result->id);
            $this->redirect('admin/gereja', 'Gereja berhasil ditambahkan.', 'success');
        } else {
            $this->redirectBack('Gagal menambahkan gereja.', 'error');
        }
    }

    public function update($id = null)
    {
        if (CSRF_ENABLED && !verify_csrf()) {
            $this->redirectBack('Token CSRF tidak valid.', 'error');
            return;
        }

        $model = new ModelGereja();
        $namaGereja = isset($_POST['nama_gereja']) ? sanitize($_POST['nama_gereja'], 'string') : '';

        $existing = $model->findByName($namaGereja, $id);
        if ($existing) {
            $this->redirectBack('Data sudah ada di database.', 'error');
            return;
        }

        $slug = generateSlug($namaGereja);
        $data = array(
            'id' => $id,
            'slug' => $slug,
            'nama_gereja' => $namaGereja,
            'alamat' => isset($_POST['alamat']) ? sanitize($_POST['alamat'], 'string') : '',
            'provinsi' => isset($_POST['provinsi']) ? sanitize($_POST['provinsi'], 'string') : '',
            'kabupaten_kota' => isset($_POST['kabupaten_kota']) ? sanitize($_POST['kabupaten_kota'], 'string') : '',
            'kecamatan' => isset($_POST['kecamatan']) ? sanitize($_POST['kecamatan'], 'string') : '',
            'kelurahan' => isset($_POST['kelurahan']) ? sanitize($_POST['kelurahan'], 'string') : '',
            'link_maps' => isset($_POST['link_maps']) ? sanitize($_POST['link_maps'], 'url') : '',
            'latitude' => isset($_POST['latitude']) ? (float)$_POST['latitude'] : 0,
            'longitude' => isset($_POST['longitude']) ? (float)$_POST['longitude'] : 0,
            'kontak_telepon' => isset($_POST['kontak_telepon']) ? sanitize($_POST['kontak_telepon'], 'string') : '',
            'deskripsi' => isset($_POST['deskripsi']) ? sanitize($_POST['deskripsi'], 'string') : '',
        );

        $result = $model->update($data, $id);
        if ($result !== false) {
            $this->saveFotos($id);
            $this->saveSocialMedia($id);
            $this->redirect('admin/gereja', 'Gereja berhasil diperbarui.', 'success');
        } else {
            $this->redirectBack('Gagal memperbarui gereja.', 'error');
        }
    }

    public function delete($id = null)
    {
        if (CSRF_ENABLED && !verify_csrf()) {
            return jsonResponse(403, 'Token CSRF tidak valid');
        }

        $modelFoto = new ModelGerejaFoto();
        $modelFoto->deleteByGereja($id);

        $modelSocial = new ModelGerejaSocialMedia();
        $modelSocial->deleteByGereja($id);

        $model = new ModelGereja();
        if ($model->delete($id)) {
            return jsonResponse(200, 'Gereja berhasil dihapus');
        } else {
            return jsonResponse(500, 'Gagal menghapus gereja');
        }
    }

    public function getRegencies($provinceId = null)
    {
        $model = new ModelGereja();
        $data = $model->rawQuery('SELECT id, name FROM regencies WHERE province_id = ? ORDER BY name', array($provinceId));
        return jsonResponse(200, 'OK', $data);
    }

    public function getDistricts($regencyId = null)
    {
        $model = new ModelGereja();
        $data = $model->rawQuery('SELECT id, name FROM districts WHERE regency_id = ? ORDER BY name', array($regencyId));
        return jsonResponse(200, 'OK', $data);
    }

    public function getVillages($districtId = null)
    {
        $model = new ModelGereja();
        $data = $model->rawQuery('SELECT id, name FROM villages WHERE district_id = ? ORDER BY name', array($districtId));
        return jsonResponse(200, 'OK', $data);
    }
}
