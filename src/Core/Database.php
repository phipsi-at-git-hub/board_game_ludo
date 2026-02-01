<?php
// Database.php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    "mysql:host=%s;dbname=%s;charset=utf8mb4", 
                    $_ENV['DB_HOST'], 
                    $_ENV['DB_NAME'],
                );

                self::$instance = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,]);
            } catch (PDOException $e) {
                die('DB connection failed: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}