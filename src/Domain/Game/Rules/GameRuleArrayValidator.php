<?php
// src/Domain/Games/Rules/GameRuleValidator.php
namespace App\Domain\Game\Rules;

use App\Domain\Game\GameRuleKey;
use InvalidArgumentException;

class GameRuleArrayValidator {
    public static function validate(array $all_rules): void {
        // min_players & max_players
        $min = $all_rules[GameRuleKey::MIN_PLAYERS] ?? 2;
        $max = $all_rules[GameRuleKey::MAX_PLAYERS] ?? 4;

        if (!is_int($min) || $min < 2) {
            throw new InvalidArgumentException("Minimum players must be >= 2");
        }

        if (!is_int($max) || $max > 4 || $max < $min) {
            throw new InvalidArgumentException("Maximum players must be between min players and 4");
        }

        // Bots allows only if min_players < max_players
        $allow_bots = $all_rules[GameRuleKey::ALLOW_BOTS] ?? false;
        if ($allow_bots && $min === $max) {
            throw new InvalidArgumentException("Bots cannot be used if all players are human");
        }

        // Extra roll on six
        $extra_roll_on_six = $all_rules[GameRuleKey::EXTRA_ROLL_ON_SIX] ?? true;
        if (!is_bool($extra_roll_on_six)) {
            throw new InvalidArgumentException("Extra roll on six must be boolean");
        }

        // Stack own figures
        $allow_stack = $all_rules[GameRuleKey::ALLOW_STACK_OWN_FIGURES] ?? false;
        if (!is_bool($allow_stack)) {
            throw new InvalidArgumentException("Allow stack own figures must be boolean");
        }

        // Strict goal order
        $strict_goal = $all_rules[GameRuleKey::STRICT_GOAL_ORDER] ?? true;
        if (!is_bool($strict_goal)) {
            throw new InvalidArgumentException("Strict goal order must be boolean");
        }

        // Start field must be cleared
        $start_clear = $all_rules[GameRuleKey::START_FIELD_MUST_BE_CLEARED] ?? true;
        if (!is_bool($start_clear)) {
            throw new InvalidArgumentException("Start field must be cleared must be boolean");
        }
    }
}