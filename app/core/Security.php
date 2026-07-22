<?php

// ============================================
// PHP 5.4 Session Constants Polyfill
// ============================================
if (!defined('PHP_SESSION_DISABLED')) {
    define('PHP_SESSION_DISABLED', 0);
}
if (!defined('PHP_SESSION_NONE')) {
    define('PHP_SESSION_NONE', 1);
}
if (!defined('PHP_SESSION_ACTIVE')) {
    define('PHP_SESSION_ACTIVE', 2);
}

/**
 * Security Class - Comprehensive Security Layer
 * Kompatibel dengan PHP 5.2, 7, 8 dan lebih tinggi
 * 
 * Fitur:
 * - CSRF Protection
 * - XSS Protection
 * - SQL Injection Prevention
 * - Session Security
 * - Input Validation & Sanitization
 * - Rate Limiting
 * - Password Hashing
 * - Security Headers
 */
class Security
{
    private static $instance = null;
    private static $forceSha256 = false;
    private static $rateLimitStore = array();

    public static function forceSha256($force = true)
    {
        self::$forceSha256 = $force;
    }

    /**
     * Get Security instance (Singleton)
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialize Security
     */
    public function __construct()
    {
        $this->startSecureSession();
        $this->setSecurityHeaders();
    }

    // ==========================================
    // CSRF PROTECTION
    // ==========================================

    /**
     * Generate CSRF Token
     * 
     * @return string
     */
    public static function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            self::regenerateCSRFToken();
        }
        
        // Regenerate token every 30 minutes
        if (time() - $_SESSION['csrf_token_time'] > 1800) {
            self::regenerateCSRFToken();
        }
        
        return $_SESSION['csrf_token'];
    }

    /**
     * Regenerate CSRF Token
     */
    private static function regenerateCSRFToken()
    {
        if (function_exists('random_bytes')) {
            // PHP 7+
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            // PHP 5.3+
            $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
        } else {
            // PHP 5.2 fallback
            $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
        }
        $_SESSION['csrf_token_time'] = time();
    }

    /**
     * Verify CSRF Token
     * 
     * @param string $token
     * @return bool
     */
    public static function verifyCSRFToken($token = null)
    {
        if ($token === null) {
            $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        }

        if (!isset($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        // Time-safe comparison
        return self::hashEquals($_SESSION['csrf_token'], $token);
    }

    /**
     * Get CSRF input field HTML
     * 
     * @return string
     */
    public static function csrfField()
    {
        $token = self::generateCSRFToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Get CSRF token for AJAX
     * 
     * @return string
     */
    public static function csrfToken()
    {
        return self::generateCSRFToken();
    }

    // ==========================================
    // XSS PROTECTION
    // ==========================================

    /**
     * Escape output for HTML
     * 
     * @param string $string
     * @return string
     */
    public static function escape($string)
    {
        if (is_array($string)) {
            return array_map(array('Security', 'escape'), $string);
        }
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Clean HTML (allow safe tags)
     * 
     * @param string $html
     * @return string
     */
    public static function cleanHTML($html)
    {
        // Allowed tags
        $allowedTags = '<p><br><b><i><u><strong><em><a><ul><ol><li><h1><h2><h3><h4><h5><h6>';
        return strip_tags($html, $allowedTags);
    }

    /**
     * Remove all HTML tags
     * 
     * @param string $string
     * @return string
     */
    public static function stripHTML($string)
    {
        return strip_tags($string);
    }

    // ==========================================
    // INPUT VALIDATION & SANITIZATION
    // ==========================================

    /**
     * Sanitize input string
     * 
     * @param string $input
     * @param string $type (string, email, url, int, float, alpha, alphanumeric)
     * @return mixed
     */
    public static function sanitize($input, $type = 'string')
    {
        $input = trim($input);
        
        switch ($type) {
            case 'email':
                $input = filter_var($input, FILTER_SANITIZE_EMAIL);
                break;
                
            case 'url':
                $input = filter_var($input, FILTER_SANITIZE_URL);
                break;
                
            case 'int':
            case 'integer':
                $input = filter_var($input, FILTER_SANITIZE_NUMBER_INT);
                $input = intval($input);
                break;
                
            case 'float':
                $input = filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $input = floatval($input);
                break;
                
            case 'alpha':
                $input = preg_replace('/[^a-zA-Z]/', '', $input);
                break;
                
            case 'alphanumeric':
                $input = preg_replace('/[^a-zA-Z0-9]/', '', $input);
                break;
                
            case 'string':
            default:
                $input = strip_tags($input);
                $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
                break;
        }
        
        return $input;
    }

    /**
     * Validate input
     * 
     * @param string $input
     * @param string $type
     * @param array $options
     * @return bool
     */
    public static function validate($input, $type, $options = array())
    {
        switch ($type) {
            case 'email':
                return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
                
            case 'url':
                return filter_var($input, FILTER_VALIDATE_URL) !== false;
                
            case 'ip':
                return filter_var($input, FILTER_VALIDATE_IP) !== false;
                
            case 'int':
            case 'integer':
                return filter_var($input, FILTER_VALIDATE_INT) !== false;
                
            case 'float':
                return filter_var($input, FILTER_VALIDATE_FLOAT) !== false;
                
            case 'alpha':
                return preg_match('/^[a-zA-Z]+$/', $input);
                
            case 'alphanumeric':
                return preg_match('/^[a-zA-Z0-9]+$/', $input);
                
            case 'required':
                return !empty($input);
                
            case 'min':
                $min = isset($options['min']) ? $options['min'] : 0;
                return strlen($input) >= $min;
                
            case 'max':
                $max = isset($options['max']) ? $options['max'] : 255;
                return strlen($input) <= $max;
                
            case 'regex':
                $pattern = isset($options['pattern']) ? $options['pattern'] : '';
                return preg_match($pattern, $input);
                
            default:
                return true;
        }
    }

    // ==========================================
    // PASSWORD SECURITY
    // ==========================================

    /**
     * Hash password (PHP 5.2+ compatible)
     * 
     * @param string $password
     * @return string
     */
    public static function hashPassword($password)
    {
        if (!self::$forceSha256 && function_exists('password_hash')) {
            $options = array('cost' => 12);
            return password_hash($password, PASSWORD_BCRYPT, $options);
        }

        $salt = self::generateSalt();
        return '$SHA256$' . $salt . '$' . hash('sha256', $salt . $password);
    }

    /**
     * Verify password
     * 
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verifyPassword($password, $hash)
    {
        if (function_exists('password_verify')) {
            // PHP 5.5+
            return password_verify($password, $hash);
        } else {
            // Cek format hash
            if (substr($hash, 0, 3) === '$2a' || substr($hash, 0, 3) === '$2y') {
                // Bcrypt hash - PHP 5.2-5.4 fallback
                return crypt($password, $hash) === $hash;
            } elseif (substr($hash, 0, 8) === '$SHA256$') {
                // SHA-256 custom hash
                $parts = explode('$', $hash);
                if (count($parts) == 4) {
                    $salt = $parts[2];
                    $storedHash = $parts[3];
                    return hash('sha256', $salt . $password) === $storedHash;
                }
            }
            
            return false;
        }
    }

    /**
     * Generate salt for bcrypt password (22 chars in base64 format)
     * Bcrypt requires exactly 22 characters from the alphabet: ./A-Za-z0-9
     * 
     * @return string
     */
    private static function generateSalt()
    {
        // Alphabet untuk bcrypt base64: ./0-9A-Za-z
        $alphabet = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $salt = '';
        
        if (function_exists('random_bytes')) {
            // PHP 7+: Gunakan random_bytes untuk security yang lebih baik
            $randomBytes = random_bytes(16);
            for ($i = 0; $i < 22; $i++) {
                $salt .= $alphabet[ord($randomBytes[$i % 16]) % 64];
            }
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            // PHP 5.3+: Gunakan openssl
            $randomBytes = openssl_random_pseudo_bytes(16);
            for ($i = 0; $i < 22; $i++) {
                $salt .= $alphabet[ord($randomBytes[$i % 16]) % 64];
            }
        } else {
            // PHP 5.2: Fallback dengan kombinasi fungsi random
            for ($i = 0; $i < 22; $i++) {
                $salt .= $alphabet[mt_rand(0, 63)];
            }
        }
        
        return $salt;
    }

    // ==========================================
    // SESSION SECURITY
    // ==========================================

    /**
     * Start secure session
     */
    private function startSecureSession()
    {
        // Check if session is already started (PHP 5.2/5.3 compatible)
        $sessionStarted = false;
        
        if (function_exists('session_status')) {
            // PHP 5.4+
            $sessionStarted = (session_status() == PHP_SESSION_ACTIVE);
        } else {
            // PHP 5.2/5.3: Check if session is started
            $sessionStarted = (isset($_SESSION) || (session_id() !== ''));
        }
        
        if (!$sessionStarted) {
            // Session configuration
            ini_set('session.use_only_cookies', 1);
            
            // use_strict_mode only available in PHP 5.5.2+
            if (version_compare(PHP_VERSION, '5.5.2', '>=')) {
                ini_set('session.use_strict_mode', 1);
            }
            
            ini_set('session.cookie_httponly', 1);
            
            // HTTPS only in production
            if (defined('APP_ENV') && APP_ENV === 'production') {
                ini_set('session.cookie_secure', 1);
            }
            
            // Session name
            if (defined('SESSION_NAME')) {
                session_name(SESSION_NAME);
            }
            
            // Session lifetime
            if (defined('SESSION_LIFETIME')) {
                ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
            }
            
            session_start();
            
            // Regenerate session ID periodically
            if (!isset($_SESSION['created'])) {
                $_SESSION['created'] = time();
            } elseif (time() - $_SESSION['created'] > 1800) {
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            }
        }
    }

    /**
     * Regenerate session ID
     */
    public static function regenerateSession()
    {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }

    /**
     * Destroy session
     */
    public static function destroySession()
    {
        $_SESSION = array();
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        session_destroy();
    }

    // ==========================================
    // RATE LIMITING
    // ==========================================

    /**
     * Rate limit check
     * 
     * @param string $key
     * @param int $maxAttempts
     * @param int $timeWindow (seconds)
     * @return bool
     */
    public static function rateLimit($key, $maxAttempts = 5, $timeWindow = 60)
    {
        $identifier = self::getClientIdentifier() . '_' . $key;
        
        if (!isset(self::$rateLimitStore[$identifier])) {
            self::$rateLimitStore[$identifier] = array(
                'attempts' => 0,
                'first_attempt' => time()
            );
        }
        
        $data = &self::$rateLimitStore[$identifier];
        
        // Reset if time window passed
        if (time() - $data['first_attempt'] > $timeWindow) {
            $data['attempts'] = 0;
            $data['first_attempt'] = time();
        }
        
        $data['attempts']++;
        
        return $data['attempts'] <= $maxAttempts;
    }

    /**
     * Get client identifier (IP + User Agent)
     * 
     * @return string
     */
    private static function getClientIdentifier()
    {
        $ip = self::getClientIP();
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        return md5($ip . $userAgent);
    }

    /**
     * Get client real IP address
     * 
     * @return string
     */
    public static function getClientIP()
    {
        $ipKeys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );
        
        foreach ($ipKeys as $key) {
            if (isset($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                // Handle multiple IPs (from proxy)
                if (strpos($ip, ',') !== false) {
                    $ips = explode(',', $ip);
                    $ip = trim($ips[0]);
                }
                
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return '0.0.0.0';
    }

    // ==========================================
    // FILE UPLOAD SECURITY
    // ==========================================

    /**
     * Validate file upload
     * 
     * @param array $file $_FILES array
     * @param array $options
     * @return bool|string (true on success, error message on failure)
     */
    public static function validateFileUpload($file, $options = array())
    {
        // Default options
        $defaults = array(
            'max_size' => defined('MAX_UPLOAD_SIZE') ? MAX_UPLOAD_SIZE : 5242880, // 5MB
            'allowed_types' => array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'),
            'allowed_mimes' => array('image/jpeg', 'image/png', 'image/gif', 'application/pdf')
        );
        
        $options = array_merge($defaults, $options);
        
        // Check if file exists
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return 'No file uploaded';
        }
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'Upload error: ' . $file['error'];
        }
        
        // Check file size
        if ($file['size'] > $options['max_size']) {
            return 'File too large. Max size: ' . ($options['max_size'] / 1048576) . 'MB';
        }
        
        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $options['allowed_types'])) {
            return 'File type not allowed. Allowed: ' . implode(', ', $options['allowed_types']);
        }
        
        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $options['allowed_mimes'])) {
            return 'Invalid file type';
        }
        
        // Check if it's actually uploaded file
        if (!is_uploaded_file($file['tmp_name'])) {
            return 'Invalid upload';
        }
        
        return true;
    }

    /**
     * Generate safe filename
     * 
     * @param string $filename
     * @return string
     */
    public static function generateSafeFilename($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        
        // Remove special characters
        $basename = preg_replace('/[^a-zA-Z0-9-_]/', '_', $basename);
        
        // Add timestamp to make unique
        $timestamp = time();
        
        return $basename . '_' . $timestamp . '.' . $extension;
    }

    // ==========================================
    // SECURITY HEADERS
    // ==========================================

    /**
     * Set security headers
     */
    private function setSecurityHeaders()
    {
        if (!headers_sent()) {
            // Prevent clickjacking
            header('X-Frame-Options: SAMEORIGIN');
            
            // XSS Protection
            header('X-XSS-Protection: 1; mode=block');
            
            // Prevent MIME sniffing
            header('X-Content-Type-Options: nosniff');
            
            // Referrer Policy
            header('Referrer-Policy: strict-origin-when-cross-origin');
            
            // Content Security Policy (basic)
            if (defined('APP_ENV') && APP_ENV === 'production') {
                header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'");
            }
            
            // HSTS (only on HTTPS)
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
            }
        }
    }

    // ==========================================
    // PATH TRAVERSAL PROTECTION
    // ==========================================

    /**
     * Sanitize path (prevent directory traversal)
     * 
     * @param string $path
     * @return string
     */
    public static function sanitizePath($path)
    {
        // Remove null bytes
        $path = str_replace(chr(0), '', $path);
        
        // Remove ../ and ..\
        $path = str_replace(array('../', '..\\'), '', $path);
        
        // Remove absolute paths
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        
        return $path;
    }

    // ==========================================
    // UTILITY FUNCTIONS
    // ==========================================

    /**
     * Time-safe string comparison (prevent timing attacks)
     * 
     * @param string $str1
     * @param string $str2
     * @return bool
     */
    public static function hashEquals($str1, $str2)
    {
        if (function_exists('hash_equals')) {
            // PHP 5.6+
            return hash_equals($str1, $str2);
        } else {
            // PHP 5.2-5.5 fallback
            if (strlen($str1) !== strlen($str2)) {
                return false;
            }
            
            $result = 0;
            for ($i = 0; $i < strlen($str1); $i++) {
                $result |= ord($str1[$i]) ^ ord($str2[$i]);
            }
            
            return $result === 0;
        }
    }

    /**
     * Check if request is HTTPS
     * 
     * @return bool
     */
    public static function isHTTPS()
    {
        return (
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
            (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        );
    }

    /**
     * Check if request is AJAX
     * 
     * @return bool
     */
    public static function isAJAX()
    {
        return (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        );
    }

    /**
     * Get request method
     * 
     * @return string
     */
    public static function getRequestMethod()
    {
        return isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
    }
}
