<?php
/**
 * Database Interconnect Manager
 */

// Enforce configuration safety layer
require_once __DIR__ . '/config.php';

class Database {
    private String $host;
    private String $db_name;
    private String $username;
    private String $password;
    private PDO $conn;

    public function __construct() {
        // Grab values directly out of our environment safely
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->db_name = getenv('DB_NAME');
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';
    }

    /**
     * Spawns or returns the active database connection instance
     */
    public function connect() {

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch (PDOException $e) {
            // Under production environments, suppress explicit database stack traces 
            error_log("Connection Failure Context: " . $e->getMessage());
            die("Critical Pipeline Error: Unable to negotiate safe database handshakes.");
        }

        return $this->conn;
    }
}