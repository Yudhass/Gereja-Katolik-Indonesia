<?php

function migration_20260722000001_up()
{
    $charset = 'utf8';
    $sql = "CREATE TABLE IF NOT EXISTS `admins` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nama_lengkap` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) NOT NULL UNIQUE,
        `password_hash` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uk_email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $charset . ";";
    return $sql;
}

function migration_20260722000001_down()
{
    $sql = "DROP TABLE IF EXISTS `admins`;";
    return $sql;
}

return array(
    'up' => 'migration_20260722000001_up',
    'down' => 'migration_20260722000001_down'
);
