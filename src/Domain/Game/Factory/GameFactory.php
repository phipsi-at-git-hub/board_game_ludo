<?php
// src/Domain/Game/Factory/GameFactory.php
namespace App\Domain\Game;

use App\Domain\Game\Rules\GameRules;

final class GameFactory {
    public static function create(string $created_by_user_id, array $rule_config = []): Game {
        $rules = new GameRules($rule_config);
        return Game::create($created_by_user_id, $rules);
    }
}