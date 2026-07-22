<?php
require_once dirname(__FILE__) . '/../core/Model.php';

class ModelSaranJadwal extends Model
{
    protected $table = 'saran_jadwal';
    protected $fields = array('id', 'gereja_id', 'jadwal_id', 'nama_pengunjung', 'saran_hari', 'saran_waktu', 'catatan', 'status', 'created_at');

    public function getPending()
    {
        return $this->rawQuery(
            "SELECT s.*, g.nama_gereja 
             FROM saran_jadwal s 
             JOIN gereja g ON s.gereja_id = g.id 
             WHERE s.status = 'Pending' 
             ORDER BY s.created_at DESC"
        );
    }

    public function getAllWithGereja()
    {
        return $this->rawQuery(
            "SELECT s.*, g.nama_gereja 
             FROM saran_jadwal s 
             JOIN gereja g ON s.gereja_id = g.id 
             ORDER BY s.created_at DESC"
        );
    }
}
