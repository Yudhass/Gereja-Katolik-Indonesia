<?php
require_once dirname(__FILE__) . '/../core/Model.php';

class ModelJadwalMisa extends Model
{
    protected $table = 'jadwal_misa';
    protected $fields = array('id', 'gereja_id', 'hari', 'tanggal', 'waktu_mulai', 'kategori', 'keterangan', 'created_at');

    public function getByGereja($gerejaId)
    {
        return $this->selectWhere('gereja_id', $gerejaId);
    }

    public function getWithGereja()
    {
        return $this->rawQuery(
            "SELECT j.*, g.nama_gereja 
             FROM jadwal_misa j 
             JOIN gereja g ON j.gereja_id = g.id 
             ORDER BY g.nama_gereja, FIELD(j.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Spesial'), j.waktu_mulai"
        );
    }
}
