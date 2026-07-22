<?php

function migration_20260722000008_up()
{
    $db = Database::getInstance();
    $charset = 'utf8';

    $statements = array(
        "user_sessions" => "CREATE TABLE IF NOT EXISTS `user_sessions` (
            `session_id` VARCHAR(128) NOT NULL,
            `user_id` INT(11) NOT NULL,
            `username` VARCHAR(255) NOT NULL,
            `role` VARCHAR(50) NOT NULL DEFAULT '',
            `ip_address` VARCHAR(45) NOT NULL DEFAULT '',
            `user_agent` VARCHAR(500) NOT NULL DEFAULT '',
            `payload` TEXT NULL COMMENT 'Data session JSON (opsional)',
            `last_activity` INT(11) NOT NULL,
            `created_at` INT(11) NOT NULL,
            `expires_at` INT(11) NOT NULL,
            PRIMARY KEY (`session_id`),
            KEY `idx_user_id` (`user_id`),
            KEY `idx_last_activity` (`last_activity`),
            KEY `idx_expires_at` (`expires_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET={$charset}",

        "provinces" => "CREATE TABLE IF NOT EXISTS `provinces` (
            `id` CHAR(2) NOT NULL,
            `name` VARCHAR(100) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET={$charset}",

        "regencies" => "CREATE TABLE IF NOT EXISTS `regencies` (
            `id` CHAR(4) NOT NULL,
            `province_id` CHAR(2) NOT NULL,
            `name` VARCHAR(100) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_province_id` (`province_id`),
            CONSTRAINT `fk_regency_province` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET={$charset}",

        "districts" => "CREATE TABLE IF NOT EXISTS `districts` (
            `id` CHAR(6) NOT NULL,
            `regency_id` CHAR(4) NOT NULL,
            `name` VARCHAR(100) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_regency_id` (`regency_id`),
            CONSTRAINT `fk_district_regency` FOREIGN KEY (`regency_id`) REFERENCES `regencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET={$charset}",

        "villages" => "CREATE TABLE IF NOT EXISTS `villages` (
            `id` CHAR(10) NOT NULL,
            `district_id` CHAR(6) NOT NULL,
            `name` VARCHAR(100) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_district_id` (`district_id`),
            CONSTRAINT `fk_village_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET={$charset}",
    );

    foreach ($statements as $name => $sql) {
        $db->query($sql);
    }

    return "SELECT 1";
}

function migration_20260722000008_down()
{
    $db = Database::getInstance();
    $tables = array('villages', 'districts', 'regencies', 'provinces', 'user_sessions');
    foreach ($tables as $table) {
        $db->query("DROP TABLE IF EXISTS `{$table}`");
    }
    return "SELECT 1";
}

return array(
    'up' => 'migration_20260722000008_up',
    'down' => 'migration_20260722000008_down'
);
