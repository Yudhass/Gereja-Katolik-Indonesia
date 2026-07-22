<?php

function migration_20260722000006_up()
{
    $sql = "ALTER TABLE `jadwal_misa`
        ADD COLUMN `tanggal` DATE DEFAULT NULL AFTER `hari`;";
    return $sql;
}

function migration_20260722000006_down()
{
    $sql = "ALTER TABLE `jadwal_misa` DROP COLUMN `tanggal`;";
    return $sql;
}

return array(
    'up' => 'migration_20260722000006_up',
    'down' => 'migration_20260722000006_down'
);
