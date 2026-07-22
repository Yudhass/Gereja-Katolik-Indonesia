<?php 

require_once dirname(__FILE__) . '/../core/Model.php';

class ModelUser extends Model
{
    protected $table = 'tbl_user';
    protected $fields = array('id', 'username', 'password', 'role', 'nama','lokasi_kerja');
}
