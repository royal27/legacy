<?php

class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private $prefix = DB_PREFIX;

    public $connection;
    private $stmt;
    private $error;

    public function __construct() {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $this->connection = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
            $this->connection->set_charset("utf8mb4");
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            throw new Exception($this->error);
        }
    }

    // Prepare statement with query
    public function query($sql) {
        $this->stmt = $this->connection->prepare($sql);
        if ($this->connection->error) {
            throw new Exception("SQL Prepare Error: " . $this->connection->error);
        }
    }

    // A simple bind parameters function
    // For mysqli, binding is done on execute. We'll collect params here.
    // Note: This is a simplified version. A more robust implementation would handle types.
    public function bind($params = []) {
        if ($this->stmt && !empty($params)) {
            $types = str_repeat('s', count($params)); // Assume all params are strings by default
            $this->stmt->bind_param($types, ...$params);
        }
    }

    // Execute the prepared statement with bound parameters
    public function execute($params = []) {
        $this->bind($params);
        return $this->stmt->execute();
    }

    // Get result set as array of objects
    public function resultSet($params = []) {
        $this->execute($params);
        $result = $this->stmt->get_result();
        $this->stmt->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get single record as object
    public function single($params = []) {
        $this->execute($params);
        $result = $this->stmt->get_result();
        $this->stmt->close();
        return $result->fetch_assoc();
    }

    // Get row count
    public function rowCount() {
        return $this->stmt->num_rows;
    }

    // Get the last inserted ID
    public function lastInsertId() {
        return $this->connection->insert_id;
    }

    // Get table prefix
    public function getPrefix() {
        return $this->prefix;
    }

    // Close connection
    public function __destruct() {
        if ($this->stmt) {
            $this->stmt->close();
        }
        if ($this->connection) {
            $this->connection->close();
        }
    }
}
