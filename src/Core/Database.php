<?php
// Database.php
namespace App\Core;

use LDAP\Result;
use PDO;
use PDOStatement;

class Database {
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct() {
        $dsn = sprintf(
            "mysql:host=%s;dbname=%s;charset=utf8mb4",
            $_ENV['DB_HOST'],
            $_ENV['DB_NAME']
        );

        $this->pdo = new PDO(
            $dsn, $_ENV['DB_USER'], 
            $_ENV['DB_PASSWORD'], 
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    public static function getInstance(): self {
        return self::$instance ??= new self();
    }

    public function prepare(string $sql): PDOStatement {
        return $this->pdo->prepare($sql);
    }

    public function lastInsertId(): string {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction(): void {
        $this->pdo->beginTransaction();
    }

    public function commit(): void {
        $this->pdo->commit();
    }

    public function rollBack(): void {
        $this->pdo->rollBack();
    }

    /*
    public function query(string $sql, array $params = []): array {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function execute(string $sql, array $params = []): bool {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function fetch(string $sql, array $params = []): ?array {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    */
}