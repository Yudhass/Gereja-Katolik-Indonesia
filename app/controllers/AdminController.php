<?php
require_once dirname(__FILE__) . '/../models/ModelGereja.php';
require_once dirname(__FILE__) . '/../models/ModelJadwalMisa.php';
require_once dirname(__FILE__) . '/../models/ModelSaranJadwal.php';

class AdminController extends Controller
{
    public function index()
    {
        $modelGereja = new ModelGereja();
        $modelJadwal = new ModelJadwalMisa();
        $modelSaran = new ModelSaranJadwal();

        $totalGereja = $modelGereja->count();
        $totalJadwal = $modelJadwal->count();
        $saranPending = $modelSaran->selectWhere('status', 'Pending');

        $data = array(
            'title' => 'Dashboard Admin',
            'totalGereja' => $totalGereja,
            'totalJadwal' => $totalJadwal,
            'totalSaranPending' => count($saranPending),
            'gerejaTerbaru' => $modelGereja->rawQuery("SELECT * FROM gereja ORDER BY created_at DESC LIMIT 5"),
            'saranTerbaru' => $modelSaran->rawQuery(
                "SELECT s.*, g.nama_gereja FROM saran_jadwal s JOIN gereja g ON s.gereja_id = g.id ORDER BY s.created_at DESC LIMIT 5"
            )
        );

        $this->view('admin/dashboard', $data);
    }
}
