<?php
// src/Models/GameRuleSetModel.php
namespace App\Models;

use App\Constants\Application;

final class GameRuleSetModel extends BaseModel {
    // ToDo: Use constant from application.php 
    private bool $allow_bots;
    private string $leave_home_attempt;
    private int $leave_home_attempts_max;
    private int $extra_roll_on_six_limit;
    private bool $force_extra_roll_on_overflow;
    private bool $allow_stack_own_figures;
    private bool $strict_goal_order;
    private bool $start_field_must_be_cleared;

    // Define Default values
    private const DEFAULT_ALLOW_BOTS = true;
    private const DEFAULT_LEAVE_HOME_ATTEMPT = Application::ENUM_FIRST_FIGURE;
    private const DEFAULT_LEAVE_HOME_ATTEMPTS_MAX = 3;
    private const DEFAULT_EXTRA_ROLL_ON_SIX_LIMIT = 255;
    private const DEFAULT_FORCE_EXTRA_LAP_ON_OVERFLOW = false;
    private const DEFAULT_ALLOW_STACK_OWN_FIGURES = false;
    private const DEFAULT_STRICT_GOAL_ORDER = true;
    private const DEFAULT_START_FIELD_MUST_BE_CLEARED = true;

    // Define special values
    private const EXTRA_ROLL_ON_SIX_UNLIMITED = 255;

    // Initialize Default RuleSet
    public function initializeDefaultRuleSet(): self {
        $rule_set = new self;

        $rule_set->allow_bots = self::DEFAULT_ALLOW_BOTS;
        $rule_set->leave_home_attempt = self::DEFAULT_LEAVE_HOME_ATTEMPT;
        $rule_set->leave_home_attempts_max = self::DEFAULT_LEAVE_HOME_ATTEMPTS_MAX;
        $rule_set->extra_roll_on_six_limit = self::DEFAULT_EXTRA_ROLL_ON_SIX_LIMIT;
        $rule_set->force_extra_roll_on_overflow = self::DEFAULT_FORCE_EXTRA_LAP_ON_OVERFLOW;
        $rule_set->allow_stack_own_figures = self::DEFAULT_ALLOW_STACK_OWN_FIGURES;
        $rule_set->strict_goal_order = self::DEFAULT_STRICT_GOAL_ORDER;
        $rule_set->start_field_must_be_cleared = self::DEFAULT_START_FIELD_MUST_BE_CLEARED;

        return $rule_set;
    }

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
            (
                game_id, 
                allow_bots, 
                leave_home_attempt, 
                leave_home_attempts_max, 
                extra_roll_on_six_limit, 
                force_extra_lap_on_overflow, 
                allow_stack_own_figures, 
                strict_goal_order, 
                start_field_must_be_cleared, 
                created_at, updated_at)
            VALUES
            (
                :game_id, 
                :allow_bots, 
                :leave_home_attempt, 
                :leave_home_attempts_max, 
                :extra_roll_on_six_limit, 
                :force_extra_lap_on_overflow, 
                :allow_stack_own_figures, 
                :strict_goal_order, 
                :start_field_must_be_cleared, 
                NOW(), 
                NOW()
            )",
            [
                'game_id' => $game_id,
                'allow_bots' => (int)$rules[Application::ALLOW_BOTS],
                'leave_home_attempt' => (string)$rules[Application::LEAVE_HOME_ATTEMPT], 
                'leave_home_attempts_max' => (int)$rules[Application::LEAVE_HOME_ATTEMPTS_MAX], 
                'extra_roll_on_six_limit' => (int)$rules[Application::EXTRA_ROLL_ON_SIX_LIMIT],
                'force_extra_lap_on_overflow' => (int)$rules[Application::FORCE_EXTRA_LAP_ON_OVERFLOW],
                'allow_stack_own_figures' => (int)$rules[Application::ALLOW_STACK_OWN_FIGURES],
                'strict_goal_order' => (int)$rules[Application::STRICT_GOAL_ORDER],
                'start_field_must_be_cleared' => (int)$rules[Application::START_FIELD_MUST_BE_CLEARED],
            ]
        );
    }

    // Update existing game
    public static function update(string $game_id, array $rule_set): void {
        $row = static::execute(
            sprintf(
                "UPDATE game_rule_set 
                SET 
                    allow_bots = :allow_bots, 
                    leave_home_attempt = :leave_home_attempt, 
                    leave_home_attempts_max = :leave_home_attempts_max, 
                    extra_roll_on_six_limit = :extra_roll_on_six_limit, 
                    force_extra_lap_on_overflow = :force_extra_lap_on_overflow, 
                    allow_stack_own_figures = :allow_stack_own_figures, 
                    strict_goal_order = :strict_goal_order, 
                    start_field_must_be_cleared = :start_field_must_be_cleared 
                WHERE game_id = :game_id"
            ), [
                'allow_bots' => $rule_set['allow_bots'], 
                'leave_home_attempt' => $rule_set['leave_home_attempt'], 
                'leave_home_attempts_max' => $rule_set['leave_home_attempts_max'], 
                'extra_roll_on_six_limit' => $rule_set['extra_roll_on_six_limit'], 
                'force_extra_lap_on_overflow' => $rule_set['force_extra_lap_on_overflow'], 
                'allow_stack_own_figures' => $rule_set['allow_stack_own_figures'], 
                'strict_goal_order' => $rule_set['strict_goal_order'], 
                'start_field_must_be_cleared' => $rule_set['start_field_must_be_cleared'], 
                'game_id' => $game_id
            ]
        );
    }

    // Delete existing game
    public static function delete(string $game_id): bool {
        return static::execute(
            "DELETE FROM game_rule_set WHERE game_id = :gid",
            ['gid' => $game_id]
        );
    }

    /**
     * Helper
     */
    // Helper - Check default game rule set - classic rule set
    public function isGameClassic(): bool {
        return $this->allow_bots === self::DEFAULT_ALLOW_BOTS 
        && $this->leave_home_attempt === self::DEFAULT_LEAVE_HOME_ATTEMPT
        && $this->leave_home_attempts_max === self::DEFAULT_LEAVE_HOME_ATTEMPTS_MAX
        && $this->extra_roll_on_six_limit === self::DEFAULT_EXTRA_ROLL_ON_SIX_LIMIT
        && $this->force_extra_roll_on_overflow === self::DEFAULT_FORCE_EXTRA_LAP_ON_OVERFLOW
        && $this->allow_stack_own_figures === self::DEFAULT_ALLOW_STACK_OWN_FIGURES 
        && $this->strict_goal_order === self::DEFAULT_STRICT_GOAL_ORDER 
        && $this->start_field_must_be_cleared === self::DEFAULT_START_FIELD_MUST_BE_CLEARED;
    }

    // Helper - Is extra_roll_limit unlimited
    public function isExtraRollUnlimited(): bool {
        return $this->extra_roll_on_six_limit === self::EXTRA_ROLL_ON_SIX_UNLIMITED;
    }

    // Helper - Is extra roll allowed
    public function isExtraRollAllowed() : bool {
        return $this->extra_roll_on_six_limit > 0;
    }

    // Helper - Convert db row to GameModel object
    public static function fromArray(array $row): self {
        $rule_set = new self();

        if (array_key_exists(Application::ALLOW_BOTS, $row)) $rule_set->allow_bots = (bool) $row[Application::ALLOW_BOTS];
        if (array_key_exists(Application::LEAVE_HOME_ATTEMPT, $row)) $rule_set->leave_home_attempt = (string) $row[Application::LEAVE_HOME_ATTEMPT];
        if (array_key_exists(Application::LEAVE_HOME_ATTEMPTS_MAX, $row)) $rule_set->leave_home_attempts_max = (int) $row[Application::LEAVE_HOME_ATTEMPTS_MAX];
        if (array_key_exists(Application::EXTRA_ROLL_ON_SIX_LIMIT, $row)) $rule_set->extra_roll_on_six_limit = (int) $row[Application::EXTRA_ROLL_ON_SIX_LIMIT];
        if (array_key_exists(Application::FORCE_EXTRA_LAP_ON_OVERFLOW, $row)) $rule_set->force_extra_roll_on_overflow = (bool) $row[Application::FORCE_EXTRA_LAP_ON_OVERFLOW];
        if (array_key_exists(Application::ALLOW_STACK_OWN_FIGURES, $row)) $rule_set->allow_stack_own_figures = (bool) $row[Application::ALLOW_STACK_OWN_FIGURES];
        if (array_key_exists(Application::STRICT_GOAL_ORDER, $row)) $rule_set->strict_goal_order = (bool) $row[Application::STRICT_GOAL_ORDER];
        if (array_key_exists(Application::START_FIELD_MUST_BE_CLEARED, $row)) $rule_set->start_field_must_be_cleared = (bool) $row[Application::START_FIELD_MUST_BE_CLEARED];

        return $rule_set;
    }

    // Check Triple roll rule
    public function canUseTripleRoll(): bool {
        return $this->leave_home_attempts_max === 3;
    }

    /**
     * Getter
     */
    // Get allow_bots
    public function  getAllowBots() : bool {
        return $this->allow_bots;
    }

    // Get leave home attempt
    public function getLeaveHomeAttemptVariant(): string {
        return $this->leave_home_attempt; // Returns either Application::ENUM_FIRST_FIGURE or Application::ENUM_ALL_FIGURES
    }

    // Get max leave home attempts
    public function getLeaveHomeAttemptsMax(): int {
        return $this->leave_home_attempts_max;
    }

    // Get extra limit
    public function getExtraRollOnSixLimit(): int {
        return $this->extra_roll_on_six_limit;
    }

    // Get force extra lap on overflow
    public function getForceExtraLapOnOverflow(): bool {
        return $this->force_extra_roll_on_overflow;
    }

    // Get allow_stack_own_figures
    public function getAllowStackOwnFigures(): bool {
        return $this->allow_stack_own_figures;
    }

    // Get strict_goal_order
    public function getStrictGoalOrder(): bool {
        return $this->strict_goal_order;
    }

    // Get start_field_must_be_cleared
    public function getStartFieldMustBeCleared(): bool {
         return $this->start_field_must_be_cleared;
    }
}