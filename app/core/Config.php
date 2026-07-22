<?php

/**
 * Configuration File
 * Menggunakan Environment Variables dari file .env
 * Kompatibel dengan PHP 5.2, 7, 8 dan versi lebih tinggi
 */

// Load Environment Variables
require_once dirname(__FILE__) . '/Env.php';
Env::load();

// Database Configuration
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_NAME', env('DB_NAME', 'crudtest'));
define('DB_PORT', env('DB_PORT', '3306'));

// Register Database Connections
require_once dirname(__FILE__) . '/DatabaseManager.php';

// Register default connection
DatabaseManager::addConnection('default', array(
    'driver' => 'mysql',
    'host' => DB_HOST,
    'user' => DB_USER,
    'pass' => DB_PASS,
    'name' => DB_NAME,
    'port' => DB_PORT
));

// Application Configuration
define('APP_NAME', env('APP_NAME', 'MVC-PHP-5-TEMPLATE'));
define('APP_ENV', env('APP_ENV', 'development'));
define('APP_DEBUG', env('APP_DEBUG', true));

// Project Configuration
define('FOLDER_PROJECT', env('FOLDER_PROJECT', 'MVC-PHP-5-TEMPLATE'));

// Base URL Configuration - Auto-detect jika kosong
$baseUrl = env('BASE_URL', '');
if (empty($baseUrl)) {
    $protocol = 'http';
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $protocol = 'https';
    } elseif (!empty($_SERVER['REQUEST_SCHEME'])) {
        $protocol = $_SERVER['REQUEST_SCHEME'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'];
    }
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    $docRoot = str_replace('\\', '/', isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '');
    $scriptFile = str_replace('\\', '/', isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '');
    $basePath = '';
    if (!empty($docRoot) && strpos($scriptFile, $docRoot) === 0) {
        $relativePath = substr($scriptFile, strlen($docRoot));
        $basePath = dirname(dirname($relativePath));
    }
    $basePath = str_replace('\\', '/', $basePath);
    if ($basePath === '/' || $basePath === '.' || $basePath === '\\') {
        $basePath = '';
    }
    $baseUrl = rtrim($protocol . '://' . $host . $basePath, '/') . '/';
}
define('BASEURL', $baseUrl);

// Upload Folder Configuration
$uploadFolder = env('UPLOAD_FOLDER', 'public/img/');
if (isset($_SERVER['DOCUMENT_ROOT'])) {
    define('UPLOAD_FOLDER', $_SERVER['DOCUMENT_ROOT'] . '/' . FOLDER_PROJECT . '/' . $uploadFolder);
} else {
    define('UPLOAD_FOLDER', dirname(__FILE__) . '/../../' . $uploadFolder);
}
define('MAX_UPLOAD_SIZE', env('MAX_UPLOAD_SIZE', 5242880)); // 5MB default

// Session Configuration
define('SESSION_LIFETIME', env('SESSION_LIFETIME', 7200)); // 2 hours
define('SESSION_NAME', env('SESSION_NAME', 'mvc_session'));

// Security Configuration
define('APP_KEY', env('APP_KEY', ''));
define('HASH_ALGO', env('HASH_ALGO', 'sha256'));
define('CSRF_ENABLED', env('CSRF_ENABLED', true));
define('CSRF_EXPIRE', env('CSRF_EXPIRE', 1800));
define('RATE_LIMIT_ENABLED', env('RATE_LIMIT_ENABLED', true));
define('RATE_LIMIT_MAX_ATTEMPTS', env('RATE_LIMIT_MAX_ATTEMPTS', 5));
define('RATE_LIMIT_TIME_WINDOW', env('RATE_LIMIT_TIME_WINDOW', 60));
define('SECURITY_HEADERS_ENABLED', env('SECURITY_HEADERS_ENABLED', true));
define('SESSION_REGENERATE_INTERVAL', env('SESSION_REGENERATE_INTERVAL', 1800));

// Password Policy
define('PASSWORD_MIN_LENGTH', env('PASSWORD_MIN_LENGTH', 8));
define('PASSWORD_REQUIRE_UPPERCASE', env('PASSWORD_REQUIRE_UPPERCASE', true));
define('PASSWORD_REQUIRE_LOWERCASE', env('PASSWORD_REQUIRE_LOWERCASE', true));
define('PASSWORD_REQUIRE_NUMBER', env('PASSWORD_REQUIRE_NUMBER', true));
define('PASSWORD_REQUIRE_SPECIAL', env('PASSWORD_REQUIRE_SPECIAL', false));

// Timezone Configuration
$timezone = env('APP_TIMEZONE', 'Asia/Jakarta');
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set($timezone);
}
define('APP_TIMEZONE', $timezone);

// Localization
define('APP_LOCALE', env('APP_LOCALE', 'id'));
define('APP_FALLBACK_LOCALE', env('APP_FALLBACK_LOCALE', 'en'));

// Maintenance Mode
define('MAINTENANCE_MODE', env('MAINTENANCE_MODE', false));
define('MAINTENANCE_MESSAGE', env('MAINTENANCE_MESSAGE', 'Aplikasi sedang dalam maintenance'));

// Logging
define('LOG_LEVEL', env('LOG_LEVEL', 'error'));
define('LOG_PATH', env('LOG_PATH', 'storage/logs/'));

// Cache Configuration
define('CACHE_ENABLED', env('CACHE_ENABLED', false));
define('CACHE_LIFETIME', env('CACHE_LIFETIME', 3600));

// Email Configuration (Optional)
define('MAIL_DRIVER', env('MAIL_DRIVER', 'smtp'));
define('MAIL_HOST', env('MAIL_HOST', 'smtp.gmail.com'));
define('MAIL_PORT', env('MAIL_PORT', 587));
define('MAIL_USERNAME', env('MAIL_USERNAME', ''));
define('MAIL_PASSWORD', env('MAIL_PASSWORD', ''));
define('MAIL_ENCRYPTION', env('MAIL_ENCRYPTION', 'tls'));
define('MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS', 'noreply@example.com'));
define('MAIL_FROM_NAME', env('MAIL_FROM_NAME', APP_NAME));

// API Configuration (Optional)
define('API_ENABLED', env('API_ENABLED', false));
define('API_PREFIX', env('API_PREFIX', 'api'));
define('API_VERSION', env('API_VERSION', 'v1'));
define('API_RATE_LIMIT', env('API_RATE_LIMIT', 60));

// ===================================================
// Initialize Security
// ===================================================
require_once dirname(__FILE__) . '/Security.php';
if (SECURITY_HEADERS_ENABLED) {
    Security::getInstance();
}

// ===================================================
// Helper Functions - Kompatibel dengan PHP 5.2+
// ===================================================

// Fungsi helper untuk URL
function base_url($path = '')
{
    return BASEURL . $path;
}

// Fungsi helper untuk flash message
function getAlertMessage()
{
    if (isset($_SESSION['flash_message'])) {
        echo $_SESSION['flash_message'];
    }
}

function delete_alert()
{
    if (isset($_SESSION['flash_message'])) {
        unset($_SESSION['flash_message']);
    }
    if (isset($_SESSION['flash_message_type'])) {
        unset($_SESSION['flash_message_type']);
    }
}

// Fungsi helper untuk sanitasi input (PHP 5.2+)
function clean_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fungsi helper untuk redirect
function redirect($url)
{
    header('Location: ' . BASEURL . ltrim($url, '/'));
    exit;
}

// ===================================================
// Security Helper Functions
// ===================================================

// CSRF Protection helpers
function csrf_field()
{
    return Security::csrfField();
}

function csrf_token()
{
    return Security::csrfToken();
}

function verify_csrf($token = null)
{
    return Security::verifyCSRFToken($token);
}

// XSS Protection helpers
function esc($string)
{
    return Security::escape($string);
}

function e($string)
{
    return Security::escape($string);
}

// Input sanitization helper
function sanitize($input, $type = 'string')
{
    return Security::sanitize($input, $type);
}

// Validation helper
function validate($input, $type, $options = array())
{
    return Security::validate($input, $type, $options);
}

// Password helpers
function hash_password($password)
{
    return Security::hashPassword($password);
}

function verify_password($password, $hash)
{
    return Security::verifyPassword($password, $hash);
}

// Rate limiting helper
function rate_limit($key, $maxAttempts = null, $timeWindow = null)
{
    if ($maxAttempts === null) {
        $maxAttempts = RATE_LIMIT_MAX_ATTEMPTS;
    }
    if ($timeWindow === null) {
        $timeWindow = RATE_LIMIT_TIME_WINDOW;
    }
    return Security::rateLimit($key, $maxAttempts, $timeWindow);
}

// Get client IP
function get_client_ip()
{
    return Security::getClientIP();
}

// Check if HTTPS
function is_https()
{
    return Security::isHTTPS();
}

// Check if AJAX
function is_ajax()
{
    return Security::isAJAX();
}

// Polyfill untuk http_response_code() - PHP 5.2+ compatibility
// Fungsi ini hanya untuk PHP < 5.4 yang tidak memiliki http_response_code()
if (!function_exists('http_response_code')) {
    function http_response_code($code = NULL) {
        if ($code !== NULL) {
            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'Not Acceptable'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
            }
            
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . $code . ' ' . $text);
            $GLOBALS['http_response_code'] = $code;
        } else {
            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
        }
        
        return $code;
    }
}

if (!function_exists('generateSlug')) {
    function generateSlug($string)
    {
        $string = trim($string);
        $string = strtolower($string);
        $string = str_replace(
            array('à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','ÿ','Ā','ā','ă','Ą','ą'),
            array('a','a','a','a','a','a','ae','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','o','u','u','u','u','y','y','a','a','a','a','a'),
            $string
        );
        $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
        $string = preg_replace('/[\s-]+/', '-', $string);
        $string = trim($string, '-');
        return $string;
    }
}

