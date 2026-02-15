<?php
/**
 * Database class - Singleton pattern for database connection
 */
class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "motocity";
        
        $this->conn = new mysqli($servername, $username, $password, $dbname);
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    private function __clone() {}
}
?>
