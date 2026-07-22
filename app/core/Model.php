<?php
require_once 'Database.php';
require_once 'DatabaseManager.php';

class Model extends Database
{
    protected $table;
    protected $fields = array();
    protected $query;
    protected $bindings = array();
    protected $connection = 'default'; // Default connection name
    protected $db; // DatabaseConnection instance

    public function __construct()
    {
        // Jika menggunakan connection khusus, ambil dari DatabaseManager
        if ($this->connection !== 'default') {
            $this->db = DatabaseManager::connection($this->connection);
            $this->usePDO = $this->db->isUsingPDO();
            if ($this->usePDO) {
                $this->pdo = $this->db->getPDO();
            } else {
                $this->conn = $this->db->getConn();
            }
        } else {
            // Gunakan default connection (parent Database)
            parent::__construct();
            $this->db = $this;
        }
    }
    
    /**
     * Set connection name untuk model
     * Usage: $model->setConnection('karyawan');
     */
    public function setConnection($connectionName)
    {
        $this->connection = $connectionName;
        $this->db = DatabaseManager::connection($connectionName);
        $this->usePDO = $this->db->isUsingPDO();
        if ($this->usePDO) {
            $this->pdo = $this->db->getPDO();
        } else {
            $this->conn = $this->db->getConn();
        }
        return $this;
    }
    
    /**
     * Get database connection
     */
    protected function getConnection()
    {
        return $this->db;
    }

    // INSERT - Tambah data baru
    public function insert($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        if ($this->usePDO) {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            return $this->find($this->pdo->lastInsertId());
        } else {
            // Untuk PHP 5.2
            foreach ($data as $key => $value) {
                $escapedValue = $this->escapeString($value);
                $sql = str_replace(':' . $key, "'" . $escapedValue . "'", $sql);
            }
            $result = mysql_query($sql, $this->conn);
            if ($result) {
                return $this->find(mysql_insert_id($this->conn));
            }
            return false;
        }
    }

    // Alias untuk insert (untuk backward compatibility)
    public function create($data)
    {
        return $this->insert($data);
    }

    // SELECT ALL - Ambil semua data
    public function selectAll()
    {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($this->usePDO) {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } else {
            // Untuk PHP 5.2
            $result = mysql_query($sql, $this->conn);
            $data = array();
            while ($row = mysql_fetch_object($result)) {
                $data[] = $row;
            }
            return $data;
        }
    }

    // Alias untuk selectAll (untuk backward compatibility)
    public function all()
    {
        return $this->selectAll();
    }

    // SELECT ONE - Ambil satu data berdasarkan ID
    public function selectOne($id)
    {
        return $this->find($id);
    }

    // SELECT WHERE - Ambil data dengan kondisi where
    public function selectWhere($column, $value, $operator = '=')
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} :value";
        
        if ($this->usePDO) {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(':value' => $value));
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } else {
            // Untuk PHP 5.2
            $escapedValue = $this->escapeString($value);
            $sql = str_replace(':value', "'" . $escapedValue . "'", $sql);
            $result = mysql_query($sql, $this->conn);
            $data = array();
            while ($row = mysql_fetch_object($result)) {
                $data[] = $row;
            }
            return $data;
        }
    }

    // WHERE clause builder
    public function where($column, $value, $operator = '=')
    {
        // Buat parameter name yang valid (ganti titik, spasi, dan karakter special dengan underscore)
        $paramName = str_replace(array('.', ' ', '-', '(', ')'), '_', $column);
        
        // Jika parameter name sudah ada, tambahkan counter
        $originalParamName = $paramName;
        $counter = 1;
        while (isset($this->bindings[$paramName])) {
            $paramName = $originalParamName . '_' . $counter;
            $counter++;
        }
        
        if (empty($this->query)) {
            $this->query = "SELECT * FROM {$this->table} WHERE {$column} {$operator} :{$paramName}";
        } else {
            $this->query .= " AND {$column} {$operator} :{$paramName}";
        }
        $this->bindings[$paramName] = $value;
        return $this;
    }
    
    /**
     * WHERE LIKE clause
     * Usage: 
     * - $model->whereLike('nama', 'contoh')->first(); // satu data
     * - $model->whereLike('nama', 'contoh')->get(); // banyak data
     * 
     * @param string $column Column name
     * @param mixed $value Value to search (wildcards % will be added automatically)
     * @return Model
     */
    public function whereLike($column, $value)
    {
        // Buat parameter name yang valid
        $paramName = str_replace(array('.', ' ', '-', '(', ')'), '_', $column);
        
        // Jika parameter name sudah ada, tambahkan counter
        $originalParamName = $paramName;
        $counter = 1;
        while (isset($this->bindings[$paramName])) {
            $paramName = $originalParamName . '_' . $counter;
            $counter++;
        }
        
        if (empty($this->query)) {
            $this->query = "SELECT * FROM {$this->table} WHERE {$column} LIKE :{$paramName}";
        } else {
            $this->query .= " AND {$column} LIKE :{$paramName}";
        }
        
        // Tambahkan wildcard % otomatis untuk contains search
        $this->bindings[$paramName] = '%' . $value . '%';
        return $this;
    }
    
    /**
     * WHERE NOT EQUAL clause
     * Usage: $model->whereNotEqual('id', 0)->get();
     * atau gunakan: $model->where('id', 0, '!=')->get();
     * 
     * @param string $column Column name
     * @param mixed $value Value to compare
     * @return Model
     */
    public function whereNotEqual($column, $value)
    {
        return $this->where($column, $value, '!=');
    }
    
    /**
     * WHERE NOT IN clause
     * Usage: $model->whereNotIn('status', array('deleted', 'banned'))->get();
     * 
     * @param string $column Column name
     * @param array $values Array of values
     * @return Model
     */
    public function whereNotIn($column, $values)
    {
        if (empty($this->query)) {
            $this->query = "SELECT * FROM {$this->table} WHERE {$column} NOT IN (";
        } else {
            $this->query .= " AND {$column} NOT IN (";
        }
        
        // Build placeholders dengan nama yang valid
        $placeholders = array();
        $paramBase = str_replace(array('.', ' ', '-', '(', ')'), '_', $column);
        foreach ($values as $index => $value) {
            $key = $paramBase . '_' . $index;
            $placeholders[] = ':' . $key;
            $this->bindings[$key] = $value;
        }
        
        $this->query .= implode(', ', $placeholders) . ')';
        return $this;
    }
    
    /**
     * WHERE IN clause
     * Usage: $model->whereIn('status', array('active', 'pending'))->get();
     * 
     * @param string $column Column name
     * @param array $values Array of values
     * @return Model
     */
    public function whereIn($column, $values)
    {
        if (empty($this->query)) {
            $this->query = "SELECT * FROM {$this->table} WHERE {$column} IN (";
        } else {
            $this->query .= " AND {$column} IN (";
        }
        
        // Build placeholders dengan nama yang valid
        $placeholders = array();
        $paramBase = str_replace(array('.', ' ', '-', '(', ')'), '_', $column);
        foreach ($values as $index => $value) {
            $key = $paramBase . '_' . $index;
            $placeholders[] = ':' . $key;
            $this->bindings[$key] = $value;
        }
        
        $this->query .= implode(', ', $placeholders) . ')';
        return $this;
    }
    
    /**
     * WHERE NOT NULL clause
     * Usage: $model->whereNotNull('deleted_at')->get();
     * 
     * @param string $column Column name
     * @return Model
     */
    public function whereNotNull($column)
    {
        if (empty($this->query)) {
            $this->query = "SELECT * FROM {$this->table} WHERE {$column} IS NOT NULL";
        } else {
            $this->query .= " AND {$column} IS NOT NULL";
        }
        return $this;
    }
    
    /**
     * WHERE NULL clause
     * Usage: $model->whereNull('deleted_at')->get();
     * 
     * @param string $column Column name
     * @return Model
     */
    public function whereNull($column)
    {
        if (empty($this->query)) {
            $this->query = "SELECT * FROM {$this->table} WHERE {$column} IS NULL";
        } else {
            $this->query .= " AND {$column} IS NULL";
        }
        return $this;
    }

    // Execute get - Ambil multiple results
    public function get()
    {
        if ($this->usePDO) {
            $stmt = $this->pdo->prepare($this->query);
            $stmt->execute($this->bindings);
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        } else {
            // Untuk PHP 5.2
            $sql = $this->query;
            foreach ($this->bindings as $key => $value) {
                $escapedValue = $this->escapeString($value);
                $sql = str_replace(':' . $key, "'" . $escapedValue . "'", $sql);
            }
            $result = mysql_query($sql, $this->conn);
            $data = array();
            while ($row = mysql_fetch_object($result)) {
                $data[] = $row;
            }
            $result = $data;
        }
        
        // Reset query builder
        $this->query = '';
        $this->bindings = array();
        
        return $result;
    }

    // Execute first - Ambil satu result pertama
    public function first()
    {
        $this->query .= " LIMIT 1";
        
        if ($this->usePDO) {
            $stmt = $this->pdo->prepare($this->query);
            $stmt->execute($this->bindings);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
        } else {
            // Untuk PHP 5.2
            $sql = $this->query;
            foreach ($this->bindings as $key => $value) {
                $escapedValue = $this->escapeString($value);
                $sql = str_replace(':' . $key, "'" . $escapedValue . "'", $sql);
            }
            $result = mysql_query($sql, $this->conn);
            $result = mysql_fetch_object($result);
        }
        
        // Reset query builder
        $this->query = '';
        $this->bindings = array();
        
        return $result;
    }

    // UPDATE - Update data (memerlukan id di dalam array $data)
    public function update($data, $id = null)
    {
        // Jika $data bukan array, return false
        if (!is_array($data)) {
            return false;
        }

        // Jika id dikirim sebagai parameter kedua (untuk backward compatibility)
        if ($id !== null) {
            $data['id'] = $id;
        }

        // Pastikan 'id' selalu ada di array $data
        if (!isset($data['id'])) {
            return false;
        }

        $setClause = '';
        foreach (array_keys($data) as $key) {
            if ($key !== 'id') {
                $setClause .= "{$key} = :{$key}, ";
            }
        }
        $setClause = rtrim($setClause, ', ');

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = :id";

        if ($this->usePDO) {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute($data) === false) {
                return false;
            }
            return $stmt->rowCount();
        } else {
            // Untuk PHP 5.2
            foreach ($data as $key => $value) {
                $escapedValue = $this->escapeString($value);
                $sql = str_replace(':' . $key, "'" . $escapedValue . "'", $sql);
            }
            $result = mysql_query($sql, $this->conn);
            if ($result === false) {
                return false;
            }
            return mysql_affected_rows($this->conn);
        }
    }

    // UPDATE BY ID - Update data berdasarkan ID
    public function updateById($id, $data)
    {
        $data['id'] = $id;
        return $this->update($data);
    }

    // DELETE - Hapus data berdasarkan ID
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";

        if ($this->usePDO) {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(':id' => $id));
            return $stmt->rowCount();
        } else {
            // Untuk PHP 5.2
            $escapedId = $this->escapeString($id);
            $sql = str_replace(':id', "'" . $escapedId . "'", $sql);
            $result = mysql_query($sql, $this->conn);
            return mysql_affected_rows($this->conn);
        }
    }

    // DELETE BY ID - Alias untuk delete
    public function deleteById($id)
    {
        return $this->delete($id);
    }

    // FIND - Cari data berdasarkan ID
    public function beginTransaction()
    {
        if ($this->connection === 'default') {
            return parent::beginTransaction();
        }
        return $this->getConnection()->beginTransaction();
    }

    public function commit()
    {
        if ($this->connection === 'default') {
            return parent::commit();
        }
        return $this->getConnection()->commit();
    }

    public function rollback()
    {
        if ($this->connection === 'default') {
            return parent::rollback();
        }
        return $this->getConnection()->rollback();
    }

    public function inTransaction()
    {
        if ($this->connection === 'default') {
            return parent::inTransaction();
        }
        return $this->getConnection()->inTransaction();
    }

    /**
     * Run a callback inside a database transaction.
     * 
     * Usage:
     *   $model->transaction(function() use ($model, $data) {
     *       $model->insert($data);
     *       $model->update($otherData, $id);
     *   });
     * 
     * @param callable $callback Function to execute within transaction
     * @return mixed Return value of the callback
     * @throws Exception
     */
    public function transaction($callback)
    {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        
        if ($this->usePDO) {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array('id' => $id));
            return $stmt->fetch(PDO::FETCH_OBJ);
        } else {
            // Untuk PHP 5.2
            $escapedId = $this->escapeString($id);
            $sql = str_replace(':id', "'" . $escapedId . "'", $sql);
            $result = mysql_query($sql, $this->conn);
            return mysql_fetch_object($result);
        }
    }
    
    /**
     * Raw Query - Execute raw SQL query and return results
     * 
     * @param string $sql SQL query
     * @param array $params Parameter bindings (optional)
     * @return array Array of objects
     */
    public function rawQuery($sql, $params = array())
    {
        if ($this->usePDO) {
            $stmt = $this->pdo->prepare($sql);
            
            // Bind parameters if provided
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    // Check if key is numeric (positional) or string (named)
                    if (is_numeric($key)) {
                        $stmt->bindValue($key + 1, $value);
                    } else {
                        $stmt->bindValue(':' . ltrim($key, ':'), $value);
                    }
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } else {
            // Untuk PHP 5.2 - escape manual
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $escapedValue = $this->escapeString($value);
                    if (is_numeric($key)) {
                        // Positional parameters (?)
                        $sql = preg_replace('/\?/', "'" . $escapedValue . "'", $sql, 1);
                    } else {
                        // Named parameters (:name)
                        $sql = str_replace(':' . ltrim($key, ':'), "'" . $escapedValue . "'", $sql);
                    }
                }
            }
            
            $result = mysql_query($sql, $this->conn);
            $data = array();
            
            if ($result) {
                while ($row = mysql_fetch_object($result)) {
                    $data[] = $row;
                }
            }
            
            return $data;
        }
    }
    
    /**
     * Execute raw query and return first result
     * 
     * @param string $sql SQL query
     * @param array $params Parameter bindings
     * @return object|null Single object or null
     */
    public function rawQueryFirst($sql, $params = array())
    {
        $results = $this->rawQuery($sql, $params);
        return !empty($results) ? $results[0] : null;
    }
    
    /**
     * JOIN - Add JOIN clause to query builder (Laravel style)
     * 
     * @param string $table Table to join
     * @param string $first First column
     * @param string $operator Operator (=, !=, >, <, dll) atau second column jika 3 parameter
     * @param string $second Second column (optional)
     * @param string $type Join type (INNER, LEFT, RIGHT)
     * @return $this
     */
    public function join($table, $first, $operator = '=', $second = null, $type = 'INNER')
    {
        // Jika hanya 3 parameter: join('table', 'col1', 'col2')
        if ($second === null) {
            $second = $operator;
            $operator = '=';
        }
        
        if (empty($this->query)) {
            $this->query = "SELECT * FROM {$this->table}";
        }
        
        $this->query .= " {$type} JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }
    
    /**
     * LEFT JOIN
     */
    public function leftJoin($table, $first, $operator = '=', $second = null)
    {
        if ($second === null) {
            $second = $operator;
            $operator = '=';
        }
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }
    
    /**
     * RIGHT JOIN
     */
    public function rightJoin($table, $first, $operator = '=', $second = null)
    {
        if ($second === null) {
            $second = $operator;
            $operator = '=';
        }
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }
    
    /**
     * SELECT - Specify columns to select
     * 
     * @param string $columns Columns to select
     * @return $this
     */
    public function select($columns = '*')
    {
        $this->query = "SELECT {$columns} FROM {$this->table}";
        return $this;
    }
    
    /**
     * OR WHERE - Add OR WHERE clause
     * 
     * @param string $column Column name
     * @param string $operator Operator
     * @param mixed $value Value
     * @return $this
     */
    public function orWhere($column, $operator, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        if (empty($this->query)) {
            $this->query = "SELECT * FROM {$this->table}";
        }
        
        if (stripos($this->query, 'WHERE') === false) {
            $this->query .= " WHERE {$column} {$operator} '" . $this->escapeString($value) . "'";
        } else {
            $this->query .= " OR {$column} {$operator} '" . $this->escapeString($value) . "'";
        }
        
        return $this;
    }
    
    /**
     * ORDER BY - Add ORDER BY clause
     * 
     * @param string $column Column name
     * @param string $direction ASC atau DESC (default: ASC)
     * @return $this
     */
    public function orderBy($column, $direction = 'ASC')
    {
        if (empty($this->query)) {
            $this->query = "SELECT * FROM {$this->table}";
        }
        
        $this->query .= " ORDER BY {$column} {$direction}";
        return $this;
    }
    
    /**
     * LIMIT - Add LIMIT clause
     * 
     * @param int $limit Limit number
     * @param int $offset Offset (optional)
     * @return $this
     */
    public function limit($limit, $offset = 0)
    {
        if (empty($this->query)) {
            $this->query = "SELECT * FROM {$this->table}";
        }
        
        if ($offset > 0) {
            $this->query .= " LIMIT {$offset}, {$limit}";
        } else {
            $this->query .= " LIMIT {$limit}";
        }
        
        return $this;
    }
    
    /**
     * COUNT - Get total count of records
     * Usage:
     * - $model->count() // count all records
     * - $model->where('status', 'active')->count() // count with condition
     * - $model->select('id')->where('role', 'admin')->count() // count with complex query
     * 
     * Note: For counting results from all(), use PHP's count() function:
     * - count($model->all()) // count all records
     * - count($model->where('status', 'active')->get()) // count filtered records
     * 
     * @return int
     */
    public function count()
    {
        // If there's an existing query, modify it for counting
        if (!empty($this->query)) {
            // Replace SELECT clause with SELECT COUNT(*)
            $countQuery = preg_replace('/^SELECT\s+.+?\s+FROM/i', 'SELECT COUNT(*) as total FROM', $this->query);
            
            // Remove ORDER BY clause (not needed for count)
            $countQuery = preg_replace('/\s+ORDER\s+BY\s+.+?(?=\s+LIMIT|\s*$)/i', '', $countQuery);
            
            // Remove LIMIT clause (not needed for count)
            $countQuery = preg_replace('/\s+LIMIT\s+.+$/i', '', $countQuery);
            
            if ($this->usePDO) {
                $stmt = $this->pdo->prepare($countQuery);
                $stmt->execute($this->bindings);
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                $count = isset($result->total) ? (int)$result->total : 0;
            } else {
                // For PHP 5.2
                $sql = $countQuery;
                foreach ($this->bindings as $key => $value) {
                    $escapedValue = $this->escapeString($value);
                    $sql = str_replace(':' . $key, "'" . $escapedValue . "'", $sql);
                }
                
                $result = mysql_query($sql, $this->conn);
                if ($result) {
                    $row = mysql_fetch_object($result);
                    $count = isset($row->total) ? (int)$row->total : 0;
                } else {
                    $count = 0;
                }
            }
            
            // Reset query builder
            $this->query = '';
            $this->bindings = array();
            
            return $count;
        } else {
            // No query built yet, count all records in table
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            
            if ($this->usePDO) {
                $stmt = $this->pdo->query($sql);
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                return isset($result->total) ? (int)$result->total : 0;
            } else {
                // For PHP 5.2
                $result = mysql_query($sql, $this->conn);
                if ($result) {
                    $row = mysql_fetch_object($result);
                    return isset($row->total) ? (int)$row->total : 0;
                }
                return 0;
            }
        }
    }
}

