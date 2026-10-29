<?php
// src/Constants/Application.php

namespace App\Constants;

use App\Constants\Database\DbColumnConstants;
use App\Constants\Dto\GameDtoConstants;
use App\Constants\Dto\GameFigureDtoConstants;
use App\Constants\Dto\GameMoveDtoConstants;
use App\Constants\Dto\GamePlayerDtoConstants;

class Application
{
    /*
     * Database columns
     */

    /**
     * Defines common database column names.
     */
    //public const DB_COLUMN_ID = DbColumnConstants::ID;
    //public const DB_COLUMN_NAME = DbColumnConstants::NAME;
    //public const DB_COLUMN_GAME_NAME = DbColumnConstants::GAME_CREATION_ENABLED;
    //public const DB_COLUMN_GAME_ID = DbColumnConstants::GAME_ID;
    //public const DB_COLUMN_USER_ID = DbColumnConstants::USER_ID;
    //public const DB_COLUMN_STATUS = DbColumnConstants::STATUS;
    //public const DB_COLUMN_CREATED_AT = DbColumnConstants::CREATED_AT;
    //public const DB_COLUMN_UPDATED_AT = DbColumnConstants::UPDATED_AT;

    /**
     * Defines game database columns.
     */
    //public const DB_COLUMN_CREATED_BY_USER_ID = DbColumnConstants::CREATED_BY_USER_ID;
    //public const DB_COLUMN_IS_PRIVATE = DbColumnConstants::IS_PRIVATE;
    //public const DB_COLUMN_IS_LOCKED = DbColumnConstants::IS_LOCKED;
    //public const DB_COLUMN_IS_TEST_GAME = DbColumnConstants::IS_TEST_GAME;

    /**
     * Defines game rule set database columns.
     */
    //public const DB_COLUMN_ALLOW_BOTS = DbColumnConstants::ALLOW_BOTS;
    //public const DB_COLUMN_ALL_FIGURES_START_AT_HOME = DbColumnConstants::ALL_FIGURES_START_AT_HOME;
    //public const DB_COLUMN_LEAVE_HOME_ATTEMPT = DbColumnConstants::LEAVE_HOME_ATTEMPT;
    //public const DB_COLUMN_LEAVE_HOME_ATTEMPTS_MAX = DbColumnConstants::LEAVE_HOME_ATTEMPTS_MAX;
    //public const DB_COLUMN_EXTRA_ROLL_ON_SIX_LIMIT = DbColumnConstants::EXTRA_ROLL_ON_SIX_LIMIT;
    //public const DB_COLUMN_FORCE_LEAVING_HOME_ON_SIX = DbColumnConstants::FORCE_LEAVING_HOME_ON_SIX;
    //public const DB_COLUMN_FORCE_CAPTURE_ENEMY_FIGURES = DbColumnConstants::FORCE_CAPTURE_ENEMY_FIGURES;
    //public const DB_COLUMN_FORCE_EXTRA_LAP_ON_OVERFLOW = DbColumnConstants::FORCE_EXTRA_LAP_ON_OVERFLOW;
    //public const DB_COLUMN_ALLOW_STACK_OWN_FIGURES = DbColumnConstants::ALLOW_STACK_OWN_FIGURES;
    //public const DB_COLUMN_STRICT_GOAL_ORDER = DbColumnConstants::STRICT_GOAL_ORDER;
    //public const DB_COLUMN_START_FIELD_MUST_BE_CLEARED = DbColumnConstants::START_FIELD_MUST_BE_CLEARED;
    //public const DB_COLUMN_ENUM_FIRST_FIGURE = DbColumnConstants::ENUM_FIRST_FIGURE;
    //public const DB_COLUMN_ENUM_ALL_FIGURES = DbColumnConstants::ENUM_ALL_FIGURES;

    /**
     * Defines game state database columns.
     */
    //public const DB_COLUMN_CURRENT_PLAYER_INDEX = DbColumnConstants::CURRENT_PLAYER_INDEX;
    //public const DB_COLUMN_CURRENT_DICE_ROLL = DbColumnConstants::CURRENT_DICE_ROLL;
    //public const DB_COLUMN_CURRENT_TURN_COUNTER = DbColumnConstants::CURRENT_TURN_COUNTER;
    //public const DB_COLUMN_LEAVE_HOME_ATTEMPTS_USED = DbColumnConstants::LEAVE_HOME_ATTEMPTS_USED;
    //public const DB_COLUMN_EXTRA_ROLLS_ON_SIX_USED = DbColumnConstants::EXTRA_ROLLS_ON_SIX_USED;
    //public const DB_COLUMN_WINNER_USER_ID = DbColumnConstants::WINNER_USER_ID;

    /**
     * Defines game figure database columns.
     */
    //public const DB_COLUMN_FIGURE_INDEX = DbColumnConstants::FIGURE_INDEX;
    //public const DB_COLUMN_POSITION = DbColumnConstants::POSITION;
    //public const DB_COLUMN_AREA = DbColumnConstants::AREA;

    /**
     * Defines game state player database columns.
     */
    //public const DB_COLUMN_PLAYER_INDEX = DbColumnConstants::PLAYER_INDEX;

    /**
     * Defines game history database columns.
     */
    //public const DB_COLUMN_STATE_INDEX = DbColumnConstants::STATE_INDEX;
    //public const DB_COLUMN_STATE = DbColumnConstants::STATE;

    /**
     * Defines user database columns.
     */
    //public const DB_COLUMN_USERNAME = DbColumnConstants::USERNAME;
    //public const DB_COLUMN_FIRSTNAME = DbColumnConstants::FIRSTNAME;
    //public const DB_COLUMN_LASTNAME = DbColumnConstants::LASTNAME;
    //public const DB_COLUMN_EMAIL = DbColumnConstants::EMAIL;
    //public const DB_COLUMN_ROLE = DbColumnConstants::ROLE;
    //public const DB_COLUMN_PREFERRED_LANGUAGE = DbColumnConstants::PREFERRED_LANGUAGE;
    //public const DB_COLUMN_PREFERRED_CAMERA_MODE = DbColumnConstants::PREFERRED_CAMERA_MODE;
    //public const DB_COLUMN_PASSWORD_HASH = DbColumnConstants::PASSWORD_HASH;
    //public const DB_COLUMN_RESET_TOKEN = DbColumnConstants::RESET_TOKEN;
    //public const DB_COLUMN_RESET_TOKEN_EXPIRES_AT = DbColumnConstants::RESET_TOKEN_EXPIRES_AT;
    //public const DB_COLUMN_LAST_LOGIN = DbColumnConstants::LAST_LOGIN;

    /**
     * Defines user statistics database columns.
     */
    //public const DB_COLUMN_LOGINS = DbColumnConstants::LOGINS;
    public const DB_COLUMN_GAMES_CREATED = DbColumnConstants::GAMES_CREATED;
    public const DB_COLUMN_GAMES_PARTICIPATED = DbColumnConstants::GAMES_PARTICIPATED;
    public const DB_COLUMN_GAMES_WON = DbColumnConstants::GAMES_WON;

    /**
     * Defines system settings database columns.
     */
    public const DB_COLUMN_REGISTRATION_ENABLED = DbColumnConstants::REGISTRATION_ENABLED;
    public const DB_COLUMN_LOGIN_ENABLED = DbColumnConstants::LOGIN_ENABLED;
    public const DB_COLUMN_SYSTEM_ENABLED = DbColumnConstants::SYSTEM_ENABLED;
    public const DB_COLUMN_LOGGING_DEBUG_ENABLED = DbColumnConstants::LOGGING_DEBUG_ENABLED;
    public const DB_COLUMN_GAME_CREATION_ENABLED = DbColumnConstants::GAME_CREATION_ENABLED;
    public const DB_COLUMN_GAME_PLAY_ENABLED = DbColumnConstants::GAME_PLAY_ENABLED;
    public const DB_COLUMN_MAINTENANCE_MODE_ENABLED = DbColumnConstants::MAINTENANCE_MODE_ENABLED;
    public const DB_COLUMN_MAINTENANCE_MESSAGE = DbColumnConstants::MAINTENANCE_MESSAGE;
    public const DB_COLUMN_SYSTEM_NOTICE_ENABLED = DbColumnConstants::SYSTEM_NOTICE_ENABLED;
    public const DB_COLUMN_SYSTEM_NOTICE_MESSAGE = DbColumnConstants::SYSTEM_NOTICE_MESSAGE;
    public const DB_COLUMN_UPDATED_BY = DbColumnConstants::UPDATED_BY;


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


    /*
     * Deprecated constants
     *
     * These aliases preserve compatibility with existing code.
     * Migrate usages to the corresponding replacement constants.
     */

    /*
     * Deprecated database column aliases
     */

    /**
     * @deprecated Use DB_COLUMN_ID instead.
     */
    //public const ID = self::DB_COLUMN_ID;

    /**
     * @deprecated Use DB_COLUMN_NAME instead.
     */
    //public const NAME = self::DB_COLUMN_NAME;

    /**
     * @deprecated Use DB_COLUMN_GAME_NAME instead.
     */
    //public const GAME_NAME = self::DB_COLUMN_GAME_NAME;

    /**
     * @deprecated Use DB_COLUMN_GAME_ID instead.
     */
    //public const GAME_ID = self::DB_COLUMN_GAME_ID;

    /**
     * @deprecated Use DB_COLUMN_USER_ID instead.
     */
    //public const USER_ID = self::DB_COLUMN_USER_ID;

    /**
     * @deprecated Use DB_COLUMN_STATUS instead.
     */
    //public const STATUS = self::DB_COLUMN_STATUS;

    /**
     * @deprecated Use DB_COLUMN_CREATED_AT instead.
     */
    //public const CREATED_AT = self::DB_COLUMN_CREATED_AT;

    /**
     * @deprecated Use DB_COLUMN_UPDATED_AT instead.
     */
    //public const UPDATED_AT = self::DB_COLUMN_UPDATED_AT;

    /**
     * @deprecated Use DB_COLUMN_CREATED_BY_USER_ID instead.
     */
    //public const CREATED_BY_USER_ID = self::DB_COLUMN_CREATED_BY_USER_ID;

    /**
     * @deprecated Use DB_COLUMN_IS_PRIVATE instead.
     */
    //public const IS_PRIVATE = self::DB_COLUMN_IS_PRIVATE;

    /**
     * @deprecated Use DB_COLUMN_IS_LOCKED instead.
     */
    //public const IS_LOCKED = self::DB_COLUMN_IS_LOCKED;

    /**
     * @deprecated Use DB_COLUMN_IS_TEST_GAME instead.
     */
    //public const IS_TEST_GAME = self::DB_COLUMN_IS_TEST_GAME;

    /**
     * @deprecated Use DB_COLUMN_ALLOW_BOTS instead.
     */
    //public const ALLOW_BOTS = self::DB_COLUMN_ALLOW_BOTS;

    /**
     * @deprecated Use DB_COLUMN_ALL_FIGURES_START_AT_HOME instead.
     */
    //public const ALL_FIGURES_START_AT_HOME = self::DB_COLUMN_ALL_FIGURES_START_AT_HOME;

    /**
     * @deprecated Use DB_COLUMN_LEAVE_HOME_ATTEMPT instead.
     */
    //public const LEAVE_HOME_ATTEMPT = self::DB_COLUMN_LEAVE_HOME_ATTEMPT;

    /**
     * @deprecated Use DB_COLUMN_LEAVE_HOME_ATTEMPTS_MAX instead.
     */
    //public const LEAVE_HOME_ATTEMPTS_MAX = self::DB_COLUMN_LEAVE_HOME_ATTEMPTS_MAX;

    /**
     * @deprecated Use DB_COLUMN_EXTRA_ROLL_ON_SIX_LIMIT instead.
     */
    //public const EXTRA_ROLL_ON_SIX_LIMIT = self::DB_COLUMN_EXTRA_ROLL_ON_SIX_LIMIT;

    /**
     * @deprecated Use DB_COLUMN_FORCE_LEAVING_HOME_ON_SIX instead.
     */
    //public const FORCE_LEAVING_HOME_ON_SIX = self::DB_COLUMN_FORCE_LEAVING_HOME_ON_SIX;

    /**
     * @deprecated Use DB_COLUMN_FORCE_CAPTURE_ENEMY_FIGURES instead.
     */
    //public const FORCE_CAPTURE_ENEMY_FIGURES = self::DB_COLUMN_FORCE_CAPTURE_ENEMY_FIGURES;

    /**
     * @deprecated Use DB_COLUMN_FORCE_EXTRA_LAP_ON_OVERFLOW instead.
     */
    //public const FORCE_EXTRA_LAP_ON_OVERFLOW = self::DB_COLUMN_FORCE_EXTRA_LAP_ON_OVERFLOW;

    /**
     * @deprecated Use DB_COLUMN_ALLOW_STACK_OWN_FIGURES instead.
     */
    //public const ALLOW_STACK_OWN_FIGURES = self::DB_COLUMN_ALLOW_STACK_OWN_FIGURES;

    /**
     * @deprecated Use DB_COLUMN_STRICT_GOAL_ORDER instead.
     */
    //public const STRICT_GOAL_ORDER = self::DB_COLUMN_STRICT_GOAL_ORDER;

    /**
     * @deprecated Use DB_COLUMN_START_FIELD_MUST_BE_CLEARED instead.
     */
    //public const START_FIELD_MUST_BE_CLEARED = self::DB_COLUMN_START_FIELD_MUST_BE_CLEARED;

    /**
     * @deprecated Use DB_COLUMN_ENUM_FIRST_FIGURE instead.
     */
    //public const ENUM_FIRST_FIGURE = self::DB_COLUMN_ENUM_FIRST_FIGURE;

    /**
     * @deprecated Use DB_COLUMN_ENUM_ALL_FIGURES instead.
     */
    //public const ENUM_ALL_FIGURES = self::DB_COLUMN_ENUM_ALL_FIGURES;

    /**
     * @deprecated Use DB_COLUMN_CURRENT_PLAYER_INDEX instead.
     */
    //public const CURRENT_PLAYER_INDEX = self::DB_COLUMN_CURRENT_PLAYER_INDEX;

    /**
     * @deprecated Use DB_COLUMN_CURRENT_DICE_ROLL instead.
     */
    //public const CURRENT_DICE_ROLL = self::DB_COLUMN_CURRENT_DICE_ROLL;

    /**
     * @deprecated Use DB_COLUMN_CURRENT_TURN_COUNTER instead.
     */
    //public const CURRENT_TURN_COUNTER = self::DB_COLUMN_CURRENT_TURN_COUNTER;

    /**
     * @deprecated Use DB_COLUMN_LEAVE_HOME_ATTEMPTS_USED instead.
     */
    //public const LEAVE_HOME_ATTEMPTS_USED = self::DB_COLUMN_LEAVE_HOME_ATTEMPTS_USED;

    /**
     * @deprecated Use DB_COLUMN_EXTRA_ROLLS_ON_SIX_USED instead.
     */
    //public const EXTRA_ROLLS_ON_SIX_USED = self::DB_COLUMN_EXTRA_ROLLS_ON_SIX_USED;

    /**
     * @deprecated Use DB_COLUMN_WINNER_USER_ID instead.
     */
    //public const WINNER_USER_ID = self::DB_COLUMN_WINNER_USER_ID;

    /**
     * @deprecated Use DB_COLUMN_FIGURE_INDEX instead.
     */
    //public const FIGURE_INDEX = self::DB_COLUMN_FIGURE_INDEX;

    /**
     * @deprecated Use DB_COLUMN_POSITION instead.
     */
    //public const POSITION = self::DB_COLUMN_POSITION;

    /**
     * @deprecated Use DB_COLUMN_AREA instead.
     */
    //public const AREA = self::DB_COLUMN_AREA;

    /**
     * @deprecated Use DB_COLUMN_PLAYER_INDEX instead.
     */
    //public const PLAYER_INDEX = self::DB_COLUMN_PLAYER_INDEX;

    /**
     * @deprecated Use DB_COLUMN_STATE_INDEX instead.
     */
    //public const STATE_INDEX = self::DB_COLUMN_STATE_INDEX;

    /**
     * @deprecated Use DB_COLUMN_STATE instead.
     */
    //public const STATE = self::DB_COLUMN_STATE;

    /**
     * @deprecated Use DB_COLUMN_USERNAME instead.
     */
    //public const USERNAME = self::DB_COLUMN_USERNAME;

    /**
     * @deprecated Use DB_COLUMN_FIRSTNAME instead.
     */
    //public const FIRSTNAME = self::DB_COLUMN_FIRSTNAME;

    /**
     * @deprecated Use DB_COLUMN_LASTNAME instead.
     */
    //public const LASTNAME = self::DB_COLUMN_LASTNAME;

    /**
     * @deprecated Use DB_COLUMN_EMAIL instead.
     */
    //public const EMAIL = self::DB_COLUMN_EMAIL;

    /**
     * @deprecated Use DB_COLUMN_ROLE instead.
     */
    //public const ROLE = self::DB_COLUMN_ROLE;

    /**
     * @deprecated Use DB_COLUMN_PREFERRED_LANGUAGE instead.
     */
    //public const PREFERRED_LANGUAGE = self::DB_COLUMN_PREFERRED_LANGUAGE;

    /**
     * @deprecated Use DB_COLUMN_PREFERRED_CAMERA_MODE instead.
     */
    //public const PREFERRED_CAMERA_MODE = self::DB_COLUMN_PREFERRED_CAMERA_MODE;

    /**
     * @deprecated Use DB_COLUMN_PASSWORD_HASH instead.
     */
    //public const PASSWORD_HASH = self::DB_COLUMN_PASSWORD_HASH;

    /**
     * @deprecated Use DB_COLUMN_RESET_TOKEN instead.
     */
    //public const RESET_TOKEN = self::DB_COLUMN_RESET_TOKEN;

    /**
     * @deprecated Use DB_COLUMN_RESET_TOKEN_EXPIRES_AT instead.
     */
    //public const RESET_TOKEN_EXPIRES_AT = self::DB_COLUMN_RESET_TOKEN_EXPIRES_AT;

    /**
     * @deprecated Use DB_COLUMN_LAST_LOGIN instead.
     */
    //public const LAST_LOGIN = self::DB_COLUMN_LAST_LOGIN;

    /**
     * @deprecated Use DB_COLUMN_LOGINS instead.
     */
    //public const LOGINS = self::DB_COLUMN_LOGINS;

    /**
     * @deprecated Use DB_COLUMN_GAMES_CREATED instead.
     */
    public const GAMES_CREATED = self::DB_COLUMN_GAMES_CREATED;

    /**
     * @deprecated Use DB_COLUMN_GAMES_PARTICIPATED instead.
     */
    public const GAMES_PARTICIPATED = self::DB_COLUMN_GAMES_PARTICIPATED;

    /**
     * @deprecated Use DB_COLUMN_GAMES_WON instead.
     */
    public const GAMES_WON = self::DB_COLUMN_GAMES_WON;

    /**
     * @deprecated Use DB_COLUMN_REGISTRATION_ENABLED instead.
     */
    public const REGISTRATION_ENABLED = self::DB_COLUMN_REGISTRATION_ENABLED;

    /**
     * @deprecated Use DB_COLUMN_LOGIN_ENABLED instead.
     */
    public const LOGIN_ENABLED = self::DB_COLUMN_LOGIN_ENABLED;

    /**
     * @deprecated Use DB_COLUMN_SYSTEM_ENABLED instead.
     */
    public const SYSTEM_ENABLED = self::DB_COLUMN_SYSTEM_ENABLED;

    /**
     * @deprecated Use DB_COLUMN_LOGGING_DEBUG_ENABLED instead.
     */
    public const LOGGING_DEBUG_ENABLED = self::DB_COLUMN_LOGGING_DEBUG_ENABLED;

    /**
     * @deprecated Use DB_COLUMN_GAME_CREATION_ENABLED instead.
     */
    public const GAME_CREATION_ENABLED = self::DB_COLUMN_GAME_CREATION_ENABLED;

    /**
     * @deprecated Use DB_COLUMN_GAME_PLAY_ENABLED instead.
     */
    public const GAME_PLAY_ENABLED = self::DB_COLUMN_GAME_PLAY_ENABLED;

    /**
     * @deprecated Use DB_COLUMN_MAINTENANCE_MODE_ENABLED instead.
     */
    public const MAINTENANCE_MODE_ENABLED = self::DB_COLUMN_MAINTENANCE_MODE_ENABLED;

    /**
     * @deprecated Use DB_COLUMN_MAINTENANCE_MESSAGE instead.
     */
    public const MAINTENANCE_MESSAGE = self::DB_COLUMN_MAINTENANCE_MESSAGE;

    /**
     * @deprecated Use DB_COLUMN_SYSTEM_NOTICE_ENABLED instead.
     */
    public const SYSTEM_NOTICE_ENABLED = self::DB_COLUMN_SYSTEM_NOTICE_ENABLED;

    /**
     * @deprecated Use DB_COLUMN_SYSTEM_NOTICE_MESSAGE instead.
     */
    public const SYSTEM_NOTICE_MESSAGE = self::DB_COLUMN_SYSTEM_NOTICE_MESSAGE;

    /**
     * @deprecated Use DB_COLUMN_UPDATED_BY instead.
     */
    public const UPDATED_BY = self::DB_COLUMN_UPDATED_BY;
}
