<?php

function migration_20260722000004_up()
{
    $charset = 'utf8';
    $sql = "CREATE TABLE IF NOT EXISTS `saran_jadwal` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `gereja_id` INT(11) NOT NULL,
        `jadwal_id` INT(11) DEFAULT NULL,
        `nama_pengunjung` VARCHAR(100) DEFAULT NULL,
        `saran_hari` VARCHAR(50) DEFAULT NULL,
        `saran_waktu` TIME DEFAULT NULL,
        `catatan` TEXT DEFAULT NULL,
        `status` ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `fk_saran_gereja` (`gereja_id`),
        KEY `fk_saran_jadwal` (`jadwal_id`),
        CONSTRAINT `fk_saran_gereja` FOREIGN KEY (`gereja_id`) REFERENCES `gereja` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `fk_saran_jadwal` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal_misa` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $charset . ";";
    return $sql;
}

function migration_20260722000004_down()
{
    $sql = "DROP TABLE IF EXISTS `saran_jadwal`;";
    return $sql;
}

return array(
    'up' => 'migration_20260722000004_up',
    'down' => 'migration_20260722000004_down'
);
