<?php 
// src/Models/GameStateFigureModel.php
namespace App\Models;

use App\Constants\Application;

final class GameStateFigureModel extends BaseModel {
    // ToDo: Use constant from application.php 
    private string $game_id;
    private string $user_id;
    private string $user_name;
    private int $figure_index;
    private int $position;
    private string $area;
    private string $created_at;
    private string $updated_at;

    // Find game state figures by game id
    public function findByGameId(string $game_id): array {
        return static::fetchAll(
            "SELECT * FROM game_state_figure WHERE game_id = :game_id",
            ['game_id' => $game_id]
        );
    }

    // Create initial figures for given game
    public static function createInitialFigureSet(string $game_id, string $user_id): void {
        for ($i = 0; $i < 4; $i++) {
            static::execute(
                "INSERT INTO game_state_figure
                 (game_id, user_id, figure_index, position, area, created_at, updated_at)
                 VALUES
                 (:game_id, :user_id, :figure_index, :position, 'home', NOW(), NOW())",
                [
                    'game_id' => $game_id,
                    'user_id' => $user_id,
                    'figure_index' => $i, 
                    'position' => $i
                ]
            );
        }
    }

    // Update given figures for given game
    public static function updateFigurePosition(): void {}

    // Reset given figure for given game
    public static function resetFigure(): void {}

    // Delete all figures of given game
    public static function removeAllFigures($game_id): bool {
        return static::execute(
            "DELETE FROM game_state_figure WHERE game_id = :game_id",
            ['game_id' => $game_id]
        );
    }

    // Getter
    // Getter - Get game id
    public function getGameId(): string {
        return $this->game_id;
    }

    // Getter - Get user id
    public function getUserId(): string {
        return $this->user_id;
    }

    // Getter - Get username
    public function getUsername(): string {
        return $this->user_name;
    }

    // Getter - Get figure index
    public function getFigureIndex(): string {
        return $this->figure_index;
    }

    // Getter - Get position
    public function getPosition(): string {
        return $this->position;
    }

    // Getter - Get area
    public function getArea(): string {
        return $this->area;
    }

    // Getter - Get created at
    public function getCreatedAt(): string {
        return $this->created_at;
    }

    // Getter - Get updated at
    public function getUpdatedAt(): string {
        return $this->updated_at;
    }

    // Helper
    // Helper - Convert db rows to GameModel dynamically
    private static function fromArrayDynamic(array $row): self {
        $game_state_figure = new self();

        foreach ($row as $key => $value) {
            $game_state_figure->{$key} = $value; 
        }
        return $game_state_figure;
    }

    // Helper - Convert db rows to GameModel strict
    public static function fromArray(array $row) : self {
        $game_state_figure = new self();

        $game_state_figure->game_id = $row[Application::GAME_ID];
        $game_state_figure->user_id = $row[Application::USER_ID];
        if (array_key_exists(Application::USERNAME, $row))  $game_state_figure->user_name = $row[Application::USERNAME];
        $game_state_figure->figure_index = $row[Application::FIGURE_INDEX];
        $game_state_figure->position = $row[Application::POSITION];
        $game_state_figure->area = $row[Application::AREA];
        $game_state_figure->created_at = $row[Application::CREATED_AT];
        $game_state_figure->updated_at = $row[Application::UPDATED_AT];

        return $game_state_figure;
    }
}