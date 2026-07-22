<?php

function migration_20260722000003_up()
{
    $charset = 'utf8';
    $sql = "CREATE TABLE IF NOT EXISTS `jadwal_misa` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `gereja_id` INT(11) NOT NULL,
        `hari` ENUM('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Spesial') NOT NULL DEFAULT 'Minggu',
        `waktu_mulai` TIME NOT NULL,
        `kategori` ENUM('Harian','Mingguan','Hari Raya') NOT NULL DEFAULT 'Mingguan',
        `keterangan` VARCHAR(100) DEFAULT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `fk_jadwal_gereja` (`gereja_id`),
        CONSTRAINT `fk_jadwal_gereja` FOREIGN KEY (`gereja_id`) REFERENCES `gereja` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $charset . ";";
    return $sql;
}

function migration_20260722000003_down()
{
    $sql = "DROP TABLE IF EXISTS `jadwal_misa`;";
    return $sql;
}

return array(
    'up' => 'migration_20260722000003_up',
    'down' => 'migration_20260722000003_down'
);
