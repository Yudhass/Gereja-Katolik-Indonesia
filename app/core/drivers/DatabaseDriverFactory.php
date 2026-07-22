<?php

/**
 * Database Driver Factory
 * 
 * Factory untuk membuat database driver berdasarkan konfigurasi
 * Compatible with PHP 5.2, 7, 8+
 * 
 * @package Core\Drivers
 */
class DatabaseDriverFactory
{
    /**
     * Create database driver berdasarkan config
     * 
     * @param array $config Database configuration
     * @return DatabaseDriverInterface
     */
    public static function create($config)
    {
        // Tentukan driver type
        $driver = isset($config['driver']) ? strtolower($config['driver']) : 'mysql';
        
        // Load driver interface
        self::loadDriverInterface();
        
        // Cek apakah PDO tersedia
        $usePDO = class_exists('PDO');
        
        // Pilih driver yang sesuai
        if ($driver === 'pgsql' || $driver === 'postgresql' || $driver === 'postgres') {
            if ($usePDO && extension_loaded('pdo_pgsql')) {
                // PDO PostgreSQL (PHP 5.3+)
                require_once dirname(__FILE__) . '/PostgreSQLPDODriver.php';
                return new PostgreSQLPDODriver();
            } elseif (function_exists('pg_connect')) {
                // PostgreSQL Legacy (PHP 5.2 dengan pg_* functions)
                require_once dirname(__FILE__) . '/PostgreSQLLegacyDriver.php';
                return new PostgreSQLLegacyDriver();
            } else {
                die("PostgreSQL driver not available. Install php_pgsql or pdo_pgsql extension.");
            }
        } else {
            // Default MySQL
            if ($usePDO && extension_loaded('pdo_mysql')) {
                // PDO MySQL (PHP 5.3+)
                require_once dirname(__FILE__) . '/MySQLPDODriver.php';
                return new MySQLPDODriver();
            } elseif (function_exists('mysql_connect')) {
                // MySQL Legacy (PHP 5.2 dengan mysql_* functions)
                require_once dirname(__FILE__) . '/MySQLLegacyDriver.php';
                return new MySQLLegacyDriver();
            } elseif (function_exists('mysqli_connect')) {
                // MySQLi fallback
                require_once dirname(__FILE__) . '/MySQLiDriver.php';
                return new MySQLiDriver();
            } else {
                die("MySQL driver not available. Install php_mysql, mysqli, or pdo_mysql extension.");
            }
        }
    }
    
    /**
     * Load driver interface if not loaded
     */
    private static function loadDriverInterface()
    {
        if (!interface_exists('DatabaseDriverInterface')) {
            require_once dirname(__FILE__) . '/DatabaseDriverInterface.php';
        }
    }
    
    /**
     * Get available drivers
     * 
     * @return array Available driver names
     */
    public static function getAvailableDrivers()
    {
        $drivers = array();
        
        // Check MySQL drivers
        if (class_exists('PDO') && extension_loaded('pdo_mysql')) {
            $drivers[] = 'mysql (PDO)';
        }
        if (function_exists('mysqli_connect')) {
            $drivers[] = 'mysql (MySQLi)';
        }
        if (function_exists('mysql_connect')) {
            $drivers[] = 'mysql (Legacy)';
        }
        
        // Check PostgreSQL drivers
        if (class_exists('PDO') && extension_loaded('pdo_pgsql')) {
            $drivers[] = 'pgsql (PDO)';
        }
        if (function_exists('pg_connect')) {
            $drivers[] = 'pgsql (pg_*)';
        }
        
        return $drivers;
    }
    
    /**
     * Check if driver is available
     * 
     * @param string $driver Driver name (mysql, pgsql)
     * @return bool True if available
     */
    public static function isDriverAvailable($driver)
    {
        $driver = strtolower($driver);
        
        if ($driver === 'mysql') {
            return (class_exists('PDO') && extension_loaded('pdo_mysql')) ||
                   function_exists('mysqli_connect') ||
                   function_exists('mysql_connect');
        } elseif ($driver === 'pgsql' || $driver === 'postgresql') {
            return (class_exists('PDO') && extension_loaded('pdo_pgsql')) ||
                   function_exists('pg_connect');
        }
        
        return false;
    }
}
