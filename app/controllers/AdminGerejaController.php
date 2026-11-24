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

        $filterSearch = isset($_GET['q']) ? sanitize($_GET['q'], 'string') : '';
        $filterProvinsi = isset($_GET['provinsi']) ? sanitize($_GET['provinsi'], 'string') : '';
        $totalGereja = $model->count();
        $provinces = $model->rawQuery('SELECT id, name FROM provinces ORDER BY name');

        $data = array(
            'title' => 'Data Gereja',
            'totalGereja' => $totalGereja,
            'provinces' => $provinces,
            'filterSearch' => $filterSearch,
            'filterProvinsi' => $filterProvinsi,
        );
        $this->view('admin/gereja/index', $data);
    }

    public function datatable()
    {
        $model = new ModelGereja();

        $draw = isset($_GET['draw']) ? (int)$_GET['draw'] : 1;
        $start = isset($_GET['start']) ? max(0, (int)$_GET['start']) : 0;
        $length = isset($_GET['length']) ? (int)$_GET['length'] : 10;
        if ($length < 1 || $length > 100) {
            $length = 10;
        }

        $filterSearch = isset($_GET['q']) ? trim(sanitize($_GET['q'], 'string')) : '';
        $dtSearch = (isset($_GET['search']) && is_array($_GET['search']) && isset($_GET['search']['value']))
            ? trim(sanitize($_GET['search']['value'], 'string'))
            : '';
        $searchTerm = $filterSearch !== '' ? $filterSearch : $dtSearch;
        $filterProvinsi = isset($_GET['provinsi']) ? trim(sanitize($_GET['provinsi'], 'string')) : '';

        $orderColMap = array(
            0 => 'id',
            1 => 'nama_gereja',
            2 => 'provinsi',
            3 => 'kontak_telepon',
        );
        $orderColIdx = (isset($_GET['order'][0]['column'])) ? (int)$_GET['order'][0]['column'] : 1;
        $orderCol = isset($orderColMap[$orderColIdx]) ? $orderColMap[$orderColIdx] : 'nama_gereja';
        $orderDirRaw = (isset($_GET['order'][0]['dir'])) ? strtoupper($_GET['order'][0]['dir']) : 'ASC';
        $orderDir = ($orderDirRaw === 'DESC') ? 'DESC' : 'ASC';

        $recordsTotal = (int)$model->count();

        $whereSql = " WHERE 1=1";
        $params = array();

        if ($searchTerm !== '') {
            $whereSql .= " AND (nama_gereja LIKE :q1 OR alamat LIKE :q2 OR provinsi LIKE :q3 OR kabupaten_kota LIKE :q4 OR kecamatan LIKE :q5 OR kelurahan LIKE :q6 OR kontak_telepon LIKE :q7)";
            $like = "%{$searchTerm}%";
            $params['q1'] = $like;
            $params['q2'] = $like;
            $params['q3'] = $like;
            $params['q4'] = $like;
            $params['q5'] = $like;
            $params['q6'] = $like;
            $params['q7'] = $like;
        }

        if ($filterProvinsi !== '') {
            $whereSql .= " AND provinsi = :provinsi";
            $params['provinsi'] = $filterProvinsi;
        }

        $countRows = $model->rawQuery("SELECT COUNT(*) AS cnt FROM gereja" . $whereSql, $params);
        $recordsFiltered = (!empty($countRows) && isset($countRows[0]->cnt)) ? (int)$countRows[0]->cnt : 0;

        $sql = "SELECT id, slug, nama_gereja, alamat, provinsi, kabupaten_kota, kecamatan, kelurahan, kontak_telepon, link_maps
                FROM gereja" . $whereSql . "
                ORDER BY {$orderCol} {$orderDir}
                LIMIT {$length} OFFSET {$start}";
        $rows = $model->rawQuery($sql, $params);

        $fotoByGereja = array();
        $socialByGereja = array();
        if (!empty($rows)) {
            $ids = array();
            foreach ($rows as $r) {
                $ids[] = (int)$r->id;
            }
            $idList = implode(',', $ids);

            $fotos = $model->rawQuery("SELECT gereja_id, foto_url, keterangan FROM gereja_foto WHERE gereja_id IN ({$idList}) ORDER BY urutan ASC, id ASC");
            foreach ($fotos as $f) {
                $gid = (int)$f->gereja_id;
                if (!isset($fotoByGereja[$gid])) $fotoByGereja[$gid] = array();
                $fotoByGereja[$gid][] = array(
                    'foto_url' => $f->foto_url,
                    'keterangan' => isset($f->keterangan) ? decode_text($f->keterangan) : ''
                );
            }

            $socials = $model->rawQuery("SELECT gereja_id, platform, url FROM gereja_social_media WHERE gereja_id IN ({$idList}) ORDER BY urutan ASC, id ASC");
            foreach ($socials as $s) {
                $gid = (int)$s->gereja_id;
                if (!isset($socialByGereja[$gid])) $socialByGereja[$gid] = array();
                $socialByGereja[$gid][] = array(
                    'platform' => $s->platform,
                    'url' => $s->url
                );
            }
        }

        $data = array();
        foreach ($rows as $idx => $g) {
            $gid = (int)$g->id;
            $data[] = array(
                'no' => $start + $idx + 1,
                'id' => $gid,
                'slug' => $g->slug,
                'nama_gereja' => decode_text($g->nama_gereja),
                'alamat' => decode_text($g->alamat),
                'provinsi' => decode_text($g->provinsi),
                'kabupaten_kota' => decode_text($g->kabupaten_kota),
                'kecamatan' => decode_text($g->kecamatan),
                'kelurahan' => decode_text($g->kelurahan),
                'kontak_telepon' => decode_text($g->kontak_telepon),
                'link_maps' => $g->link_maps,
                'foto_list' => isset($fotoByGereja[$gid]) ? $fotoByGereja[$gid] : array(),
                'social_media_list' => isset($socialByGereja[$gid]) ? $socialByGereja[$gid] : array(),
            );
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array(
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ));
        exit;
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

        $baseSlug = generateSlug($namaGereja);
        $slug = $baseSlug;
        $counter = 1;
        while ($model->findBySlug($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
            if ($counter > 100) break;
        }
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

        try {
            $result = $model->insert($data);
        } catch (Exception $e) {
            // Fallback jika masih duplicate (race condition)
            if (strpos($e->getMessage(), 'Duplicate entry') !== false || strpos($e->getMessage(), '1062') !== false) {
                $slug = $baseSlug . '-' . time();
                $data['slug'] = $slug;
                $result = $model->insert($data);
            } else {
                throw $e;
            }
        }
        if ($result) {
            $this->saveFotos($result->id);
            $this->saveSocialMedia($result->id);
            $this->redirect('admin/gereja', 'Gereja berhasil ditambahkan.', 'success');
        } else {
            $this->redirectBack('Gagal menambahkan gereja.', 'error');
        }
    }

    public function editPage($id = null)
    {
        $model = new ModelGereja();
        $modelFoto = new ModelGerejaFoto();
        $modelSocial = new ModelGerejaSocialMedia();

        $gereja = $model->find($id);
        if (!$gereja) {
            $this->redirect('admin/gereja', 'Gereja tidak ditemukan.', 'error');
            return;
        }
        $gereja->foto_list = $modelFoto->getByGereja($id);
        $gereja->social_media_list = $modelSocial->getByGereja($id);

        $provinces = $model->rawQuery('SELECT id, name FROM provinces ORDER BY name');

        $data = array(
            'title' => 'Edit Gereja',
            'gereja' => $gereja,
            'provinces' => $provinces,
            'filterSearch' => isset($_GET['q']) ? sanitize($_GET['q'], 'string') : '',
            'filterProvinsi' => isset($_GET['provinsi']) ? sanitize($_GET['provinsi'], 'string') : '',
        );
        $this->view('admin/gereja/edit', $data);
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

        $baseSlug = generateSlug($namaGereja);
        $slug = $baseSlug;
        $counter = 1;
        $existingSlug = $model->findBySlug($slug);
        while ($existingSlug && $existingSlug->id != $id) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
            if ($counter > 100) break;
            $existingSlug = $model->findBySlug($slug);
        }
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

        try {
            $result = $model->update($data, $id);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false || strpos($e->getMessage(), '1062') !== false) {
                $data['slug'] = $baseSlug . '-' . time();
                $result = $model->update($data, $id);
            } else {
                throw $e;
            }
        }
        if ($result !== false) {
            $this->saveFotos($id);
            $this->saveSocialMedia($id);
            $queryParams = array();
            $fq = isset($_POST['filter_q']) ? $_POST['filter_q'] : '';
            $fp = isset($_POST['filter_provinsi']) ? $_POST['filter_provinsi'] : '';
            if (!empty($fq)) $queryParams['q'] = $fq;
            if (!empty($fp)) $queryParams['provinsi'] = $fp;
            $queryString = !empty($queryParams) ? '?' . http_build_query($queryParams) : '';
            $this->redirect('admin/gereja' . $queryString, 'Gereja berhasil diperbarui.', 'success');
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

    public function check()
    {
        // IDENTIK dengan AdminGerejaController::add() baris 130-133:
        // $namaGereja = sanitize($_POST['nama_gereja'],'string'); $existing = $model->findByName($namaGereja);
        $namaRaw = isset($_GET['nama']) ? $_GET['nama'] : (isset($_GET['q']) ? $_GET['q'] : '');
        $namaRaw = trim($namaRaw);
        if (empty($namaRaw)) {
            return jsonResponse(400, 'Parameter nama diperlukan');
        }
        $model = new ModelGereja();
        $namaGereja = sanitize($namaRaw, 'string');
        $existing = $model->findByName($namaGereja);
        $exists = $existing ? true : false;
        return jsonResponse(200, $exists ? 'Data sudah ada di database.' : 'Data belum ada', array('exists' => $exists, 'nama' => $namaRaw, 'sanitized' => $namaGereja));
    }

    public function checkBatch()
    {
        // IDENTIK dengan add() per-item: sanitize + findByName
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        $names = array();
        if (isset($data['names']) && is_array($data['names'])) {
            $names = $data['names'];
        } elseif (isset($_POST['names']) && is_array($_POST['names'])) {
            $names = $_POST['names'];
        }
        if (empty($names)) {
            return jsonResponse(400, 'Parameter names diperlukan');
        }
        $model = new ModelGereja();
        $result = array();
        foreach ($names as $n) {
            $nTrim = trim($n);
            if ($nTrim === '') continue;
            // IDENTIK add(): sanitize + findByName
            $namaGereja = sanitize($nTrim, 'string');
            $result[$n] = $model->findByName($namaGereja) ? true : false;
        }
        return jsonResponse(200, 'OK', $result);
    }
}
