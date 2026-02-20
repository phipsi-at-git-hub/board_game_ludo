<?php 
// src/Models/GameStateModel.php
namespace App\Models;

final class GameStateModel extends BaseModel {
    // ToDo: Use constant from application.php 
    private int $current_player_index;
    private string $created_at;
    private string $updated_at;

    // Find game state by game id
    public static function findByGameId(string $game_id): ?array {
        return static::fetchOne(
            "SELECT * FROM game_state WHERE game_id = :gid LIMIT 1",
            ['gid' => $game_id]
        );
    }

    // Create new game state for given game
    public static function create(string $game_id): bool {
        return static::execute(
            "INSERT INTO game_state
            (game_id, current_player_index, created_at, updated_at)
            VALUES
            (:game_id, 0, NOW(), NOW())",
            ['game_id' => $game_id]
        );
    }

    // Update game state for given game
    public static function updateCurrentPlayer (): void {}

    // Delete game state for given game
    public static function delete(string $game_id): bool {
        return static::execute(
            "DELETE FROM game_state WHERE game_id = :game_id",
            ['game_id' => $game_id]
        );
    }
}