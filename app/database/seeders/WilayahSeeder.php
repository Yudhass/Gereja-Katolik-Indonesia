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
    $total = 0;

    $tables = array(
        'provinces' => array('file' => 'provinces.csv', 'fields' => array('id', 'name')),
        'regencies' => array('file' => 'regencies.csv', 'fields' => array('id', 'province_id', 'name')),
        'districts' => array('file' => 'districts.csv', 'fields' => array('id', 'regency_id', 'name')),
        'villages' => array('file' => 'villages.csv', 'fields' => array('id', 'district_id', 'name')),
    );

    foreach ($tables as $table => $cfg) {
        $file = $dataDir . '/' . $cfg['file'];
        if (!file_exists($file)) {
            echo "  File not found: {$cfg['file']}\n";
            continue;
        }

        $check = $db->query("SELECT COUNT(*) as cnt FROM `{$table}`");
        $row = $check->fetch(PDO::FETCH_OBJ);
        if ($row && $row->cnt > 0) {
            echo "  {$cfg['file']}: skipped (table already has {$row->cnt} rows)\n";
            $total += $row->cnt;
            continue;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $header = str_getcsv_compat(array_shift($lines), ';');
        $fields = $cfg['fields'];
        $totalRows = count($lines);
        $columns = implode(', ', $fields);
        $placeholders = ':' . implode(', :', $fields);

        $sql = "INSERT INTO `{$table}` ({$columns}) VALUES ({$placeholders})";
        $stmt = $db->prepare($sql);

        $batchSize = 500;
        $inserted = 0;
        $db->beginTransaction();

        try {
            foreach ($lines as $i => $line) {
                $values = str_getcsv_compat($line, ';');
                $data = array();
                foreach ($fields as $j => $field) {
                    $data[$field] = isset($values[$j]) ? $values[$j] : '';
                }
                $stmt->execute($data);
                $inserted++;
                $total++;

                if (($i + 1) % $batchSize === 0) {
                    $db->commit();
                    echo "  {$cfg['file']}: " . ($i + 1) . "/{$totalRows}\n";
                    $db->beginTransaction();
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            echo "  Error inserting {$cfg['file']}: " . $e->getMessage() . "\n";
            continue;
        }

        echo "  {$cfg['file']}: {$inserted} rows inserted\n";
    }

    return $total;
}

return 'run_wilayah_seeder';
