<?php
// GameModel.php
namespace App\Models;

use App\Core\Database;

class GameModel extends BaseModel {
    private string $id;
    private string $name;
    private string $status;
    private string $created_at;
    private string $updated_at;

    // Games - Retrieve all games
    public static function all(): array {
        $rows = static::fetchAll("SELECT * FROM games ORDER BY created_at DESC");
        return array_map(fn($row) => self::fromArray($row), $rows);
    }

    // Games - Find game by id
    public static function findById(string $game_id): ?self {
        $row = static::fetchAll(
            "SELECT * FROM games WHERE id = :id LIMIT 1",
            ['id' => $game_id]
        );
        return $row ? self::fromArray($row) : null;
    }

    // Games - Count all games
    public static function countAll() : int {
        return static::count("SELECT COUNT(*) FROM games");
    }

    // Games - Count all games with specific status
    public static function countByStatus(string $status): int {
        return static::count(
            "SELECT COUNT(*) FROM games WHERE status = :status",
            ['status' => $status]
        );
    }

    // Games - Create new game
    public static function create(string $name, string $status = 'waiting'): self {
        $game_id = self::generateUUID();

        static::execute(
            "INSERT INTO games (id, name, status) VALUES (:id, :name, :status, :created_at)",
            [
                'id' => $game_id,
                'name' => $name,
                'status' => $status,
            ]
        );
        return self::findById($game_id);
    }

    // Games - Update game data
    public function update(string $name, string $status): void {
        static::execute(
            "UPDATE games SET name = :name, status = :status WHERE id = :id",
            ['id' => $this->id, 'name' => $name, 'status' => $status]
        );
        $this->name = $name;
        $this->status = $status;
    }

    // Games - Delete game
    public function delete(): void {
        static::execute(
            "DELETE FROM games WHERE id = :id", 
            ['id' => $this->id]
        );
    }

    // Helper - Convert db row to GameModel object
    private static function fromArray(array $row): self {
        $game = new self();
        $game->id = $row['id'];
        $game->name = $row['name'];
        $game->status = $row['status'];
        $game->created_at = $row['created_at'];
        $game->updated_at = $row['updated_at'];
        return $game;
    }

    // Helper - UUID generator
    private static function generateUUID(): string {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff), random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0x0fff) | 0x4000,
            random_int(0, 0x3fff) | 0x8000,
            random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
        );
    }

    // Get the value of id
    public function getId() {
        return $this->id;
    }

    // Get the value of name
    public function getName() {
        return $this->name;
    }

    //Get the value of status
    public function getStatus() {
        return $this->status;
    }

    // Get the value of created_at
    public function getCreated_at() {
        return $this->created_at;
    }

    // Get the value of updated_at
    public function getUpdated_at() {
        return $this->updated_at;
    }
}