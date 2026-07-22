<?php
require_once dirname(__FILE__) . '/../core/Model.php';

class ModelAdmin extends Model
{
    protected $table = 'admins';
    protected $fields = array('id', 'nama_lengkap', 'email', 'password_hash', 'created_at');
}
