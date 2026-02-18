<?php
// src/Models/GameRuleSetModel.php
namespace App\Models;

use App\Constants\Application;

final class GameRuleSetModel extends BaseModel {
    // ToDo: Use constant from application.php 

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
            (
                :game_id, 
                :allow_bots, 
                :extra_roll_on_six, 
                :allow_stack_own_figures, 
                :strict_goal_order, 
                :start_field_must_be_cleared, 
                NOW(), 
                NOW()
            )",
            [
                'game_id' => $game_id,
                'allow_bots' => (int)$rules[Application::ALLOW_BOTS],
                'extra_roll_on_six' => (int)$rules[Application::EXTRA_ROLL_ON_SIX],
                'allow_stack_own_figures' => (int)$rules[Application::ALLOW_STACK_OWN_FIGURES],
                'strict_goal_order' => (int)$rules[Application::STRICT_GOAL_ORDER],
                'start_field_must_be_cleared' => (int)$rules[Application::START_FIELD_MUST_BE_CLEARED],
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