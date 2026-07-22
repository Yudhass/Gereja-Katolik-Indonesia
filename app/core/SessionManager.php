<?php

/**
 * SessionManager
 * 
 * Mengelola server-side session store di database.
 * Browser hanya menyimpan session_id (cookie), sementara
 * validitas session sepenuhnya dikontrol oleh record di tabel
 * `user_sessions` pada database.
 *
 * Alur keamanan:
 *  1. Login  → SessionManager::create() menyimpan record ke DB
 *  2. Request → SessionManager::validate() memastikan record ada & tidak kadaluarsa
 *  3. Logout  → SessionManager::destroy() menghapus record dari DB
 *  4. Expired → Record otomatis tidak valid; browser diarahkan ke logout
 */
class SessionManager
{
    /** Nama tabel session di database */
    const TABLE = 'user_sessions';

    // ----------------------------------------------------------------
    // CRUD Session Record
    // ----------------------------------------------------------------

    /**
     * Simpan session baru ke database setelah login berhasil.
     *
     * @param  array  $userData   Data user dari $_SESSION['user']
     * @param  string $sessionId  session_id() saat ini
     * @return bool
     */
    public static function create(array $userData, $sessionId)
    {
        try {
            $db  = self::getDB();
            $now = time();

            // Hapus session lama milik user yang sama di device/IP ini
            // (opsional – aktifkan jika satu user hanya boleh 1 sesi aktif)
            // self::destroyAllForUser($userData['id']);

            $sql = "INSERT INTO " . self::TABLE . "
                        (session_id, user_id, username, role,
                         ip_address, user_agent,
                         last_activity, created_at, expires_at)
                    VALUES
                        (:session_id, :user_id, :username, :role,
                         :ip_address, :user_agent,
                         :last_activity, :created_at, :expires_at)
                    ON DUPLICATE KEY UPDATE
                        user_id       = VALUES(user_id),
                        username      = VALUES(username),
                        role          = VALUES(role),
                        ip_address    = VALUES(ip_address),
                        user_agent    = VALUES(user_agent),
                        last_activity = VALUES(last_activity),
                        expires_at    = VALUES(expires_at)";

            $params = array(
                ':session_id'    => $sessionId,
                ':user_id'       => $userData['id'],
                ':username'      => $userData['username'],
                ':role'          => isset($userData['role_nama']) ? $userData['role_nama'] : '',
                ':ip_address'    => self::getIpAddress(),
                ':user_agent'    => self::getUserAgent(),
                ':last_activity' => $now,
                ':created_at'    => $now,
                ':expires_at'    => $now + SESSION_LIFETIME,
            );

            $stmt = $db->prepare($sql);
            if ($stmt instanceof PDOStatement) {
                $stmt->execute($params);
                return true;
            }

            // Fallback mysql_* (PHP 5.2) – query sudah di-escape
            return self::legacyInsert($params);

        } catch (Exception $e) {
            self::log('SessionManager::create error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validasi session: cek apakah session_id terdaftar di DB
     * dan belum kadaluarsa.
     *
     * @param  string $sessionId
     * @return bool
     */
    public static function validate($sessionId)
    {
        if (empty($sessionId)) {
            return false;
        }

        try {
            $db  = self::getDB();
            $now = time();

            $sql  = "SELECT session_id, expires_at FROM " . self::TABLE . "
                     WHERE session_id = :session_id
                     LIMIT 1";
            $stmt = $db->prepare($sql);

            if ($stmt instanceof PDOStatement) {
                $stmt->execute(array(':session_id' => $sessionId));
                $row = $stmt->fetch(PDO::FETCH_OBJ);

                if (!$row) {
                    return false; // Tidak ada di DB → session tidak valid
                }

                if ((int)$row->expires_at < $now) {
                    // Sudah kadaluarsa → hapus record stale
                    self::destroy($sessionId);
                    return false;
                }

                return true;
            }

            // Fallback PHP 5.2 – mysql_*
            return self::legacyValidate($sessionId, $now);

        } catch (Exception $e) {
            self::log('SessionManager::validate error: ' . $e->getMessage());
            // Jika DB error, TOLAK akses (fail-secure)
            return false;
        }
    }

    /**
     * Perbarui last_activity dan perpanjang expires_at.
     * Dipanggil di setiap request yang valid.
     *
     * @param  string $sessionId
     * @return void
     */
    public static function touch($sessionId)
    {
        try {
            $db  = self::getDB();
            $now = time();

            $sql  = "UPDATE " . self::TABLE . "
                     SET last_activity = :last_activity,
                         expires_at    = :expires_at
                     WHERE session_id  = :session_id";
            $stmt = $db->prepare($sql);

            if ($stmt instanceof PDOStatement) {
                $stmt->execute(array(
                    ':last_activity' => $now,
                    ':expires_at'    => $now + SESSION_LIFETIME,
                    ':session_id'    => $sessionId,
                ));
            }
        } catch (Exception $e) {
            self::log('SessionManager::touch error: ' . $e->getMessage());
        }
    }

    /**
     * Hapus session dari DB (logout).
     *
     * @param  string $sessionId
     * @return void
     */
    public static function destroy($sessionId)
    {
        try {
            $db   = self::getDB();
            $sql  = "DELETE FROM " . self::TABLE . " WHERE session_id = :session_id";
            $stmt = $db->prepare($sql);

            if ($stmt instanceof PDOStatement) {
                $stmt->execute(array(':session_id' => $sessionId));
            }
        } catch (Exception $e) {
            self::log('SessionManager::destroy error: ' . $e->getMessage());
        }
    }

    /**
     * Hapus SEMUA session milik user tertentu (force logout semua device).
     *
     * @param  int $userId
     * @return void
     */
    public static function destroyAllForUser($userId)
    {
        try {
            $db   = self::getDB();
            $sql  = "DELETE FROM " . self::TABLE . " WHERE user_id = :user_id";
            $stmt = $db->prepare($sql);

            if ($stmt instanceof PDOStatement) {
                $stmt->execute(array(':user_id' => (int)$userId));
            }
        } catch (Exception $e) {
            self::log('SessionManager::destroyAllForUser error: ' . $e->getMessage());
        }
    }

    /**
     * Perbarui session_id di DB setelah session_regenerate_id().
     *
     * @param  string $oldSessionId
     * @param  string $newSessionId
     * @return void
     */
    public static function regenerate($oldSessionId, $newSessionId)
    {
        try {
            $db   = self::getDB();
            $now  = time();
            $sql  = "UPDATE " . self::TABLE . "
                     SET session_id    = :new_session_id,
                         last_activity = :last_activity,
                         expires_at    = :expires_at
                     WHERE session_id  = :old_session_id";
            $stmt = $db->prepare($sql);

            if ($stmt instanceof PDOStatement) {
                $stmt->execute(array(
                    ':new_session_id' => $newSessionId,
                    ':last_activity'  => $now,
                    ':expires_at'     => $now + SESSION_LIFETIME,
                    ':old_session_id' => $oldSessionId,
                ));
            }
        } catch (Exception $e) {
            self::log('SessionManager::regenerate error: ' . $e->getMessage());
        }
    }

    /**
     * Ambil semua session aktif milik seorang user.
     *
     * @param  int   $userId
     * @return array
     */
    public static function getActiveSessionsByUser($userId)
    {
        try {
            $db   = self::getDB();
            $now  = time();
            $sql  = "SELECT * FROM " . self::TABLE . "
                     WHERE user_id  = :user_id
                       AND expires_at > :now
                     ORDER BY last_activity DESC";
            $stmt = $db->prepare($sql);

            if ($stmt instanceof PDOStatement) {
                $stmt->execute(array(':user_id' => (int)$userId, ':now' => $now));
                return $stmt->fetchAll(PDO::FETCH_OBJ);
            }
        } catch (Exception $e) {
            self::log('SessionManager::getActiveSessionsByUser error: ' . $e->getMessage());
        }

        return array();
    }

    /**
     * Hapus semua session yang sudah kadaluarsa dari DB.
     * Panggil ini secara berkala (misal: via cron / probabilistic cleanup).
     *
     * @return void
     */
    public static function cleanupExpired()
    {
        try {
            $db   = self::getDB();
            $now  = time();
            $sql  = "DELETE FROM " . self::TABLE . " WHERE expires_at < :now";
            $stmt = $db->prepare($sql);

            if ($stmt instanceof PDOStatement) {
                $stmt->execute(array(':now' => $now));
            }
        } catch (Exception $e) {
            self::log('SessionManager::cleanupExpired error: ' . $e->getMessage());
        }
    }

    // ----------------------------------------------------------------
    // Internal helpers
    // ----------------------------------------------------------------

    /**
     * Ambil koneksi PDO dari DatabaseManager (default connection).
     *
     * @return PDO
     */
    private static function getDB()
    {
        $conn = DatabaseManager::connection('default');
        return $conn->getPDO();
    }

    /**
     * Catat error ke log file.
     *
     * @param string $message
     */
    private static function log($message)
    {
        $logDir  = dirname(__FILE__) . '/../../storage/logs/';
        $logFile = $logDir . 'session_errors.log';

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    }

    /**
     * Dapatkan IP address pengunjung.
     *
     * @return string
     */
    private static function getIpAddress()
    {
        $headers = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR',
        );
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $parts = explode(',', $_SERVER[$header]);
                $ip    = trim($parts[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return '0.0.0.0';
    }

    /**
     * Dapatkan User-Agent string (dibatasi 500 karakter).
     *
     * @return string
     */
    private static function getUserAgent()
    {
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        return substr($ua, 0, 500);
    }

    // ----------------------------------------------------------------
    // PHP 5.2 Legacy Fallback (mysql_*)
    // ----------------------------------------------------------------

    /**
     * Helper: escape string untuk PHP 5.2 (mysql_*)
     * Tidak pakai closure agar kompatibel dengan PHP 5.2.
     */
    private static function legacyEscape($value, $conn)
    {
        return mysql_real_escape_string($value, $conn);
    }

    private static function legacyInsert(array $params)
    {
        $conn = DatabaseManager::connection('default')->getConn();

        $sid  = self::legacyEscape($params[':session_id'],  $conn);
        $uid  = (int)$params[':user_id'];
        $uname = self::legacyEscape($params[':username'],   $conn);
        $role  = self::legacyEscape($params[':role'],       $conn);
        $ip    = self::legacyEscape($params[':ip_address'], $conn);
        $ua    = self::legacyEscape($params[':user_agent'], $conn);
        $la    = (int)$params[':last_activity'];
        $ca    = (int)$params[':created_at'];
        $ea    = (int)$params[':expires_at'];

        $sql = "INSERT INTO " . self::TABLE . "
                    (session_id, user_id, username, role,
                     ip_address, user_agent, last_activity, created_at, expires_at)
                VALUES
                    ('$sid', '$uid', '$uname', '$role',
                     '$ip', '$ua', '$la', '$ca', '$ea')
                ON DUPLICATE KEY UPDATE
                     user_id       = VALUES(user_id),
                     last_activity = VALUES(last_activity),
                     expires_at    = VALUES(expires_at)";

        return (bool)mysql_query($sql, $conn);
    }

    private static function legacyValidate($sessionId, $now)
    {
        $conn = DatabaseManager::connection('default')->getConn();
        $sid  = mysql_real_escape_string($sessionId, $conn);
        $sql  = "SELECT expires_at FROM " . self::TABLE . "
                 WHERE session_id = '$sid' LIMIT 1";
        $res  = mysql_query($sql, $conn);
        if (!$res) {
            return false;
        }
        $row = mysql_fetch_object($res);
        if (!$row) {
            return false;
        }
        if ((int)$row->expires_at < $now) {
            self::destroy($sessionId);
            return false;
        }
        return true;
    }
}
