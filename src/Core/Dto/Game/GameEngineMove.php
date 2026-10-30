<?php
// src/Core/Dto/Game/GameEngineMove.php

namespace App\Core\Dto\Game;

use App\Constants\Dto\GameDtoConstants;
use App\Constants\Dto\GameFigureDtoConstants;
use App\Constants\Dto\GameMoveDtoConstants;

final class GameEngineMove
{
    public static function create(): array
    {
        return [
            GameDtoConstants::GAME_TURN => null,
            GameFigureDtoConstants::FIGURE_INDEX => null,
            GameMoveDtoConstants::FROM => [
                GameFigureDtoConstants::AREA => null,
                GameFigureDtoConstants::POSITION => null
            ],
            GameMoveDtoConstants::TO => [
                GameFigureDtoConstants::AREA => null,
                GameFigureDtoConstants::POSITION => null
            ],
            GameDtoConstants::WINNER_USER_ID => null,
            GameDtoConstants::WINNER_PLAYER_INDEX => null,
            GameMoveDtoConstants::IS_PASS => false,
            GameMoveDtoConstants::ABSOLUTE_TARGET => null,
            GameMoveDtoConstants::IS_KICK => false,
            GameMoveDtoConstants::KICKED_PLAYER_ID => null,
            GameMoveDtoConstants::KICKED_PLAYER_INDEX => null,
            GameMoveDtoConstants::KICKED_FIGURE_INDEX => null,
            GameMoveDtoConstants::IS_GOAL_ENTRY => false,
            GameMoveDtoConstants::IS_LAP_OVERFLOW => false,
        ];
    }

    public static function createPass(): array
    {
        $move = self::create();
        $move[GameMoveDtoConstants::IS_PASS] = true;
        return $move;
    }
}
