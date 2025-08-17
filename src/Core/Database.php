<?php

namespace App\Core;

use mysqli;

class Database
{
    /**
     * The single instance of the class.
     * @var Database|null
     */
    private static $instance = null;

    /**
     * The mysqli connection object.
     * @var mysqli
     */
    private $connection;

    /**
     * Private constructor to prevent direct creation of object.
     */
    private function __construct()
    {
        // Suppress errors to handle them manually
        mysqli_report(MYSQLI_REPORT_STRICT);
        try {
            $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $this->connection->set_charset('utf8mb4');
        } catch (\Exception $e) {
            // In a real app, you'd log this error. For now, we die.
            // This should only happen if the config is wrong and the installer check fails.
            error_log($e->getMessage());
            die('Could not connect to the database. Please check your configuration.');
        }
    }

    /**
     * Get an instance of the Database.
     * @return Database
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the mysqli connection object.
     * @return mysqli
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * A helper method to prepare a statement.
     * @param string $sql The SQL query
     * @return \mysqli_stmt
     */
    public function prepare($sql)
    {
        return $this->connection->prepare($sql);
    }

    /**
     * Cloning and unserialization are not permitted for singletons.
     */
    private function __clone() {}
    public function __wakeup() {}
}
