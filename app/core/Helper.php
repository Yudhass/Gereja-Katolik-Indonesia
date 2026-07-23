<?php

/**
 * Helper Functions
 * Functions yang dapat digunakan di seluruh aplikasi (controller dan view)
 * Compatible with PHP 5.2, 7, 8+
 */

/**
 * Die Dump - Beautiful debug output with file location
 * Usage: dd($var1, $var2, $var3)
 */
if (!function_exists('dd')) {
    function dd()
    {
        // Get all arguments
        $args = func_get_args();

        // Get backtrace to find where dd() was called
        $backtrace = debug_backtrace();
        $caller = isset($backtrace[0]) ? $backtrace[0] : array('file' => 'Unknown', 'line' => 0);

        // Clear any output buffer
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Start new output buffer
        ob_start();

        // Prepare debug data with location info
        $debugData = $args;
        $callerFile = isset($caller['file']) ? $caller['file'] : 'Unknown';
        $callerLine = isset($caller['line']) ? $caller['line'] : 0;

        // Load dd view
        $ddView = dirname(__FILE__) . '/../views/errors/dd.php';

        if (file_exists($ddView)) {
            require $ddView;
        } else {
            // Fallback jika view tidak ada
            echo '<!DOCTYPE html><html><head><title>Debug</title></head><body>';
            echo '<div style="background: #667eea; color: white; padding: 15px; font-family: sans-serif;">';
            echo '<h2>🐛 Debug Data - Dump & Die</h2>';
            echo '<p style="font-size: 13px; opacity: 0.9;">Called from: <strong>' . htmlspecialchars($callerFile) . '</strong> on line <strong>' . $callerLine . '</strong></p>';
            echo '</div>';
            echo '<pre style="background: #1a1a2e; color: #e0e0e0; padding: 20px; font-family: monospace;">';
            foreach ($debugData as $index => $data) {
                echo "=== Variable #" . ($index + 1) . " ===\n";
                var_dump($data);
                echo "\n\n";
            }
            echo '</pre></body></html>';
        }

        // Flush output buffer
        ob_end_flush();

        die();
    }
}


if (!function_exists('Show404')) {
    function Show404($messages = null)
    {
        // load view error 404
        $errorView = dirname(__FILE__) . '/../views/errors/404.php';
        if (file_exists($errorView)) {
            require $errorView;
        } else {
            // Fallback jika view tidak ada
            echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body>';
            echo '<h1>404 Not Found</h1>';
            echo '<p>Maaf, halaman yang Anda cari tidak dapat ditemukan.</p>';
            if ($messages) {
                echo '<p>' . htmlspecialchars($messages) . '</p>';
            }
            echo '</body></html>';
        }
    }
}

if (!function_exists('Show403')) {
    function Show403($messages = null)
    {
        // load view error 403
        $errorView = dirname(__FILE__) . '/../views/errors/403.php';
        if (file_exists($errorView)) {
            require $errorView;
        } else {
            // Fallback jika view tidak ada
            echo '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body>';
            echo '<h1>403 Forbidden</h1>';
            echo '<p>Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.</p>';
            if ($messages) {
                echo '<p>' . htmlspecialchars($messages) . '</p>';
            }
            echo '</body></html>';
        }
    }
}

/**
 * Dump - Debug output without die
 * Usage: dump($var1, $var2, $var3)
 */
if (!function_exists('dump')) {
    function dump()
    {
        $args = func_get_args();

        echo '<pre style="background: #2d2d44; color: #e0e0e0; padding: 15px; margin: 10px; border-radius: 5px; font-family: monospace; font-size: 13px; border-left: 4px solid #667eea;">';
        foreach ($args as $index => $data) {
            if (count($args) > 1) {
                echo '<strong style="color: #667eea;">Variable #' . ($index + 1) . ':</strong><br>';
            }
            var_dump($data);
            if ($index < count($args) - 1) {
                echo '<hr style="border: 1px solid #3d3d5c; margin: 10px 0;">';
            }
        }
        echo '</pre>';
    }
}

// Get session value
if (!function_exists('getSession')) {
    function getSession($key, $default = null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
}

if (!function_exists('Auth')) {
    function Auth()
    {
        if (isset($_SESSION['user'])) {
            return (object) $_SESSION['user'];
        } else {
            return null;
        }
    }
}

// Set session value
if (!function_exists('setSession')) {
    function setSession($key, $value)
    {
        $_SESSION[$key] = $value;
    }
}

// Get and clear flash message
if (!function_exists('getFlashMessage')) {
    function getFlashMessage()
    {
        if (isset($_SESSION['flash_message'])) {
            $message = array(
                'message' => $_SESSION['flash_message'],
                'type' => isset($_SESSION['flash_message_type']) ? $_SESSION['flash_message_type'] : 'success'
            );

            // Clear the flash message after retrieving
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_message_type']);

            return $message;
        }

        return null;
    }
}

if (!function_exists('getTypeFlashMessage')) {
    function getTypeFlashMessage()
    {
        if (isset($_SESSION['flash_message'])) {
            $message = array(
                'type' => isset($_SESSION['flash_message_type']) ? $_SESSION['flash_message_type'] : 'success'
            );

            return $message['type'];
        }

        return null;
    }
}

// Check if flash message exists
if (!function_exists('hasFlashMessage')) {
    function hasFlashMessage()
    {
        return isset($_SESSION['flash_message']);
    }
}

// Set flash message
if (!function_exists('setFlashMessage')) {
    function setFlashMessage($message, $type = 'success')
    {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_message_type'] = $type;
    }
}

// convert tanggal full indo 2026-01-23 08:40:00 -> 23 Januari 2026 08:40:00
if (!function_exists('getDateID')) {
    function getDateID($date = null)
    {
        if ($date == null) {
            return '-';
        } else {
            return date('d F Y H:i:s', strtotime($date));
        }
    }
}

// Display flash message as HTML
if (!function_exists('displayFlashMessage')) {
    function displayFlashMessage()
    {
        $flash = getFlashMessage();
        if ($flash) {
            $alertClass = array(
                'success' => 'alert-success',
                'error' => 'alert-danger',
                'warning' => 'alert-warning',
                'info' => 'alert-info'
            );

            $class = isset($alertClass[$flash['type']]) ? $alertClass[$flash['type']] : 'alert-info';

            return '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">
                        ' . htmlspecialchars($flash['message']) . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        }

        return '';
    }
}

// Dump and Die - Debug helper
if (!function_exists('dd')) {
    function dd()
    {
        // Get all arguments
        $args = func_get_args();

        // Clear any output buffer
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Prepare debug data
        $debugData = array();
        foreach ($args as $arg) {
            $debugData[] = $arg;
        }

        // Load dd view
        $ddView = dirname(__FILE__) . '/../views/errors/dd.php';

        if (file_exists($ddView)) {
            require $ddView;
        } else {
            // Fallback jika view tidak ada
            echo '<pre>';
            foreach ($debugData as $index => $data) {
                echo "=== Variable #" . ($index + 1) . " ===\n";
                var_dump($data);
                echo "\n";
            }
            echo '</pre>';
        }

        die();
    }
}

// Dump - Debug helper tanpa die
if (!function_exists('dump')) {
    function dump()
    {
        $args = func_get_args();

        echo '<div style="background: #f8f9fa; border: 2px solid #667eea; border-radius: 8px; padding: 20px; margin: 10px 0; font-family: monospace;">';
        foreach ($args as $index => $arg) {
            echo '<div style="margin-bottom: 15px;">';
            echo '<strong style="color: #667eea;">Variable #' . ($index + 1) . ':</strong>';
            echo '<pre style="margin: 10px 0; padding: 10px; background: white; border-radius: 4px;">';
            var_dump($arg);
            echo '</pre>';
            echo '</div>';
        }
        echo '</div>';
    }
}

/**
 * ============================================================================
 * VALIDATION HELPER FUNCTIONS
 * ============================================================================
 */

/**
 * Create validator instance
 * Usage: $validator = validator($data, $rules, $messages);
 * 
 * @param array $data Data yang akan divalidasi
 * @param array $rules Rules validasi
 * @param array $messages Custom error messages (optional)
 * @return Validator
 */
if (!function_exists('validator')) {
    function validator($data, $rules, $messages = array())
    {
        return new Validator($data, $rules, $messages);
    }
}

/**
 * Quick validation with auto redirect on fail
 * Usage: validate($data, $rules, $messages);
 * 
 * @param array $data Data yang akan divalidasi
 * @param array $rules Rules validasi
 * @param array $messages Custom error messages (optional)
 * @param string $redirectUrl URL untuk redirect jika gagal (optional)
 * @return Validator|null Return validator jika sukses, redirect jika gagal
 */
if (!function_exists('validate')) {
    function validate($data, $rules, $messages = array(), $redirectUrl = null)
    {
        $validator = new Validator($data, $rules, $messages);

        if ($validator->fails()) {
            $errors = $validator->getErrorMessages();
            $errorMessage = implode(' ', $errors);

            // Set flash message untuk alert notif
            setFlashMessage($errorMessage, 'error');

            // Redirect
            if ($redirectUrl) {
                header('Location: ' . $redirectUrl);
            } else {
                // Redirect back
                if (isset($_SERVER['HTTP_REFERER'])) {
                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                } else {
                    header('Location: ' . BASEURL);
                }
            }
            exit;
        }

        return $validator;
    }
}

/**
 * Validate data and return boolean
 * Usage: if (is_valid($data, $rules)) { ... }
 * 
 * @param array $data Data yang akan divalidasi
 * @param array $rules Rules validasi
 * @param array $messages Custom error messages (optional)
 * @return boolean
 */
if (!function_exists('is_valid')) {
    function is_valid($data, $rules, $messages = array())
    {
        $validator = new Validator($data, $rules, $messages);
        return $validator->passes();
    }
}

/**
 * Get validation errors
 * Usage: $errors = validation_errors($validator);
 * 
 * @param Validator $validator Instance validator
 * @return array
 */
if (!function_exists('validation_errors')) {
    function validation_errors($validator)
    {
        if ($validator instanceof Validator) {
            return $validator->getErrors();
        }
        return array();
    }
}

/**
 * Get first validation error message
 * Usage: $error = validation_first_error($validator);
 * 
 * @param Validator $validator Instance validator
 * @return string|null
 */
if (!function_exists('validation_first_error')) {
    function validation_first_error($validator)
    {
        if ($validator instanceof Validator) {
            return $validator->getFirstError();
        }
        return null;
    }
}

/**
 * Get all validation error messages as flat array
 * Usage: $messages = validation_messages($validator);
 * 
 * @param Validator $validator Instance validator
 * @return array
 */
if (!function_exists('validation_messages')) {
    function validation_messages($validator)
    {
        if ($validator instanceof Validator) {
            return $validator->getErrorMessages();
        }
        return array();
    }
}

if (!function_exists('jsonResponse')) {
    function jsonResponse($status, $message = null, $data = null)
    {
        header('Content-Type: application/json');
        echo json_encode(array('status' => $status, 'message' => $message, 'data' => $data));
    }
}



/**
 * ============================================================================
 * DATABASE HELPER FUNCTIONS
 * ============================================================================
 */

/**
 * Get database connection by name
 * Usage: $db = db_connection('karyawan');
 * 
 * @param string $name Connection name
 * @return DatabaseConnection
 */
if (!function_exists('db_connection')) {
    function db_connection($name = 'default')
    {
        return DatabaseManager::connection($name);
    }
}

/**
 * Get default database connection
 * Usage: $db = db();
 * 
 * @return DatabaseConnection
 */
if (!function_exists('db')) {
    function db()
    {
        return DatabaseManager::connection('default');
    }
}

/**
 * Execute raw query on specific connection
 * Usage: db_query('SELECT * FROM users', array(), 'karyawan');
 * 
 * @param string $query SQL query
 * @param array $params Query parameters
 * @param string $connection Connection name
 * @return mixed
 */
if (!function_exists('db_query')) {
    function db_query($query, $params = array(), $connection = 'default')
    {
        $db = DatabaseManager::connection($connection);
        return $db->query($query, $params);
    }
}

if (!function_exists('formatTanggal')) {
    function formatTanggal($tanggal = null)
    {
        if ($tanggal == null) {
            return '-';
        } else {
            $ts = strtotime($tanggal);
            if ($ts === false) {
                return '-';
            }
            $day = date('j', $ts); // day without leading zero
            $months = array(
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            );
            $month = (int) date('n', $ts);
            $year = date('Y', $ts);
            return $day . ' ' . $months[$month] . ' ' . $year;
        }
    }
}

if (!function_exists('formatTanggalJam')) {
    function formatTanggalJam($tanggal = null)
    {
        if ($tanggal == null) {
            return '-';
        } else {
            $ts = strtotime($tanggal);
            if ($ts === false) {
                return '-';
            }
            $day = date('j', $ts); // day without leading zero
            $months = array(
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            );
            $month = (int) date('n', $ts);
            $year = date('Y', $ts);
            $time = date('H:i', $ts);
            return $day . ' ' . $months[$month] . ' ' . $year . ' ' . $time;
        }
    }
}

/**
 * ============================================================================
 * PHP 5.2 COMPATIBILITY POLYFILLS & HELPERS
 * Compatible dengan PHP 5.2, 7.x, dan 8.x+
 * ============================================================================
 */

/**
 * lcfirst() - Tidak tersedia di PHP 5.2, tersedia mulai PHP 5.3
 * Huruf pertama menjadi lowercase
 * 
 * @param string $str String input
 * @return string String dengan huruf pertama lowercase
 */
if (!function_exists('lcfirst')) {
    function lcfirst($str)
    {
        if (empty($str)) return $str;
        $str[0] = strtolower($str[0]);
        return $str;
    }
}

/**
 * array_key_first() - Tidak tersedia di PHP 5.2-7.2, tersedia mulai PHP 7.3
 * Dapatkan key pertama dari array
 * 
 * @param array $array Input array
 * @return mixed Key pertama atau null
 */
if (!function_exists('array_key_first')) {
    function array_key_first($array)
    {
        foreach ($array as $key => $unused) {
            return $key;
        }
        return null;
    }
}

/**
 * array_key_last() - Tidak tersedia di PHP 5.2-7.2, tersedia mulai PHP 7.3
 * Dapatkan key terakhir dari array
 * 
 * @param array $array Input array
 * @return mixed Key terakhir atau null
 */
if (!function_exists('array_key_last')) {
    function array_key_last($array)
    {
        $key = null;
        foreach ($array as $key => $unused) {
            // Loop terus hingga key terakhir
        }
        return $key;
    }
}

/**
 * str_contains() - Tidak tersedia di PHP 5.2-7.x, tersedia mulai PHP 8.0
 * Cek apakah string mengandung substring
 * 
 * @param string $haystack String yang dicari
 * @param string $needle Substring yang dicari
 * @return boolean
 */
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        if ($needle === '') {
            return true;
        }
        return strpos($haystack, $needle) !== false;
    }
}

/**
 * str_starts_with() - Tidak tersedia di PHP 5.2-7.x, tersedia mulai PHP 8.0
 * Cek apakah string dimulai dengan substring
 * 
 * @param string $haystack String yang dicari
 * @param string $needle Substring yang dicari
 * @return boolean
 */
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle)
    {
        $length = strlen($needle);
        return substr($haystack, 0, $length) === $needle;
    }
}

/**
 * str_ends_with() - Tidak tersedia di PHP 5.2-7.x, tersedia mulai PHP 8.0
 * Cek apakah string diakhiri dengan substring
 * 
 * @param string $haystack String yang dicari
 * @param string $needle Substring yang dicari
 * @return boolean
 */
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle)
    {
        $length = strlen($needle);
        return $length > 0 ? substr($haystack, -$length) === $needle : true;
    }
}

/**
 * get_class_vars() - Untuk compatibility dengan object properties
 * Dapatkan public properties dari object/class
 * 
 * @param object|string $obj Object atau class name
 * @return array
 */
if (!function_exists('object_to_array')) {
    function object_to_array($obj)
    {
        if (is_object($obj)) {
            // Gunakan get_object_vars untuk convert object ke array
            // Lebih aman daripada (array)$obj di PHP 8+
            return get_object_vars($obj);
        }
        return (array) $obj;
    }
}

/**
 * array_to_object() - Convert array ke stdClass object
 * Lebih aman dan portable daripada (object)$array
 * 
 * @param array $array Input array
 * @return stdClass Object
 */
if (!function_exists('array_to_object')) {
    function array_to_object($array)
    {
        $obj = new stdClass();
        foreach ((array) $array as $key => $val) {
            $obj->{$key} = $val;
        }
        return $obj;
    }
}

/**
 * Safely get nested array/object value
 * Usage: $value = array_get($data, 'user.profile.name', 'default');
 * 
 * @param array|object $data Data source
 * @param string $path Dot-separated path (e.g., 'user.profile.name')
 * @param mixed $default Default value jika tidak ditemukan
 * @return mixed
 */
if (!function_exists('array_get')) {
    function array_get($data, $path, $default = null)
    {
        if (empty($path)) {
            return $data;
        }

        $keys = explode('.', $path);
        $current = $data;

        foreach ($keys as $key) {
            if (is_array($current)) {
                if (!isset($current[$key])) {
                    return $default;
                }
                $current = $current[$key];
            } elseif (is_object($current)) {
                if (!isset($current->{$key})) {
                    return $default;
                }
                $current = $current->{$key};
            } else {
                return $default;
            }
        }

        return $current;
    }
}

/**
 * Get PHP Version Information
 * Compatible dengan semua versi PHP
 * 
 * @return object PHP version info
 */
if (!function_exists('get_php_version_info')) {
    function get_php_version_info()
    {
        $version = phpversion();
        $parts = explode('.', $version);
        
        $info = array(
            'full' => $version,
            'major' => (int) $parts[0],
            'minor' => (int) isset($parts[1]) ? $parts[1] : 0,
            'patch' => (int) isset($parts[2]) ? $parts[2] : 0,
            'is_php52' => (int) $parts[0] === 5 && (int) isset($parts[1]) ? (int) $parts[1] === 2 : false,
            'is_php5' => (int) $parts[0] === 5,
            'is_php7' => (int) $parts[0] === 7,
            'is_php8' => (int) $parts[0] === 8,
            'is_modern' => (int) $parts[0] >= 7
        );
        
        return (object) $info;
    }
}

/**
 * Check if extension is loaded (safe for all PHP versions)
 * 
 * @param string $ext Extension name (tanpa 'php_' prefix)
 * @return boolean
 */
if (!function_exists('is_extension_loaded_safe')) {
    function is_extension_loaded_safe($ext)
    {
        // Remove 'php_' prefix jika ada
        $ext = str_replace('php_', '', $ext);
        
        if (function_exists('extension_loaded')) {
            return extension_loaded($ext);
        }
        
        return false;
    }
}

/**
 * Safe JSON encode dengan error handling
 * Compatible dengan semua versi PHP
 * 
 * @param mixed $data Data to encode
 * @return string JSON string atau error message
 */
if (!function_exists('safe_json_encode')) {
    function safe_json_encode($data)
    {
        if (!function_exists('json_encode')) {
            // Fallback untuk PHP tanpa json extension
            return serialize($data);
        }
        
        $json = json_encode($data);
        
        // Check json_last_error jika available (PHP 5.3+)
        if (function_exists('json_last_error')) {
            if (json_last_error() !== JSON_ERROR_NONE) {
                return json_encode(array('error' => 'JSON encode failed'));
            }
        }
        
        return $json;
    }
}

/**
 * Safe JSON decode dengan error handling
 * Compatible dengan semua versi PHP
 * 
 * @param string $json JSON string
 * @param boolean $assoc Return as associative array
 * @return mixed Decoded data atau null
 */
if (!function_exists('safe_json_decode')) {
    function safe_json_decode($json, $assoc = true)
    {
        if (!function_exists('json_decode')) {
            // Fallback untuk PHP tanpa json extension
            return unserialize($json);
        }
        
        $decoded = json_decode($json, $assoc);
        
        // Check json_last_error jika available (PHP 5.3+)
        if (function_exists('json_last_error')) {
            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }
        }
        
        return $decoded;
    }
}

/**
 * ============================================================================
 * MAPS & COORDINATES HELPER FUNCTIONS
 * ============================================================================
 */

/**
 * Extract coordinates (latitude, longitude) from Google Maps URL
 * 
 * Supports multiple Google Maps URL formats:
 * - https://www.google.com/maps/@-6.123456,106.789012,15z
 * - https://maps.google.com/?q=-6.123456,106.789012
 * - https://www.google.com/maps/place/.../@-6.123456,106.789012,...
 * - https://www.google.com/maps/search/-6.123456,106.789012
 * 
 * @param string $link_maps Google Maps URL
 * @param float $defaultLat Default latitude if extraction fails
 * @param float $defaultLng Default longitude if extraction fails
 * @return array Array with 'lat' and 'lng' keys
 */
if (!function_exists('extractCoordinatesFromMapsUrl')) {
    function extractCoordinatesFromMapsUrl($link_maps, $defaultLat = null, $defaultLng = null)
    {
        $lat = $defaultLat;
        $lng = $defaultLng;
        
        if (empty($link_maps)) {
            return array('lat' => $lat, 'lng' => $lng);
        }
        
        // Trim whitespace
        $link_maps = trim($link_maps);
        
        // Pattern 0 (PRIORITY): !3d and !4d from Google Maps place data
        // These are the most accurate coordinates from Google Maps
        // They appear as !3d<lat>...!4d<lng> in the URL
        if (preg_match('/!3d(-?\d+\.?\d*)/', $link_maps, $m1) && preg_match('/!4d(-?\d+\.?\d*)/', $link_maps, $m2)) {
            $lat_val = (float)$m1[1];
            $lng_val = (float)$m2[1];
            if ($lat_val >= -90 && $lat_val <= 90 && $lng_val >= -180 && $lng_val <= 180) {
                return array('lat' => $lat_val, 'lng' => $lng_val);
            }
        }
        
        // Pattern 1: @-6.123456,106.789012 (from google maps direct share or maps/@lat,lng format)
        if (preg_match('/@(-?\d+\.?\d*),(-?\d+\.?\d*)/', $link_maps, $m)) {
            if (isset($m[1], $m[2]) && !empty($m[1]) && !empty($m[2])) {
                $lat_val = (float)$m[1];
                $lng_val = (float)$m[2];
                // Validate latitude range: -90 to 90
                if ($lat_val >= -90 && $lat_val <= 90) {
                    // Validate longitude range: -180 to 180
                    if ($lng_val >= -180 && $lng_val <= 180) {
                        return array('lat' => $lat_val, 'lng' => $lng_val);
                    }
                }
            }
        }
        
        // Pattern 2: ?q=-6.123456,106.789012 or &q=-6.123456,106.789012
        if (preg_match('/[?&]q=(-?\d+\.?\d*),(-?\d+\.?\d*)/', $link_maps, $m)) {
            if (isset($m[1], $m[2]) && !empty($m[1]) && !empty($m[2])) {
                $lat_val = (float)$m[1];
                $lng_val = (float)$m[2];
                if ($lat_val >= -90 && $lat_val <= 90 && $lng_val >= -180 && $lng_val <= 180) {
                    return array('lat' => $lat_val, 'lng' => $lng_val);
                }
            }
        }
        
        // Pattern 3: /search/-6.123456,106.789012 format
        if (preg_match('/\/search\/(-?\d+\.?\d*),(-?\d+\.?\d*)/', $link_maps, $m)) {
            if (isset($m[1], $m[2]) && !empty($m[1]) && !empty($m[2])) {
                $lat_val = (float)$m[1];
                $lng_val = (float)$m[2];
                if ($lat_val >= -90 && $lat_val <= 90 && $lng_val >= -180 && $lng_val <= 180) {
                    return array('lat' => $lat_val, 'lng' => $lng_val);
                }
            }
        }
        
        return array('lat' => $lat, 'lng' => $lng);
    }
}

/**
 * ============================================================================
 * CONDITIONAL FEATURE DETECTION
 * Untuk determine apa yang bisa digunakan dalam environment saat ini
 * ============================================================================
 */

// Detect PHP capabilities
$phpInfo = get_php_version_info();

// Define constants untuk feature detection
if (!defined('PHP_VERSION_COMPAT_MAJOR')) {
    define('PHP_VERSION_COMPAT_MAJOR', $phpInfo->major);
    define('PHP_VERSION_COMPAT_MINOR', $phpInfo->minor);
    define('IS_PHP_52', $phpInfo->is_php52);
    define('IS_PHP_5X', $phpInfo->is_php5);
    define('IS_PHP_7X', $phpInfo->is_php7);
    define('IS_PHP_8X', $phpInfo->is_php8);
}
