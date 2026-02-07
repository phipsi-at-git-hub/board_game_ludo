<?php
// src/Domain/Game/Rules/GameRules.php
namespace App\Domain\Game\Rules;

use App\Domain\Game\GameRuleKey;
use InvalidArgumentException;

final class GameRules {
    // Setup game rules
    private int $min_players;
    private int $max_players;
    private bool $allow_bots;

    // Game mechanics
    private bool $extra_roll_on_six;
    private bool $allow_stack_own_figures;
    private bool $strict_goal_order;
    private bool $start_field_must_be_cleared;

    // Game rule defaults
    private const DEFAULTS = [
        GameRuleKey::MIN_PLAYERS => 2, 
        GameRuleKey::MAX_PLAYERS => 4, 
        GameRuleKey::ALLOW_BOTS => true, 
        GameRuleKey::EXTRA_ROLL_ON_SIX => true, 
        GameRuleKey::ALLOW_STACK_OWN_FIGURES => false, 
        GameRuleKey::STRICT_GOAL_ORDER => true, 
        GameRuleKey::START_FIELD_MUST_BE_CLEARED => true, 
    ];

    // Constructor with config array
    public function __construct(array $config) {
        // Replace missing rules from given rule set config with defaults
        $config = array_replace(self::DEFAULTS, $config);
        // Validate given rule set config
        GameRuleArrayValidator::validate($config);

        // Setup game rules
        $this->min_players = (int) ($config[GameRuleKey::MIN_PLAYERS] ?? 2);
        $this->max_players = (int) ($config[GameRuleKey::MAX_PLAYERS] ?? 4);
        $this->allow_bots = (bool) ($config[GameRuleKey::ALLOW_BOTS] ?? true);

        // Setup Mechanics
        $this->extra_roll_on_six = (bool) ($config[GameRuleKey::EXTRA_ROLL_ON_SIX] ?? true);
        $this->allow_stack_own_figures = (bool) ($config[GameRuleKey::ALLOW_STACK_OWN_FIGURES] ?? true);
        $this->strict_goal_order = (bool) ($config[GameRuleKey::STRICT_GOAL_ORDER] ?? true);
        $this->start_field_must_be_cleared = (bool) ($config[GameRuleKey::START_FIELD_MUST_BE_CLEARED] ?? true);
    }

    // Create an instance with default rules
    public static function fromDefaults(): self {
        return new self([]);
    }

    // Convert GameRules to array
    public function toArray(): array {
        return [
            GameRuleKey::MIN_PLAYERS => $this->min_players, 
            GameRuleKey::MAX_PLAYERS => $this->max_players, 
            GameRuleKey::ALLOW_BOTS => $this->allow_bots, 
            GameRuleKey::EXTRA_ROLL_ON_SIX => $this->extra_roll_on_six, 
            GameRuleKey::ALLOW_STACK_OWN_FIGURES => $this->allow_stack_own_figures, 
            GameRuleKey::STRICT_GOAL_ORDER => $this->strict_goal_order, 
            GameRuleKey::START_FIELD_MUST_BE_CLEARED => $this->start_field_must_be_cleared, 
        ];
    }

    // Update rules dynamically
    public function update(array $config): self {
        return new self(array_merge($this->toArray(), $config));
    }

    // Getter
    public function getMinPlayers(): int {
        return $this->min_players;
    }

    public function getMaxPlayers(): int {
        return $this->max_players;
    }

    public function getAllowBots(): bool {
        return $this->allow_bots;
    }

    public function getExtraRollOnSix(): bool {
        return $this->extra_roll_on_six;
    }

    public function getAllowStackOwnFigures(): bool {
        return $this->allow_stack_own_figures;
    }

    public function getStrictGoalOrder(): bool {
        return $this->strict_goal_order;
    }

    public function getStartFieldMustBeCleared(): bool {
        return $this->start_field_must_be_cleared;
    }
}