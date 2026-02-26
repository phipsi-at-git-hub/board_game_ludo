<?php
// BaseModel.php
namespace App\Models;

use App\Core\Database;
use PDO;

abstract class BaseModel {
    protected Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected static function db(): Database {
        return Database::getInstance();
    }

    protected static function fetchOne(string $sql, array $params = []): ?array {
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    protected static function fetchAll(string $sql, array $params = []): array {
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function execute(string $sql, array $params = []): bool {
        $stmt = static::db()->prepare($sql);
        return $stmt->execute($params);
    }

    protected static function count(string $sql, array $params = []): int {
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    protected static function rollBack() {
        return static::db()->rollBack();
    }

    // Helper - UUID generator
    protected static function generateUUID(): string {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff), random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0x0fff) | 0x4000,
            random_int(0, 0x3fff) | 0x8000,
            random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
        );
    }
}