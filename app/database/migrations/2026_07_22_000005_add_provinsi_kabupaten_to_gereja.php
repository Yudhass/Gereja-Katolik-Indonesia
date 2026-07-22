<?php

function migration_20260722000005_up()
{
    $sql = "ALTER TABLE `gereja`
        ADD COLUMN `provinsi` VARCHAR(100) NOT NULL DEFAULT '' AFTER `alamat`,
        ADD COLUMN `kabupaten_kota` VARCHAR(100) NOT NULL DEFAULT '' AFTER `provinsi`;";
    return $sql;
}

function migration_20260722000005_down()
{
    $sql = "ALTER TABLE `gereja`
        DROP COLUMN `kabupaten_kota`,
        DROP COLUMN `provinsi`;";
    return $sql;
}

return array(
    'up' => 'migration_20260722000005_up',
    'down' => 'migration_20260722000005_down'
);
