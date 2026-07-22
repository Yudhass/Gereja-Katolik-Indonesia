<?php
// Simple router for PHP built-in server
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Jika file ada di public/, serve langsung
if ($uri && file_exists(__DIR__ . "/public" . $uri)) {
    return false;
}

// Jika tidak, route ke index.php
require __DIR__ . "/public/index.php";
