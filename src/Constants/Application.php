<?php
// src/Constants/Application.php

namespace App\Constants;

use App\Constants\Dto\GameDtoConstants;
use App\Constants\Dto\GameFigureDtoConstants;
use App\Constants\Dto\GameMoveDtoConstants;
use App\Constants\Dto\GamePlayerDtoConstants;

class Application
{
    /*
     * DTO keys
     */

    /**
     * Defines game DTO keys.
     */
    public const DTO_GAME_ID = GameDtoConstants::GAME_ID;
    public const DTO_GAME_NAME = GameDtoConstants::GAME_NAME;
    public const DTO_GAME_STATUS = GameDtoConstants::GAME_STATUS;
    public const DTO_GAME_TURN = GameDtoConstants::GAME_TURN;
    public const DTO_PLAYERS = GameDtoConstants::PLAYERS;
    public const DTO_CURRENT_PLAYER_ID = GameDtoConstants::CURRENT_PLAYER_ID;
    public const DTO_CURRENT_PLAYER_INDEX = GameDtoConstants::CURRENT_PLAYER_INDEX;
    public const DTO_CURRENT_PLAYER_USERNAME = GameDtoConstants::CURRENT_PLAYER_USERNAME;
    public const DTO_WINNER_USER_ID = GameDtoConstants::WINNER_USER_ID;
    public const DTO_WINNER_PLAYER_INDEX = GameDtoConstants::WINNER_PLAYER_INDEX;
    public const DTO_CURRENT_DICE_ROLL = GameDtoConstants::CURRENT_DICE_ROLL;
    public const DTO_RULESET = GameDtoConstants::RULESET;

    /**
     * Defines player DTO keys.
     */
    public const DTO_USER_ID = GamePlayerDtoConstants::USER_ID;
    public const DTO_USERNAME = GamePlayerDtoConstants::USERNAME;
    public const DTO_PLAYER_INDEX = GamePlayerDtoConstants::PLAYER_INDEX;
    public const DTO_PLAYER_COUNT_EMPTY = GamePlayerDtoConstants::PLAYER_COUNT_EMPTY;
    public const DTO_PLAYER_COUNT_LOW = GamePlayerDtoConstants::PLAYER_COUNT_LOW;
    public const DTO_PLAYER_COUNT_READY = GamePlayerDtoConstants::PLAYER_COUNT_READY;

    /**
     * Defines figure DTO keys.
     */
    public const DTO_FIGURES = GameFigureDtoConstants::FIGURES;
    public const DTO_FIGURE_INDEX = GameFigureDtoConstants::FIGURE_INDEX;
    public const DTO_AREA = GameFigureDtoConstants::AREA;
    public const DTO_POSITION = GameFigureDtoConstants::POSITION;

    /**
     * Defines move DTO keys.
     */
    public const DTO_MOVE = GameMoveDtoConstants::MOVE;
    public const DTO_FROM = GameMoveDtoConstants::FROM;
    public const DTO_TO = GameMoveDtoConstants::TO;
    public const DTO_ABSOLUTE_TARGET = GameMoveDtoConstants::ABSOLUTE_TARGET;
    public const DTO_IS_KICK = GameMoveDtoConstants::IS_KICK;
    public const DTO_KICKED_PLAYER_ID = GameMoveDtoConstants::KICKED_PLAYER_ID;
    public const DTO_KICKED_PLAYER_INDEX = GameMoveDtoConstants::KICKED_PLAYER_INDEX; 
    public const DTO_KICKED_FIGURE_INDEX = GameMoveDtoConstants::KICKED_FIGURE_INDEX;
    public const DTO_IS_GOAL_ENTRY = GameMoveDtoConstants::IS_GOAL_ENTRY;
    public const DTO_IS_LAP_OVERFLOW = GameMoveDtoConstants::IS_LAP_OVERFLOW;
    public const DTO_IS_PASS = GameMoveDtoConstants::IS_PASS;
}
