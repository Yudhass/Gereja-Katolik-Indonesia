<?php

/**
 * Migration: Create user_sessions Table
 * Compatible dengan PHP 5.2, 7, 8+
 */

function migration_20260312121003_up()
{
    // Auto-detect MySQL charset (utf8mb4 untuk MySQL 5.5.3+ atau utf8 untuk lebih lama)
    $charset = 'utf8'; // default aman untuk semua versi

    $sql = "CREATE TABLE IF NOT EXISTS `user_sessions` (
        `session_id` VARCHAR(128) NOT NULL,
        `user_id` INT(11) NOT NULL,
        `username` VARCHAR(255) NOT NULL,
        `role`          VARCHAR(50)     NOT NULL DEFAULT '',
        `ip_address`    VARCHAR(45)     NOT NULL DEFAULT '',
        `user_agent`    VARCHAR(500)    NOT NULL DEFAULT '',
        `payload`       TEXT            NULL COMMENT 'Data session JSON (opsional)',
        `last_activity` INT(11)         NOT NULL COMMENT 'Unix timestamp last activity',
        `created_at`    INT(11)         NOT NULL COMMENT 'Unix timestamp saat login',
        `expires_at`    INT(11)         NOT NULL COMMENT 'Unix timestamp kadaluarsa',
        PRIMARY KEY (`session_id`),
        INDEX `idx_user_id` (`user_id`),
        INDEX `idx_last_activity` (`last_activity`),
        INDEX `idx_expires_at` (`expires_at`)

    ) ENGINE=InnoDB DEFAULT CHARSET=" . $charset . ";";

    return $sql;
}

function migration_20260312121003_down()
{
    $sql = "DROP TABLE IF EXISTS `user_sessions`;";
    return $sql;
}

// Return array untuk kompatibilitas
return array(
    'up' => 'migration_20260312121003_up',
    'down' => 'migration_20260312121003_down'
);
