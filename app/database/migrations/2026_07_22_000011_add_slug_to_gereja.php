<?php

function migration_20260722000011_up()
{
    $sql = "ALTER TABLE `gereja`
        ADD COLUMN `slug` VARCHAR(255) NOT NULL DEFAULT '' AFTER `id`,
        ADD UNIQUE KEY `slug` (`slug`);";
    return $sql;
}

function migration_20260722000011_seed()
{
    require_once dirname(dirname(__FILE__)) . '/seeders/SlugSeeder.php';
    return true;
}

function migration_20260722000011_down()
{
    $sql = "ALTER TABLE `gereja` DROP INDEX `slug`, DROP COLUMN `slug`;";
    return $sql;
}

return array(
    'up' => 'migration_20260722000011_up',
    'seed' => 'migration_20260722000011_seed',
    'down' => 'migration_20260722000011_down'
);
