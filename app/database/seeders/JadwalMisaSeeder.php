<?php

function run_jadwalmisa_seeder() {
    require_once dirname(__FILE__) . '/../../core/Database.php';
    $db = Database::getInstance();

    $data = array(
        array('gereja_id' => 1, 'hari' => 'Senin', 'waktu_mulai' => '06:30:00', 'kategori' => 'Harian', 'keterangan' => 'Misa Harian Pagi'),
        array('gereja_id' => 1, 'hari' => 'Selasa', 'waktu_mulai' => '06:30:00', 'kategori' => 'Harian', 'keterangan' => 'Misa Harian Pagi'),
        array('gereja_id' => 1, 'hari' => 'Rabu', 'waktu_mulai' => '06:30:00', 'kategori' => 'Harian', 'keterangan' => 'Misa Harian Pagi'),
        array('gereja_id' => 1, 'hari' => 'Kamis', 'waktu_mulai' => '06:30:00', 'kategori' => 'Harian', 'keterangan' => 'Misa Harian Pagi'),
        array('gereja_id' => 1, 'hari' => 'Jumat', 'waktu_mulai' => '06:30:00', 'kategori' => 'Harian', 'keterangan' => 'Misa Harian Pagi'),
        array('gereja_id' => 1, 'hari' => 'Sabtu', 'waktu_mulai' => '17:00:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Sabtu Sore'),
        array('gereja_id' => 1, 'hari' => 'Minggu', 'waktu_mulai' => '07:00:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Minggu Pagi'),
        array('gereja_id' => 1, 'hari' => 'Minggu', 'waktu_mulai' => '09:00:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Minggu Anak'),
        array('gereja_id' => 1, 'hari' => 'Minggu', 'waktu_mulai' => '11:00:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Minggu Siang'),
        array('gereja_id' => 1, 'hari' => 'Minggu', 'waktu_mulai' => '17:00:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Minggu Sore'),
        array('gereja_id' => 1, 'hari' => 'Spesial', 'tanggal' => '2026-03-29', 'waktu_mulai' => '19:00:00', 'kategori' => 'Hari Raya', 'keterangan' => 'Misa Malam Paskah'),
        array('gereja_id' => 1, 'hari' => 'Spesial', 'tanggal' => '2026-12-24', 'waktu_mulai' => '19:00:00', 'kategori' => 'Hari Raya', 'keterangan' => 'Misa Malam Natal'),
        array('gereja_id' => 2, 'hari' => 'Minggu', 'waktu_mulai' => '07:00:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Minggu Pagi'),
        array('gereja_id' => 2, 'hari' => 'Minggu', 'waktu_mulai' => '09:00:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Minggu Siang'),
        array('gereja_id' => 2, 'hari' => 'Minggu', 'waktu_mulai' => '17:00:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Minggu Sore'),
        array('gereja_id' => 2, 'hari' => 'Sabtu', 'waktu_mulai' => '17:00:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Sabtu Sore'),
        array('gereja_id' => 2, 'hari' => 'Selasa', 'waktu_mulai' => '18:00:00', 'kategori' => 'Harian', 'keterangan' => 'Misa Harian Sore'),
        array('gereja_id' => 2, 'hari' => 'Kamis', 'waktu_mulai' => '18:00:00', 'kategori' => 'Harian', 'keterangan' => 'Misa Harian Sore'),
        array('gereja_id' => 3, 'hari' => 'Minggu', 'waktu_mulai' => '06:00:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Minggu Pagi'),
        array('gereja_id' => 3, 'hari' => 'Minggu', 'waktu_mulai' => '08:00:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Minggu'),
        array('gereja_id' => 3, 'hari' => 'Minggu', 'waktu_mulai' => '10:00:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Minggu Bahasa Inggris'),
        array('gereja_id' => 3, 'hari' => 'Minggu', 'waktu_mulai' => '17:00:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Minggu Sore'),
        array('gereja_id' => 3, 'hari' => 'Sabtu', 'waktu_mulai' => '17:00:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Sabtu Sore'),
        array('gereja_id' => 3, 'hari' => 'Senin', 'waktu_mulai' => '06:30:00', 'kategori' => 'Harian', 'keterangan' => 'Misa Harian'),
        array('gereja_id' => 3, 'hari' => 'Rabu', 'waktu_mulai' => '06:30:00', 'kategori' => 'Harian', 'keterangan' => 'Misa Harian'),
        array('gereja_id' => 3, 'hari' => 'Jumat', 'waktu_mulai' => '06:30:00', 'kategori' => 'Harian', 'keterangan' => 'Misa Harian'),
        array('gereja_id' => 4, 'hari' => 'Minggu', 'waktu_mulai' => '06:30:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Minggu Pagi'),
        array('gereja_id' => 4, 'hari' => 'Minggu', 'waktu_mulai' => '08:30:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Minggu'),
        array('gereja_id' => 4, 'hari' => 'Minggu', 'waktu_mulai' => '17:00:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Minggu Sore'),
        array('gereja_id' => 4, 'hari' => 'Sabtu', 'waktu_mulai' => '16:30:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Sabtu Sore'),
        array('gereja_id' => 4, 'hari' => 'Rabu', 'waktu_mulai' => '18:00:00', 'kategori' => 'Harian', 'keterangan' => 'Misa Harian Sore'),
        array('gereja_id' => 4, 'hari' => 'Jumat', 'waktu_mulai' => '18:00:00', 'kategori' => 'Harian', 'keterangan' => 'Misa Harian Sore'),
        array('gereja_id' => 5, 'hari' => 'Minggu', 'waktu_mulai' => '06:30:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Minggu Pagi'),
        array('gereja_id' => 5, 'hari' => 'Minggu', 'waktu_mulai' => '09:00:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Minggu'),
        array('gereja_id' => 5, 'hari' => 'Minggu', 'waktu_mulai' => '17:00:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Minggu Sore'),
        array('gereja_id' => 5, 'hari' => 'Sabtu', 'waktu_mulai' => '17:00:00', 'kategori' => 'Mingguan', 'keterangan' => 'Misa Sabtu Sore'),
        array('gereja_id' => 5, 'hari' => 'Senin', 'waktu_mulai' => '05:30:00', 'kategori' => 'Harian', 'keterangan' => 'Misa Harian Pagi'),
        array('gereja_id' => 5, 'hari' => 'Selasa', 'waktu_mulai' => '05:30:00', 'kategori' => 'Harian', 'keterangan' => 'Misa Harian Pagi'),
        array('gereja_id' => 5, 'hari' => 'Rabu', 'waktu_mulai' => '05:30:00', 'kategori' => 'Harian', 'keterangan' => 'Misa Harian Pagi'),
        array('gereja_id' => 5, 'hari' => 'Kamis', 'waktu_mulai' => '05:30:00', 'kategori' => 'Harian', 'keterangan' => 'Misa Harian Pagi'),
        array('gereja_id' => 5, 'hari' => 'Jumat', 'waktu_mulai' => '05:30:00', 'kategori' => 'Harian', 'keterangan' => 'Misa Harian Pagi'),
    );

    foreach ($data as $item) {
        $columns = implode(', ', array_keys($item));
        $placeholders = ':' . implode(', :', array_keys($item));
        $sql = "INSERT INTO jadwal_misa ({$columns}) VALUES ({$placeholders})";
        $stmt = $db->prepare($sql);
        $stmt->execute($item);
    }

    return count($data);
}

return 'run_jadwalmisa_seeder';
