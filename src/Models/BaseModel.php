<?php
// BaseModel.php
namespace App\Models;

use App\Core\Database;
use PDO;

abstract class BaseModel {
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
    /*
    protected Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
    */
}