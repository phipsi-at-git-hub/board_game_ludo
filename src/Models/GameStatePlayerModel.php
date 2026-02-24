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

    private array $figure_array = [];    // max 4

    // Find game state players for given game
    public static function findByGameId(string $game_id): array {
        return static::fetchAll(
            "SELECT * FROM game_state_players WHERE game_id = :game_id",
            ['game_id' => $game_id]
        );
    }

    // Add player to game and 4 figures
    public static function addPlayer(string $game_id, string $user_id): bool {
        $row = static::execute(
            "INSERT INTO game_state_players
             (game_id, user_id, created_at, updated_at)
             VALUES
             (:game_id, :user_id, NOW(), NOW())",
            [
                'game_id' => $game_id,
                'user_id' => $user_id
            ]
        );

        if ($row) {
            // add 4 figures
            GameStateFigureModel::createInitialFigureSet($game_id, $user_id);
            return true;
        }
        return false;
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
    public function getUserId(): string {
        return $this->user_id;
    }

    // Getter - get username
    public function getUsername(): string {
        return $this->user_name;
    }

    // Getter - Get created at
    public function getCreatedAt(): string {
        return $this->created_at;
    }

    // Getter - Get updated at
    public function getUpdatedAt(): string {
        return $this->updated_at;
    }

    // Getter - Get figures
    public function getAllFigures(): array {
        return $this->figure_array;
    }

    // Getter - Get figure by figure index
    public function getFigureByFigureIndex(int $figure_index) {
        // ToDo: Implement
    }

    // Setter - Set Figure
    public function addFigure(GameStateFigureModel $figure): void {
        $this->figure_array[] = $figure; 
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
        if (array_key_exists(Application::USERNAME, $row))  $game_state_player->user_name = $row[Application::USERNAME];
        $game_state_player->created_at = $row[Application::CREATED_AT];
        $game_state_player->updated_at = $row[Application::UPDATED_AT];

        return $game_state_player;
    }
}