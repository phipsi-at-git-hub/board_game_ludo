<?php 
// src/Models/GameStatePlayerModel.php
namespace App\Models;

use App\Constants\Application;

final class GameStatePlayerModel extends BaseModel {
    // ToDo: Use constant from application.php 
    private string $game_id;
    private string $user_id;
    private string $user_name;
    private string $created_at;
    private string $updated_at;

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

    // Getter
    // Getter - get user id
    public function getPlayerId(): string {
        return $this->user_id;
    }

    // Helper
    // Helper - Convert db rows to GameModel dynamically
    private static function fromArrayDynamic(array $row): self {
        $game_state_player = new self();

        foreach ($row as $key => $value) {
            $game_state_player->{$key} = $value; 
        }
        return $game_state_player;
    }

    // Helper - Convert db rows to GameModel strict
    public static function fromArray(array $row) : self {
        $game_state_player = new self();

        $game_state_player->game_id = $row[Application::GAME_ID];
        $game_state_player->user_id = $row[Application::USER_ID];
        $game_state_player->user_name = $row[Application::USERNAME];
        $game_state_player->created_at = $row[Application::CREATED_AT];
        $game_state_player->updated_at = $row[Application::UPDATED_AT];

        return $game_state_player;
    }
}