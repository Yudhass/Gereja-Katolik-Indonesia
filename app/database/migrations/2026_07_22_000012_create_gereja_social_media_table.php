<?php

function migration_20260722000012_up()
{
    $charset = 'utf8';
    $sql = "CREATE TABLE IF NOT EXISTS `gereja_social_media` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `gereja_id` INT(11) NOT NULL,
        `platform` VARCHAR(50) NOT NULL,
        `url` VARCHAR(500) NOT NULL,
        `urutan` INT(11) NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_social_gereja_id` (`gereja_id`),
        CONSTRAINT `fk_social_gereja` FOREIGN KEY (`gereja_id`) REFERENCES `gereja` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $charset . ";";
    return $sql;
}

function migration_20260722000012_down()
{
    $sql = "DROP TABLE IF EXISTS `gereja_social_media`;";
    return $sql;
}

return array(
    'up' => 'migration_20260722000012_up',
    'down' => 'migration_20260722000012_down'
);
