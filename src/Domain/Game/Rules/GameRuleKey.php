<?php 
// src/Domain/Game/GameRuleKey.php
namespace App\Domain\Game;

final class GameRuleKey {
    // Setup rules
    public const MIN_PLAYERS = 'min_players';
    public const MAX_PLAYERS = 'max_players';
    public const ALLOW_BOTS = 'allow_bots';

    // Game mechanics
    public const EXTRA_ROLL_ON_SIX = 'extra_roll_on_six';
    public const ALLOW_STACK_OWN_FIGURES = 'allow_stack_own_figures';
    public const STRICT_GOAL_ORDER = 'strict_goal_order';
    public const START_FIELD_MUST_BE_CLEARED = 'start_field_must_be_cleared';

    public static function all(): array {
        return [
            self::MIN_PLAYERS, 
            self::MAX_PLAYERS, 
            self::ALLOW_BOTS, 
            self::EXTRA_ROLL_ON_SIX, 
            self::ALLOW_STACK_OWN_FIGURES, 
            self::STRICT_GOAL_ORDER, 
            self::START_FIELD_MUST_BE_CLEARED,
        ];
    }
}