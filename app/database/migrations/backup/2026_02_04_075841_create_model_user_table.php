<?php
/**
 * Migration: Create model_user Table
 * Compatible dengan PHP 5.2, 7, 8+
 */

function migration_20260204075841_up() {
    // Auto-detect MySQL charset (utf8mb4 untuk MySQL 5.5.3+ atau utf8 untuk lebih lama)
    $charset = 'utf8'; // default aman untuk semua versi
    
    $sql = "CREATE TABLE IF NOT EXISTS `tbl_user` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL,
    `nama` varchar(155) NOT NULL,
    `password` varchar(255) DEFAULT NULL,
    `role` int(11) NOT NULL,
    `lokasi_kerja` varchar(255) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $charset . ";";
    
    return $sql;
}

function migration_20260204075841_down() {
    $sql = "DROP TABLE IF EXISTS `tbl_user`;";
    return $sql;
}

// Return array untuk kompatibilitas
return array(
    'up' => 'migration_20260204075841_up',
    'down' => 'migration_20260204075841_down'
);