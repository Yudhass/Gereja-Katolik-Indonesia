<?php 

// Start output buffering untuk mencegah "headers already sent" error
ob_start();

require_once '../app/init.php';
require_once '../app/core/App.php'; 


$app = new App();
$app->run();

// Flush output buffer
ob_end_flush();