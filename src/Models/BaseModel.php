<?php
// BaseModel.php
namespace App\Models;

use App\Core\Persistence\Database;
use App\Core\Utility\UUID;
use PDO;

abstract class BaseModel {
    protected Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected static function db(): Database {
        return Database::getInstance();
    }

    protected static function beginTransaction() {
        return static::db()->beginTransaction(); 
    }

    protected static function commit() {
        return static::db()->commit();
    }

    protected static function rollBack() {
        return static::db()->rollBack();
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

    // Helper - UUID generator
    protected static function generateUUID(): string {
        return UUID::generate(); 
    }

    // Helper - Helps with hydration - Hydrate Int or NULL
    protected static function hydrateIntOrNull(array $row, string $key): ?int {
        if (!array_key_exists($key, $row) || $row[$key] === null) {
            return null;
        }
        return (int) $row[$key];
    }

    // Helper - Helps with hydration - Hydrate Int
    protected static function hydrateInt(array $row, string $key): int {
        return (int) $row[$key];
    }

    // Helper = Helps with hydration - Hydrate String or NULL
    protected static function hydrateStringOrNull(array $row, string $key): ?string {
        if (!array_key_exists($key, $row) || $row[$key] === null) {
            return null;
        }
        return (string) $row[$key];
    }

    // Helper - Helps with hydration - Hydrate String
    protected static function hydrateString(array $row, string $key): string {
        return (string) $row[$key];
    }

    // Helper - Helps with hydration - Hydrate Boolean or Null
    protected static function hydrateBooleanOrNull(array $row, string $key): ?bool {
        if (!array_key_exists($key, $row) || $row[$key] === null) {
            return null;
        }
        return (bool) $row[$key];
    }

    // Helper - Helps with hydration - Hydrate Boolean
    protected static function hydrateBoolean(array $row, string $key): bool {
        return (bool) $row[$key];
    }

    // Helper - Helps with hydration - Hydrate Float or Null
    protected static function hydrateFloatOrNull(array $row, string $key): ?float {
        if (!array_key_exists($key, $row) || $row[$key] === null) {
            return null;
        }
        return (float) $row[$key];
    }

    // Helper - Helps with hydration - Hydrate Boolean
    protected static function hydrateFloat(array $row, string $key): float {
        return (float) $row[$key];
    }

    // Helper - Helps with hydration - Hydrate JSON
    protected static function hydrateJsonOrNull(array $row, string $key): ?array {
        if (!array_key_exists($key, $row) || $row[$key] === null) {
            return null;
        }
        return json_decode($row[$key], true);
    }

    // Helper - Helps with hydration - Array
    protected static function hydrateArray(array $row, string $key): array {
        if (!array_key_exists($key, $row) || $row[$key] === null) {
            return [];
        }
        return json_decode($row[$key], true);
    }

    // Helper - Helps with hydration of UUID
    protected static function hydrateUUIDOrNull(array $row, string $key): ?string {
        if (
            !array_key_exists($key, $row) 
            || $row[$key] === null 
            || strlen($row[$key]) !== 36 
        ) {
            return null;
        }
        return (string) $row[$key];
    }

    // Helper - int or NULL
    protected static function intOrNull(int $value): ?int {
        return $value === null ? null : (int) $value;
    }

    // Helper - string or NULL
    protected static function stringOrNull(string $value): ?string {
        return $value === null ? null : (string) $value;
    }

    // Helper - Boolean
    protected static function bool4DB(bool $value): bool {
        return $value ? 1 : 0;
    }
}