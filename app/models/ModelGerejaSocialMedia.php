<?php
require_once dirname(__FILE__) . '/../core/Model.php';

class ModelGerejaSocialMedia extends Model
{
    protected $table = 'gereja_social_media';
    protected $fields = array('id', 'gereja_id', 'platform', 'url', 'urutan', 'created_at');

    public function getByGereja($gerejaId)
    {
        return $this->rawQuery(
            "SELECT * FROM gereja_social_media WHERE gereja_id = ? ORDER BY urutan ASC, id ASC",
            array($gerejaId)
        );
    }

    public function deleteByGereja($gerejaId)
    {
        $sql = "DELETE FROM gereja_social_media WHERE gereja_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(array($gerejaId));
    }
}
