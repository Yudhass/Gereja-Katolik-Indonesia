<?php

function _slugify($s) {
    $s = strtolower(trim($s));
    $s = str_replace(array("'", '"', '&'), '', $s);
    $s = preg_replace('/[^a-z0-9\s-]/', '', $s);
    $s = preg_replace('/[\s-]+/', '-', $s);
    return trim($s, '-');
}

function run_gereja_seeder() {
    require_once dirname(__FILE__) . '/../../core/Database.php';
    $db = Database::getInstance();

    $data = array(
        array(
            'nama_gereja' => 'Gereja Katedral Jakarta',
            'alamat' => 'Jl. Katedral No.7B, Ps. Baru, Kecamatan Sawah Besar, Kota Jakarta Pusat, Daerah Khusus Ibukota Jakarta 10710',
            'provinsi' => 'DKI Jakarta',
            'kabupaten_kota' => 'Jakarta Pusat',
            'latitude' => -6.16815400,
            'longitude' => 106.83320600,
            'kontak_telepon' => '(021) 3855581',
            'deskripsi' => 'Gereja Katedral Jakarta atau resminya Gereja Santa Maria Pelindung Diangkat ke Surga adalah sebuah gereja Katolik yang menjadi pusat Keuskupan Agung Jakarta.',
            'foto_url' => 'https://images.unsplash.com/photo-1740439803003-985158f43b70?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
        ),
        array(
            'nama_gereja' => 'Gereja Santa Maria Regina, Bekasi',
            'alamat' => 'Jl. Raya Perjuangan, Margamulya, Kec. Bekasi Utara, Kota Bekasi, Jawa Barat 17143',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Bekasi',
            'latitude' => -6.22140900,
            'longitude' => 107.00970400,
            'kontak_telepon' => '(021) 88861717',
            'deskripsi' => 'Gereja Katolik Santa Maria Regina adalah sebuah gereja paroki di Bekasi yang melayani umat Katolik di wilayah Bekasi Utara.',
            'foto_url' => ''
        ),
        array(
            'nama_gereja' => 'Gereja Santo Fransiskus Xaverius, Jakarta',
            'alamat' => 'Jl. Kramat Raya No.67, Kramat, Kec. Senen, Kota Jakarta Pusat, Daerah Khusus Ibukota Jakarta 10450',
            'provinsi' => 'DKI Jakarta',
            'kabupaten_kota' => 'Jakarta Pusat',
            'latitude' => -6.18769700,
            'longitude' => 106.84313900,
            'kontak_telepon' => '(021) 31936758',
            'deskripsi' => 'Gereja Santo Fransiskus Xaverius terkenal dengan sebutan Gereja Kramat, merupakan gereja Katolik yang bersejarah di Jakarta.',
            'foto_url' => ''
        ),
        array(
            'nama_gereja' => 'Gereja Santo Yakobus, Surabaya',
            'alamat' => 'Jl. Jemursari Raya No.1, Jemur Wonosari, Kec. Wonocolo, Kota Surabaya, Jawa Timur 60237',
            'provinsi' => 'Jawa Timur',
            'kabupaten_kota' => 'Surabaya',
            'latitude' => -7.33061600,
            'longitude' => 112.73519100,
            'kontak_telepon' => '(031) 8419222',
            'deskripsi' => 'Gereja Katolik Santo Yakobus adalah paroki di Surabaya selatan yang melayani ribuan umat dengan berbagai kegiatan gerejawi.',
            'foto_url' => ''
        ),
        array(
            'nama_gereja' => 'Gereja Santo Petrus, Bandung',
            'alamat' => 'Jl. Merdeka No.12, Babakan Ciamis, Kec. Sumur Bandung, Kota Bandung, Jawa Barat 40117',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Bandung',
            'latitude' => -6.91375100,
            'longitude' => 107.60962000,
            'kontak_telepon' => '(022) 4207475',
            'deskripsi' => 'Gereja Santo Petrus adalah gereja Katolik yang terletak di pusat Kota Bandung, menjadi landmark dan pusat kegiatan rohani umat Katolik Bandung.',
            'foto_url' => ''
        ),
    );

    $existingSlugs = array();
    foreach ($data as $item) {
        if (!isset($item['slug']) || empty($item['slug'])) {
            $base = _slugify($item['nama_gereja']);
            $slug = $base;
            $i = 1;
            while (in_array($slug, $existingSlugs)) {
                $slug = $base . '-' . $i;
                $i++;
            }
            $item['slug'] = $slug;
        }
        $existingSlugs[] = $item['slug'];
        $columns = implode(', ', array_keys($item));
        $placeholders = ':' . implode(', :', array_keys($item));
        $sql = "INSERT INTO gereja ({$columns}) VALUES ({$placeholders})";
        $stmt = $db->prepare($sql);
        $stmt->execute($item);
    }

    return count($data);
}

return 'run_gereja_seeder';
