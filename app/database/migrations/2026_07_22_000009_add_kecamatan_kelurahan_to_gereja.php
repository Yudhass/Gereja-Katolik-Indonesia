<?php

function migration_20260722000009_up()
{
    $sql = "ALTER TABLE `gereja`
        ADD COLUMN `kecamatan` VARCHAR(100) NOT NULL DEFAULT '' AFTER `kabupaten_kota`,
        ADD COLUMN `kelurahan` VARCHAR(100) NOT NULL DEFAULT '' AFTER `kecamatan`;";
    return $sql;
}

function migration_20260722000009_down()
{
    $sql = "ALTER TABLE `gereja`
        DROP COLUMN `kelurahan`,
        DROP COLUMN `kecamatan`;";
    return $sql;
}

return array(
    'up' => 'migration_20260722000009_up',
    'down' => 'migration_20260722000009_down'
);
