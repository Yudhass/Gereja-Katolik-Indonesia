<?php

function migration_20260722000010_up()
{
    $sql = "ALTER TABLE `gereja`
        ADD COLUMN `link_maps` VARCHAR(500) NOT NULL DEFAULT '' AFTER `kelurahan`;";
    return $sql;
}

function migration_20260722000010_down()
{
    $sql = "ALTER TABLE `gereja` DROP COLUMN `link_maps`;";
    return $sql;
}

return array(
    'up' => 'migration_20260722000010_up',
    'down' => 'migration_20260722000010_down'
);
