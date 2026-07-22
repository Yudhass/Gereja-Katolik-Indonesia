<?php

function run_admin_seeder() {
    require_once dirname(__FILE__) . '/../../core/Database.php';
    require_once dirname(__FILE__) . '/../../core/Security.php';
    $db = Database::getInstance();

    Security::forceSha256(true);
    $passwordHash = Security::hashPassword('Admin123_@');
    Security::forceSha256(false);

    $data = array(
        array(
            'nama_lengkap' => 'Admin Gereja',
            'email' => 'admin.gereja.katolik.indonesia@gmail.com',
            'password_hash' => $passwordHash
        ),
    );

    foreach ($data as $item) {
        $columns = implode(', ', array_keys($item));
        $placeholders = ':' . implode(', :', array_keys($item));
        $sql = "INSERT INTO admins ({$columns}) VALUES ({$placeholders})";
        $stmt = $db->prepare($sql);
        $stmt->execute($item);
    }

    return count($data);
}

return 'run_admin_seeder';
