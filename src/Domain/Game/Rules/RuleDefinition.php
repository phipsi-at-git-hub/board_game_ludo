<?php
// src/Domain/Game/Rules/RuleDefinition.php
namespace App\Domain\Game\Rules;

use App\Domain\Game\GameRuleKey;
use App\Domain\Game\Rules\RuleType;

class RuleDefinition {
    /** @return RoleOption[] */
    public static function all(): array {
        return [
            new RuleOption(
                key: GameRuleKey::MAX_PLAYERS, 
                label_key: 'rules.max_players.label', 
                type: RuleType::SELECT, 
                default: 4, 
                choices: [2,3,4], 
                description_key: 'rules.max_players.description'
            ), 
            new RuleOption(
                key: GameRuleKey::EXTRA_ROLL_ON_SIX,
                label_key: 'rules.extra_roll_on_six.label', 
                type: RuleType::BOOL, 
                default: true
            ), 
            new RuleOption(
                key: GameRuleKey::ALLOW_STACK_OWN_FIGURES, 
                label_key: 'rules.allow_stack_own_figures.label', 
                type: RuleType::BOOL, 
                default: false
            ), 
            new RuleOption(
                key: GameRuleKey::STRICT_GOAL_ORDER, 
                label_key: 'rules.strict_goal_order.label', 
                type: RuleType::BOOL, 
                default: true, 
                description_key: 'rules.strict_goal_order.description'
            ), 
            new RuleOption(
                key: GameRuleKey::START_FIELD_MUST_BE_CLEARED, 
                label_key: 'rules.start_field_must_be_cleared.label',
                type: RuleType::BOOL, 
                default: true
            ),
        ];
    }

    /** Defaults for GameRules */
    public static function defaults(): array {
        return array_reduce(
            self::all(), 
            fn($carry, RuleOption $opt) => $carry + [$opt->key => $opt->default], 
            []
        );
    }
}