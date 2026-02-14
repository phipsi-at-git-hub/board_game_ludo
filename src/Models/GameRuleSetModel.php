<?php
// src/Models/GameRuleSetModel.php
namespace App\Models;

final class GameRuleSetModel extends BaseModel {
    //Find game by game id
    public static function findByGameId(string $game_id): ?array {
        return static::fetchOne(
            "SELECT * FROM game_rule_set WHERE game_id = :gid LIMIT 1",
            ['gid' => $game_id]
        );
    }

    // Create new game
    public static function create(string $game_id, array $rules): bool {
        return static::execute(
            "INSERT INTO game_rule_set
            (game_id, allow_bots, extra_roll_on_six,
             allow_stack_own_figures, strict_goal_order,
             start_field_must_be_cleared, created_at, updated_at)
            VALUES
            (:gid, :bots, :six, :stack, :goal, :clear, NOW(), NOW())",
            [
                'gid' => $game_id,
                'bots' => (int)$rules['allow_bots'],
                'six' => (int)$rules['extra_roll_on_six'],
                'stack' => (int)$rules['allow_stack_own_figures'],
                'goal' => (int)$rules['strict_goal_order'],
                'clear' => (int)$rules['start_field_must_be_cleared'],
            ]
        );
    }

    // Update existing game
    public static function update(string $game_id): void {}

    // Delete existing game
    public static function delete(string $game_id): bool {
        return static::execute(
            "DELETE FROM game_rule_set WHERE game_id = :gid",
            ['gid' => $game_id]
        );
    }
}