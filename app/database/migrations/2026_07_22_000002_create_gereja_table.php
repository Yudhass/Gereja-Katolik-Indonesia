<?php

function migration_20260722000002_up()
{
    $charset = 'utf8';
    $sql = "CREATE TABLE IF NOT EXISTS `gereja` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nama_gereja` VARCHAR(150) NOT NULL,
        `alamat` TEXT NOT NULL,
        `latitude` DECIMAL(10, 8) NOT NULL,
        `longitude` DECIMAL(11, 8) NOT NULL,
        `kontak_telepon` VARCHAR(20) DEFAULT NULL,
        `deskripsi` TEXT DEFAULT NULL,
        `foto_url` VARCHAR(255) DEFAULT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $charset . ";";
    return $sql;
}

function migration_20260722000002_down()
{
    $sql = "DROP TABLE IF EXISTS `gereja`;";
    return $sql;
}

return array(
    'up' => 'migration_20260722000002_up',
    'down' => 'migration_20260722000002_down'
);
