<?php

function str_getcsv_compat($input, $delimiter = ';')
{
    $fh = fopen('php://temp', 'r+');
    fwrite($fh, $input);
    rewind($fh);
    $result = fgetcsv($fh, 0, $delimiter);
    fclose($fh);
    return $result;
}

function run_wilayah_seeder()
{
    require_once dirname(__FILE__) . '/../../core/Database.php';
    $db = Database::getInstance();
    $dataDir = dirname(__FILE__) . '/../data';

    // Urutan baru -> lama. Data terbaru dimasukkan dulu jadi prioritas,
    // data lama hanya menambah yang belum ada (duplikat by primary key di-skip).
    $yearDirs = array(
        'data_wilayah_2026',
        'data_wilayah_2023',
        'data_wilayah_2022',
        'data_wilayah_2020',
    );

    // Urutan tabel penting untuk foreign key: parent dulu, child belakangan.
    $tables = array(
        'provinces' => array('file' => 'provinces.csv', 'fields' => array('id', 'name')),
        'regencies' => array('file' => 'regencies.csv', 'fields' => array('id', 'province_id', 'name')),
        'districts' => array('file' => 'districts.csv', 'fields' => array('id', 'regency_id', 'name')),
        'villages'  => array('file' => 'villages.csv',  'fields' => array('id', 'district_id', 'name')),
    );

    $totalInserted = 0;
    $totalSkipped = 0;

    foreach ($tables as $table => $cfg) {
        $fields = $cfg['fields'];
        $columns = implode(', ', array_map(function ($f) { return "`{$f}`"; }, $fields));
        $placeholders = ':' . implode(', :', $fields);

        // INSERT IGNORE: kalau id (primary key) sudah ada -> di-skip, tidak error, tidak duplikat.
        $sql = "INSERT IGNORE INTO `{$table}` ({$columns}) VALUES ({$placeholders})";
        $stmt = $db->prepare($sql);

        $tableInserted = 0;
        $tableSkipped = 0;

        // Loop per tahun dari yang paling baru ke paling lama.
        foreach ($yearDirs as $yearDir) {
            $file = $dataDir . '/' . $yearDir . '/' . $cfg['file'];
            if (!file_exists($file)) {
                echo "  {$yearDir}/{$cfg['file']}: file tidak ditemukan, dilewati\n";
                continue;
            }

            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (!$lines || count($lines) < 2) {
                echo "  {$yearDir}/{$cfg['file']}: file kosong\n";
                continue;
            }
            array_shift($lines); // buang header
            $totalRows = count($lines);

            $batchSize = 1000;
            $fileInserted = 0;
            $fileSkipped = 0;
            $db->beginTransaction();

            try {
                foreach ($lines as $i => $line) {
                    $values = str_getcsv_compat($line, ';');
                    if ($values === false || count($values) < count($fields)) {
                        $fileSkipped++;
                        continue;
                    }
                    $data = array();
                    foreach ($fields as $j => $field) {
                        $data[$field] = isset($values[$j]) ? trim($values[$j]) : '';
                    }
                    // Skip baris dengan id kosong
                    if ($data[$fields[0]] === '') {
                        $fileSkipped++;
                        continue;
                    }
                    $stmt->execute($data);
                    // rowCount() == 1 berarti baris baru masuk, == 0 berarti duplikat -> di-skip
                    if ($stmt->rowCount() > 0) {
                        $fileInserted++;
                    } else {
                        $fileSkipped++;
                    }

                    if (($i + 1) % $batchSize === 0) {
                        $db->commit();
                        $db->beginTransaction();
                    }
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                echo "  Error {$yearDir}/{$cfg['file']}: " . $e->getMessage() . "\n";
                continue;
            }

            echo "  {$yearDir}/{$cfg['file']}: +{$fileInserted} baru, {$fileSkipped} duplikat di-skip (dari {$totalRows})\n";
            $tableInserted += $fileInserted;
            $tableSkipped += $fileSkipped;
        }

        echo "  => {$table}: total +{$tableInserted} baru, {$tableSkipped} duplikat di-skip\n";
        $totalInserted += $tableInserted;
        $totalSkipped += $tableSkipped;
    }

    echo "  Wilayah: {$totalInserted} baru dimasukkan, {$totalSkipped} duplikat di-skip\n";

    return $totalInserted;
}

return 'run_wilayah_seeder';
