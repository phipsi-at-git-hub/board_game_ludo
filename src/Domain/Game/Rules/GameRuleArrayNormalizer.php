<?php
// src/Domain/Game/Rules/GameRuleArrayNormalizer.php
namespace App\Domain\Game\Rules;

final class GameRuleArrayNormalizer {
    public static function normalize(array $post_input): array {
        return [
            GameRuleKey::ALLOW_BOTS => isset($post_input[GameRuleKey::ALLOW_BOTS]) ? (bool) $post_input[GameRuleKey::ALLOW_BOTS] : null, 
            GameRuleKey::EXTRA_ROLL_ON_SIX => isset($post_input[GameRuleKey::EXTRA_ROLL_ON_SIX]) ? (bool) $post_input[GameRuleKey::EXTRA_ROLL_ON_SIX] : null, 
            GameRuleKey::ALLOW_STACK_OWN_FIGURES => isset($post_input[GameRuleKey::ALLOW_STACK_OWN_FIGURES]) ? (bool) $post_input[GameRuleKey::ALLOW_STACK_OWN_FIGURES] : null, 
            GameRuleKey::STRICT_GOAL_ORDER => isset($post_input[GameRuleKey::STRICT_GOAL_ORDER]) ? (bool) $post_input[GameRuleKey::STRICT_GOAL_ORDER] : null, 
            GameRuleKey::START_FIELD_MUST_BE_CLEARED => isset($post_input[GameRuleKey::START_FIELD_MUST_BE_CLEARED]) ? (bool) $post_input[GameRuleKey::START_FIELD_MUST_BE_CLEARED] : null, 
        ];
    }
}