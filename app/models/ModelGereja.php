<?php
require_once dirname(__FILE__) . '/../core/Model.php';

class ModelGereja extends Model
{
    protected $table = 'gereja';
    protected $fields = array('id', 'slug', 'nama_gereja', 'alamat', 'provinsi', 'kabupaten_kota', 'kecamatan', 'kelurahan', 'link_maps', 'latitude', 'longitude', 'kontak_telepon', 'deskripsi', 'foto_url', 'created_at', 'updated_at');

    public function findBySlug($slug)
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug LIMIT 1";
        if ($this->usePDO) {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array('slug' => $slug));
            return $stmt->fetch(PDO::FETCH_OBJ);
        } else {
            $escaped = $this->escapeString($slug);
            $sql = str_replace(':slug', "'" . $escaped . "'", $sql);
            $result = mysql_query($sql, $this->conn);
            return mysql_fetch_object($result);
        }
    }
}
