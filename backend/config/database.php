<?php

namespace Backend\Config;

use PDO;
use PDOException;

class Database {
    private static ?PDO $conn = null;

    /**
     * Spawns or returns the active database connection instance
     */
    public static function connect(): PDO {
        // If a connection already exists, return it immediately instead of creating a new one
        if (self::$conn !== null) {
            return self::$conn;
        }

        // Grab values directly out of our environment safely
        $host     = getenv('DB_HOST') ?: 'localhost';
        $db_name  = getenv('DB_NAME');
        $username = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';

        try {
            $dsn = "mysql:host=" . $host . ";dbname=" . $db_name . ";charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            // Assign the PDO connection to the static property
            self::$conn = new PDO($dsn, $username, $password, $options);
            
        } catch (PDOException $e) {
            // Under production environments, suppress explicit database stack traces 
            error_log("Connection Failure Context: " . $e->getMessage());
            die("Critical Pipeline Error: Unable to negotiate safe database handshakes.");
        }

        return self::$conn;
    }
}