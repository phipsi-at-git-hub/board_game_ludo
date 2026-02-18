<?php
// src/Models/GameRuleSetModel.php
namespace App\Models;

use App\Constants\Application;

final class GameRuleSetModel extends BaseModel {
    // ToDo: Use constant from application.php 
    private bool $allow_bots;
    private int $extra_roll_limit;
    private bool $allow_stack_own_figures;
    private bool $strict_goal_order;
    private bool $start_field_must_be_cleared;

    // Define Default values
    private const DEFAULT_ALLOW_BOTS = true;
    private const DEFAULT_EXTRA_ROLL_LIMIT = 255;
    private const DEFAULT_ALLOW_STACK_OWN_FIGURES = false;
    private const DEFAULT_STRICT_GOAL_ORDER = true;
    private const DEFAULT_START_FIELD_MUST_BE_CLEARED = true;

    // Define special values
    private const EXTRA_ROLL_UNLIMITED = 255;

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
            (game_id, allow_bots, extra_roll_limit,
             allow_stack_own_figures, strict_goal_order,
             start_field_must_be_cleared, created_at, updated_at)
            VALUES
            (
                :game_id, 
                :allow_bots, 
                :extra_roll_limit, 
                :allow_stack_own_figures, 
                :strict_goal_order, 
                :start_field_must_be_cleared, 
                NOW(), 
                NOW()
            )",
            [
                'game_id' => $game_id,
                'allow_bots' => (int)$rules[Application::ALLOW_BOTS],
                'extra_roll_limit' => (int)$rules[Application::EXTRA_ROLL_LIMIT],
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

    // Helpers
    // Helper - Check default game rule set - classic rule set
    public function isGameClassic(): bool {
        return $this->allow_bots === self::DEFAULT_ALLOW_BOTS 
        && $this->extra_roll_limit === self::DEFAULT_EXTRA_ROLL_LIMIT
        && $this->allow_stack_own_figures === self::DEFAULT_ALLOW_STACK_OWN_FIGURES 
        && $this->strict_goal_order === self::DEFAULT_STRICT_GOAL_ORDER 
        && $this->start_field_must_be_cleared === self::DEFAULT_START_FIELD_MUST_BE_CLEARED;
    }

    // Helper - Is extra_roll_limit unlimited
    public function isExtraRollUnlimited(): bool {
        return $this->extra_roll_limit === self::EXTRA_ROLL_UNLIMITED;
    }

    // Helper - Is extra roll allowed
    public function isExtraRollAllowed() : bool {
        return $this->extra_roll_limit > 0;
    }

    // Helper - Convert db row to GameModel object
    public static function fromArray(array $row): self {
        $rule_set = new self();

        $rule_set->allow_bots = (bool) $row[Application::ALLOW_BOTS];
        $rule_set->extra_roll_limit = (int) $row[Application::EXTRA_ROLL_LIMIT];
        $rule_set->allow_stack_own_figures = (bool) $row[Application::ALLOW_STACK_OWN_FIGURES];
        $rule_set->strict_goal_order = (bool) $row[Application::STRICT_GOAL_ORDER];
        $rule_set->start_field_must_be_cleared = (bool) $row[Application::START_FIELD_MUST_BE_CLEARED];

        return $rule_set;
    }

    // Getter
    // Get extra limit
    public function getExtraRollLimit(): int {
        return $this->extra_roll_limit;
    }
}