<?php

/**
 * Env Class - Environment Variables Loader
 * Kompatibel dengan PHP 5.2, 7, 8 dan lebih tinggi
 * 
 * Fungsi: Load dan parse file .env
 */
class Env
{
    private static $variables = array();
    private static $loaded = false;

    /**
     * Load file .env
     * 
     * @param string $path Path ke file .env
     * @return bool
     */
    public static function load($path = null)
    {
        if (self::$loaded) {
            return true;
        }

        if ($path === null) {
            $path = dirname(__FILE__) . '/../../.env';
        }

        if (!file_exists($path)) {
            // Jika .env tidak ada, coba gunakan .env.example
            $examplePath = dirname(__FILE__) . '/../../.env.example';
            if (file_exists($examplePath)) {
                // Copy .env.example ke .env
                copy($examplePath, dirname(__FILE__) . '/../../.env');
                $path = dirname(__FILE__) . '/../../.env';
            } else {
                // Jika tidak ada keduanya, return false
                return false;
            }
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse line
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                
                $name = trim($name);
                $value = trim($value);

                // Remove quotes if present
                if (preg_match('/^(["\'])(.*)\\1$/', $value, $matches)) {
                    $value = $matches[2];
                }

                // Store in variables array
                self::$variables[$name] = $value;

                // Set as environment variable
                if (!getenv($name)) {
                    putenv("$name=$value");
                }

                // Set in $_ENV superglobal
                $_ENV[$name] = $value;

                // Set in $_SERVER superglobal
                $_SERVER[$name] = $value;
            }
        }

        self::$loaded = true;
        return true;
    }

    /**
     * Get environment variable
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }

        // Check in variables array first
        if (isset(self::$variables[$key])) {
            return self::parseValue(self::$variables[$key]);
        }

        // Check in $_ENV
        if (isset($_ENV[$key])) {
            return self::parseValue($_ENV[$key]);
        }

        // Check in $_SERVER
        if (isset($_SERVER[$key])) {
            return self::parseValue($_SERVER[$key]);
        }

        // Check with getenv
        $value = getenv($key);
        if ($value !== false) {
            return self::parseValue($value);
        }

        return $default;
    }

    /**
     * Parse value to appropriate type
     * 
     * @param string $value
     * @return mixed
     */
    private static function parseValue($value)
    {
        if ($value === '') {
            return '';
        }

        // Boolean values
        $lower = strtolower($value);
        if ($lower === 'true' || $lower === '(true)') {
            return true;
        }
        if ($lower === 'false' || $lower === '(false)') {
            return false;
        }

        // Null value
        if ($lower === 'null' || $lower === '(null)') {
            return null;
        }

        // Empty string
        if ($lower === 'empty' || $lower === '(empty)') {
            return '';
        }

        // Numeric values
        if (is_numeric($value)) {
            return $value + 0; // Convert to int or float automatically
        }

        return $value;
    }

    /**
     * Check if environment variable exists
     * 
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        if (!self::$loaded) {
            self::load();
        }

        return isset(self::$variables[$key]) || 
               isset($_ENV[$key]) || 
               isset($_SERVER[$key]) || 
               getenv($key) !== false;
    }

    /**
     * Get all environment variables
     * 
     * @return array
     */
    public static function all()
    {
        if (!self::$loaded) {
            self::load();
        }

        return self::$variables;
    }

    /**
     * Set environment variable
     * 
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value)
    {
        self::$variables[$key] = $value;
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

/**
 * Helper function untuk akses environment variable
 * Kompatibel dengan PHP 5.2+
 * 
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function env($key, $default = null)
{
    return Env::get($key, $default);
}
