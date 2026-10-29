<?php
// src/Constants/Game/GameConstants.php

namespace App\Constants\Game;

use App\Constants\Database\DbColumnConstants;

final class GameConstants
{
    /**
     * Defines general game constants. 
     */
    public const PLAYER_COUNT = 'player_count';
    public const AVAILABLE_MOVES = 'available_moves';

    /**
     * Defines game constants by game db columns
     */
    public const GAME_ID = DbColumnConstants::GAME_ID;
    public const GAME_NAME = DbColumnConstants::GAME_NAME; 
    public const USER_ID = DbColumnConstants::USER_ID; 
    
    public const CREATED_BY_USER_ID = DbColumnConstants::CREATED_BY_USER_ID;
    public const IS_PRIVATE = DbColumnConstants::IS_PRIVATE;
    public const IS_LOCKED = DbColumnConstants::IS_LOCKED;
    public const IS_TEST_GAME = DbColumnConstants::IS_TEST_GAME;

    /**
     * Defines game rule set constants by database columns.
     */
    public const ALLOW_BOTS = DbColumnConstants::ALLOW_BOTS;
    public const ALL_FIGURES_START_AT_HOME = DbColumnConstants::ALL_FIGURES_START_AT_HOME;
    public const LEAVE_HOME_ATTEMPT = DbColumnConstants::LEAVE_HOME_ATTEMPT;
    public const LEAVE_HOME_ATTEMPTS_MAX = DbColumnConstants::LEAVE_HOME_ATTEMPTS_MAX;
    public const EXTRA_ROLL_ON_SIX_LIMIT = DbColumnConstants::EXTRA_ROLL_ON_SIX_LIMIT;
    public const FORCE_LEAVING_HOME_ON_SIX = DbColumnConstants::FORCE_LEAVING_HOME_ON_SIX;
    public const FORCE_CAPTURE_ENEMY_FIGURES = DbColumnConstants::FORCE_CAPTURE_ENEMY_FIGURES; 
    public const FORCE_EXTRA_LAP_ON_OVERFLOW = DbColumnConstants::FORCE_EXTRA_LAP_ON_OVERFLOW;
    public const ALLOW_STACK_OWN_FIGURES = DbColumnConstants::ALLOW_STACK_OWN_FIGURES;
    public const STRICT_GOAL_ORDER = DbColumnConstants::STRICT_GOAL_ORDER;
    public const START_FIELD_MUST_BE_CLEARED = DbColumnConstants::START_FIELD_MUST_BE_CLEARED;
    public const ENUM_FIRST_FIGURE = DbColumnConstants::ENUM_FIRST_FIGURE;
    public const ENUM_ALL_FIGURES = DbColumnConstants::ENUM_ALL_FIGURES;

    /**
     * Defines game state constants by database columns.
     */
    public const CURRENT_PLAYER_INDEX = DbColumnConstants::CURRENT_PLAYER_INDEX;
    public const CURRENT_DICE_ROLL = DbColumnConstants::CURRENT_DICE_ROLL;
    public const CURRENT_TURN_COUNTER = DbColumnConstants::CURRENT_TURN_COUNTER;
    public const LEAVE_HOME_ATTEMPTS_USED = DbColumnConstants::LEAVE_HOME_ATTEMPTS_USED;
    public const EXTRA_ROLLS_ON_SIX_USED = DbColumnConstants::EXTRA_ROLLS_ON_SIX_USED;
    public const WINNER_USER_ID = DbColumnConstants::WINNER_USER_ID;

    /**
     * Defines game figure constants by database columns.
     */
    public const FIGURE_INDEX = DbColumnConstants::FIGURE_INDEX;
    public const POSITION = DbColumnConstants::POSITION;
    public const AREA = DbColumnConstants::AREA;

    /**
     * Defines game state player database columns.
     */
    public const PLAYER_INDEX = DbColumnConstants::PLAYER_INDEX;

    /**
     * Defines game history constants by database columns.
     */
    public const STATE_INDEX = DbColumnConstants::STATE_INDEX;
    public const STATE = DbColumnConstants::STATE;
}
