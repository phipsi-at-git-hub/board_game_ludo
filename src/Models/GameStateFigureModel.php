<?php 
// src/Models/GameStateFigureModel.php
namespace App\Models;

final class GameStateFigureModel extends BaseModel {
    // ToDo: Use constant from application.php 
    private string $player_user_id;
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
                 (:game_id, :user_id, :figure_index, 0, 'home', NOW(), NOW())",
                [
                    'game_id' => $game_id,
                    'user_id' => $user_id,
                    'figure_index' => $i
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
}