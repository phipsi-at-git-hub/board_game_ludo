<?php 
// src/Models/GameStatePlayerModel.php
namespace App\Models;

final class GameStatePlayerModel extends BaseModel {
    // Find game state players for given game
    public static function findByGameId(string $game_id): array {
        return static::fetchAll(
            "SELECT * FROM game_state_players WHERE game_id = :game_id",
            ['game_id' => $game_id]
        );
    }

    // Add player to game
    public static function addPlayer(string $game_id, string $user_id): bool {
        return static::execute(
            "INSERT INTO game_state_players
             (game_id, user_id, created_at, updated_at)
             VALUES
             (:game_id, :user_id, NOW(), NOW())",
            [
                'game_id' => $game_id,
                'user_id' => $user_id
            ]
        );
    }

    // Remove given player from game
    public static function removePlayer(string $game_id, string $user_id): bool {
        return static::execute(
            "DELETE FROM game_state_players WHERE game_id = :game_id AND user_id = :user_id",
            ['game_id' => $game_id, 'user_id' => $user_id], 

        );
    }

    // Remove all player from game
    public static function removeAllPlayer(string $game_id): bool {
        return static::execute(
            "DELETE FROM game_state_players WHERE game_id = :game_id",
            ['game_id' => $game_id]
        );
    }
}