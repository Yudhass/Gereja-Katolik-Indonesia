<?php

/**
 * DatabaseManager
 * Mengelola multiple database connections
 * Compatible with PHP 5.2, 7, 8+
 */

class DatabaseManager
{
    private static $connections = array();
    private static $configs = array();
    
    /**
     * Register database configuration
     * 
     * @param string $name Connection name
     * @param array $config Database configuration
     */
    public static function addConnection($name, $config)
    {
        self::$configs[$name] = $config;
    }
    
    /**
     * Get database connection by name
     * 
     * @param string $name Connection name (default: 'default')
     * @return DatabaseConnection
     */
    public static function connection($name = 'default')
    {
        // Jika connection belum dibuat, buat baru
        if (!isset(self::$connections[$name])) {
            if (!isset(self::$configs[$name])) {
                die("Database configuration for '{$name}' not found.");
            }
            
            self::$connections[$name] = new DatabaseConnection(self::$configs[$name]);
        }
        
        return self::$connections[$name];
    }
    
    /**
     * Get default connection
     */
    public static function getDefaultConnection()
    {
        return self::connection('default');
    }
    
    /**
     * Close all connections
     */
    public static function closeAll()
    {
        foreach (self::$connections as $connection) {
            $connection->closeConnection();
        }
        self::$connections = array();
    }
    
    /**
     * Close specific connection
     */
    public static function close($name)
    {
        if (isset(self::$connections[$name])) {
            self::$connections[$name]->closeConnection();
            unset(self::$connections[$name]);
        }
    }
}

/**
 * DatabaseConnection
 * Handle individual database connection
 * Compatible with PHP 5.2, 7, 8+
 */
class DatabaseConnection
{
    protected $pdo;
    protected $conn;
    protected $usePDO = true;
    protected $config;
    
    public function __construct($config)
    {
        $this->config = $config;
        $this->connect();
    }
    
    private function connect()
    {
        $host = $this->config['host'];
        $user = $this->config['user'];
        $pass = $this->config['pass'];
        $db = $this->config['name'];
        $driver = isset($this->config['driver']) ? $this->config['driver'] : 'mysql';
        
        // Set default port based on driver
        if (isset($this->config['port'])) {
            $port = $this->config['port'];
        } else {
            $port = ($driver === 'pgsql') ? 5432 : 3306;
        }
        
        // Simpan driver untuk digunakan di query methods
        $this->config['driver'] = $driver;
        
        // Coba PDO dulu jika tersedia
        if (class_exists('PDO')) {
            // Cek apakah driver PDO yang dibutuhkan tersedia
            $availableDrivers = PDO::getAvailableDrivers();
            $pdoDriver = ($driver === 'pgsql') ? 'pgsql' : 'mysql';
            
            if (in_array($pdoDriver, $availableDrivers)) {
                try {
                    $this->usePDO = true;
                    $options = array(
                        PDO::ATTR_PERSISTENT => true,
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    );

                    // Build DSN based on driver type
                    if ($driver === 'pgsql') {
                        $dsn = "pgsql:host=$host;port=$port;dbname=$db";
                    } else {
                        $dsn = "mysql:host=$host;port=$port;dbname=$db";
                        // Charset UTF8 hanya untuk PHP 5.3.6+ dan MySQL
                        if (version_compare(PHP_VERSION, '5.3.6', '>=')) {
                            $dsn .= ";charset=utf8";
                        }
                    }
                    
                    $this->pdo = new PDO($dsn, $user, $pass, $options);
                    
                    // Set charset untuk MySQL versi dibawah 5.3.6
                    if ($driver === 'mysql' && version_compare(PHP_VERSION, '5.3.6', '<')) {
                        $this->pdo->exec("SET NAMES utf8");
                    }
                    
                    return $this->pdo;
                } catch (PDOException $e) {
                    die("PDO Connection failed: " . $e->getMessage());
                }
            }
        }
        
        // Fallback ke native functions jika PDO tidak tersedia atau driver tidak ada
        $this->usePDO = false;
        
        if ($driver === 'pgsql') {
            // Gunakan pg_* functions untuk PostgreSQL
            if (!function_exists('pg_connect')) {
                die("PostgreSQL functions (pg_*) not available. Please install php_pgsql extension.");
            }
            
            $connString = "host=$host port=$port dbname=$db user=$user password=$pass";
            $this->conn = pg_connect($connString);
            
            if (!$this->conn) {
                die("PostgreSQL Connection failed: " . pg_last_error());
            }
            
            // Set client encoding to UTF8
            pg_set_client_encoding($this->conn, 'UTF8');
            
            return $this->conn;
        } else {
            // Gunakan mysql_* functions untuk MySQL (PHP 5.2)
            if (!function_exists('mysql_connect')) {
                die("MySQL functions (mysql_*) not available. Please install php_mysql extension.");
            }
            
            $this->conn = mysql_connect("$host:$port", $user, $pass);
            
            if (!$this->conn) {
                die("MySQL Connection failed: " . mysql_error());
            }
            
            $select_db = mysql_select_db($db, $this->conn);
            if (!$select_db) {
                die("Database selection failed: " . mysql_error());
            }
            
            // Set charset
            mysql_query("SET NAMES utf8", $this->conn);
            
            return $this->conn;
        }
    }
    
    public function query($query, $params = array())
    {
        if ($this->usePDO) {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } else {
            $driver = isset($this->config['driver']) ? $this->config['driver'] : 'mysql';
            
            // Replace placeholders dengan escaped values
            foreach ($params as $key => $value) {
                $escapedValue = $this->escapeString($value);
                $query = str_replace(':' . $key, "'" . $escapedValue . "'", $query);
            }
            
            if ($driver === 'pgsql') {
                // Gunakan pg_query untuk PostgreSQL
                $result = pg_query($this->conn, $query);
                if (!$result) {
                    die("PostgreSQL Query failed: " . pg_last_error($this->conn));
                }
                return $result;
            } else {
                // Gunakan mysql_query untuk MySQL
                $result = mysql_query($query, $this->conn);
                if (!$result) {
                    die("MySQL Query failed: " . mysql_error());
                }
                return $result;
            }
        }
    }
    
    public function prepare($sql)
    {
        if ($this->usePDO) {
            return $this->pdo->prepare($sql);
        } else {
            // Untuk PHP 5.2, return query langsung
            return $sql;
        }
    }
    
    public function fetch($stmt)
    {
        if ($this->usePDO) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $driver = isset($this->config['driver']) ? $this->config['driver'] : 'mysql';
            if ($driver === 'pgsql') {
                return pg_fetch_assoc($stmt);
            } else {
                return mysql_fetch_assoc($stmt);
            }
        }
    }
    
    public function fetchAll($stmt)
    {
        if ($this->usePDO) {
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } else {
            $driver = isset($this->config['driver']) ? $this->config['driver'] : 'mysql';
            $data = array();
            if ($driver === 'pgsql') {
                while ($row = pg_fetch_object($stmt)) {
                    $data[] = $row;
                }
            } else {
                while ($row = mysql_fetch_object($stmt)) {
                    $data[] = $row;
                }
            }
            return $data;
        }
    }
    
    public function fetchObject($stmt)
    {
        if ($this->usePDO) {
            return $stmt->fetch(PDO::FETCH_OBJ);
        } else {
            $driver = isset($this->config['driver']) ? $this->config['driver'] : 'mysql';
            if ($driver === 'pgsql') {
                return pg_fetch_object($stmt);
            } else {
                return mysql_fetch_object($stmt);
            }
        }
    }
    
    public function escapeString($value)
    {
        // Handle array - escape setiap element
        if (is_array($value)) {
            $escaped = array();
            foreach ($value as $item) {
                $escaped[] = $this->escapeString($item);
            }
            return $escaped;
        }
        
        // Handle NULL
        if ($value === null) {
            return 'NULL';
        }
        
        // Convert to string jika bukan string
        $value = (string)$value;
        
        if ($this->usePDO) {
            return substr($this->pdo->quote($value), 1, -1);
        } else {
            $driver = isset($this->config['driver']) ? $this->config['driver'] : 'mysql';
            if ($driver === 'pgsql') {
                return pg_escape_string($this->conn, $value);
            } else {
                return mysql_real_escape_string($value, $this->conn);
            }
        }
    }
    
    public function lastInsertId()
    {
        if ($this->usePDO) {
            return $this->pdo->lastInsertId();
        } else {
            $driver = isset($this->config['driver']) ? $this->config['driver'] : 'mysql';
            if ($driver === 'pgsql') {
                // PostgreSQL menggunakan sequence, perlu query khusus
                $result = pg_query($this->conn, "SELECT lastval()");
                if ($result) {
                    $row = pg_fetch_row($result);
                    return $row[0];
                }
                return null;
            } else {
                return mysql_insert_id($this->conn);
            }
        }
    }
    
    public function beginTransaction()
    {
        if ($this->usePDO) {
            return $this->pdo->beginTransaction();
        } else {
            $driver = isset($this->config['driver']) ? $this->config['driver'] : 'mysql';
            if ($driver === 'pgsql') {
                return pg_query($this->conn, "BEGIN");
            } else {
                return mysql_query("START TRANSACTION", $this->conn);
            }
        }
    }

    public function commit()
    {
        if ($this->usePDO) {
            return $this->pdo->commit();
        } else {
            $driver = isset($this->config['driver']) ? $this->config['driver'] : 'mysql';
            if ($driver === 'pgsql') {
                return pg_query($this->conn, "COMMIT");
            } else {
                return mysql_query("COMMIT", $this->conn);
            }
        }
    }

    public function rollback()
    {
        if ($this->usePDO) {
            return $this->pdo->rollBack();
        } else {
            $driver = isset($this->config['driver']) ? $this->config['driver'] : 'mysql';
            if ($driver === 'pgsql') {
                return pg_query($this->conn, "ROLLBACK");
            } else {
                return mysql_query("ROLLBACK", $this->conn);
            }
        }
    }

    public function inTransaction()
    {
        if ($this->usePDO) {
            return $this->pdo->inTransaction();
        }
        return false;
    }

    public function closeConnection()
    {
        if ($this->usePDO) {
            $this->pdo = null;
        } else {
            $driver = isset($this->config['driver']) ? $this->config['driver'] : 'mysql';
            if ($this->conn) {
                if ($driver === 'pgsql') {
                    pg_close($this->conn);
                } else {
                    mysql_close($this->conn);
                }
            }
        }
    }
    
    public function isUsingPDO()
    {
        return $this->usePDO;
    }
    
    public function getPDO()
    {
        return $this->pdo;
    }
    
    public function getConn()
    {
        return $this->conn;
    }
    
    /**
     * Get connection info for debugging
     * @return array
     */
    public function getConnectionInfo()
    {
        $driver = isset($this->config['driver']) ? $this->config['driver'] : 'mysql';
        
        return array(
            'driver' => $driver,
            'host' => $this->config['host'],
            'database' => $this->config['name'],
            'port' => isset($this->config['port']) ? $this->config['port'] : (($driver === 'pgsql') ? 5432 : 3306),
            'using_pdo' => $this->usePDO,
            'php_version' => PHP_VERSION,
            'connection_method' => $this->usePDO ? 'PDO' : ($driver === 'pgsql' ? 'pg_*' : 'mysql_*')
        );
    }
}
