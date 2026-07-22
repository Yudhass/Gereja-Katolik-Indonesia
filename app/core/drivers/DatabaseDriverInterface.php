<?php

/**
 * Database Driver Interface
 * 
 * Interface untuk semua database driver
 * Compatible with PHP 5.2, 7, 8+
 * 
 * @package Core\Drivers
 */
interface DatabaseDriverInterface
{
    /**
     * Connect to database
     * 
     * @param array $config Database configuration
     * @return mixed Connection resource
     */
    public function connect($config);
    
    /**
     * Execute query with optional parameters
     * 
     * @param string $sql SQL query
     * @param array $params Query parameters
     * @return mixed Query result
     */
    public function query($sql, $params = array());
    
    /**
     * Prepare SQL statement
     * 
     * @param string $sql SQL query
     * @return mixed Prepared statement
     */
    public function prepare($sql);
    
    /**
     * Fetch single row as associative array
     * 
     * @param mixed $result Query result
     * @return array|false Associative array or false
     */
    public function fetch($result);
    
    /**
     * Fetch all rows as array of objects
     * 
     * @param mixed $result Query result
     * @return array Array of objects
     */
    public function fetchAll($result);
    
    /**
     * Fetch single row as object
     * 
     * @param mixed $result Query result
     * @return object|false Object or false
     */
    public function fetchObject($result);
    
    /**
     * Get last inserted ID
     * 
     * @return int Last insert ID
     */
    public function lastInsertId();
    
    /**
     * Escape string for safe query
     * 
     * @param string $value Value to escape
     * @return string Escaped value
     */
    public function escapeString($value);
    
    /**
     * Quote identifier (table/column name)
     * 
     * @param string $identifier Table or column name
     * @return string Quoted identifier
     */
    public function quoteIdentifier($identifier);
    
    /**
     * Begin transaction
     * 
     * @return bool Success status
     */
    public function beginTransaction();
    
    /**
     * Commit transaction
     * 
     * @return bool Success status
     */
    public function commit();
    
    /**
     * Rollback transaction
     * 
     * @return bool Success status
     */
    public function rollback();
    
    /**
     * Get number of affected rows
     * 
     * @param mixed $result Query result
     * @return int Number of affected rows
     */
    public function affectedRows($result);
    
    /**
     * Get number of rows in result
     * 
     * @param mixed $result Query result
     * @return int Number of rows
     */
    public function numRows($result);
    
    /**
     * Close database connection
     * 
     * @return void
     */
    public function close();
    
    /**
     * Get database driver name
     * 
     * @return string Driver name (mysql, pgsql, etc)
     */
    public function getDriverName();
    
    /**
     * Get database version
     * 
     * @return string Database version
     */
    public function getVersion();
    
    /**
     * Check if using PDO
     * 
     * @return bool True if using PDO
     */
    public function isPDO();
}
