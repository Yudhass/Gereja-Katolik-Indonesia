<?php

function run_database_seeder() {
    $seeders = array(
        // 'AdminSeeder',
        'GerejaSeeder',
        'JadwalMisaSeeder',
        'WilayahSeeder',
    );

    $totalRecords = 0;

    foreach ($seeders as $seederName) {
        $filePath = dirname(__FILE__) . '/' . $seederName . '.php';

        if (file_exists($filePath)) {
            echo "  Seeding: " . $seederName . "\n";

            include_once $filePath;
            $functionName = 'run_' . strtolower(str_replace('Seeder', '', $seederName)) . '_seeder';

            if (function_exists($functionName)) {
                $count = call_user_func($functionName);
                $totalRecords += $count;
                echo "  Seeded {$seederName} ({$count} records)\n";
            }
        }
    }

    return $totalRecords;
}

return 'run_database_seeder';
