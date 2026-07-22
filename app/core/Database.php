<?php

class Database
{
    private static $instance = null;
    protected $pdo;
    protected $conn;
    protected $usePDO = true;

    public function __construct()
    {
        $this->setConnect();
    }

    public function query($query, $params = array())
    {
        if ($this->usePDO) {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } else {
            // Untuk PHP 5.2 tanpa PDO, gunakan mysql_* functions
            // Ganti placeholders dengan nilai sebenarnya
            foreach ($params as $key => $value) {
                $escapedValue = $this->escapeString($value);
                $query = str_replace(':' . $key, "'" . $escapedValue . "'", $query);
            }
            $result = mysql_query($query, $this->conn);
            return $result;
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

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function setConnect()
    {
        $host = DB_HOST;
        $user = DB_USER;
        $pass = DB_PASS;
        $db = DB_NAME;
        $port = DB_PORT;

        // Cek apakah PDO tersedia (PHP 5.3+)
        if (class_exists('PDO')) {
            try {
                $this->usePDO = true;
                $options = array(
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                );

                $dsn = "mysql:host=$host;port=$port;dbname=$db";
                // Charset UTF8 hanya untuk PHP 5.3.6+
                if (version_compare(PHP_VERSION, '5.3.6', '>=')) {
                    $dsn .= ";charset=utf8";
                }
                
                $this->pdo = new PDO($dsn, $user, $pass, $options);
                
                // Set charset untuk versi dibawah 5.3.6
                if (version_compare(PHP_VERSION, '5.3.6', '<')) {
                    $this->pdo->exec("SET NAMES utf8");
                }
                
                return $this->pdo;
            } catch (PDOException $e) {
                die("PDO Connection failed: " . $e->getMessage());
            }
        } else {
            // Fallback ke mysql_* untuk PHP 5.2
            $this->usePDO = false;
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

    public function getMySQLVersion()
    {
        try {
            if ($this->usePDO) {
                $stmt = $this->pdo->query("SELECT VERSION() as version");
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row ? $row['version'] : '5.0.0';
            } else {
                $result = mysql_query("SELECT VERSION() as version", $this->conn);
                if ($result) {
                    $row = mysql_fetch_assoc($result);
                    return $row ? $row['version'] : '5.0.0';
                }
            }
        } catch (Exception $e) {
            return '5.0.0'; // fallback ke versi paling aman
        }
        return '5.0.0';
    }

    public function escapeString($value)
    {
        if ($this->usePDO) {
            return substr($this->pdo->quote($value), 1, -1);
        } else {
            return mysql_real_escape_string($value, $this->conn);
        }
    }

    public function lastInsertId()
    {
        if ($this->usePDO) {
            return $this->pdo->lastInsertId();
        } else {
            return mysql_insert_id($this->conn);
        }
    }

    public function beginTransaction()
    {
        if ($this->usePDO) {
            return $this->pdo->beginTransaction();
        } else {
            return mysql_query("START TRANSACTION", $this->conn);
        }
    }

    public function commit()
    {
        if ($this->usePDO) {
            return $this->pdo->commit();
        } else {
            return mysql_query("COMMIT", $this->conn);
        }
    }

    public function rollback()
    {
        if ($this->usePDO) {
            return $this->pdo->rollBack();
        } else {
            return mysql_query("ROLLBACK", $this->conn);
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
            if ($this->conn) {
                mysql_close($this->conn);
            }
        }
    }
}
