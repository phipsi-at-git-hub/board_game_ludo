<?php
// src/Constants/Application.php

namespace App\Constants;

use App\Constants\Database\DbColumnConstants;
use App\Constants\Database\DbTableConstants;
use App\Constants\Game\GameCameraModeConstants;
use App\Constants\Game\GameFigureAreaConstants;
use App\Constants\Game\GameRulesetConstants;
use App\Constants\Game\GameStatusConstants;
use App\Constants\Game\GameVisibilityConstants;
use App\Constants\User\UserRelationConstants;
use App\Constants\User\UserRole;
use App\Constants\User\UserRoleConstants;
use App\Constants\User\UserStatusConstants;

class Application
{
    /*
     * General application values
     */

    /**
     * Defines general application actions.
     */
    public const ACTION_ACCESS = 'access';
    public const ACTION_JOIN = 'join';
    public const ACTION_EDIT = 'edit';
    public const ACTION_DELETE = 'delete';

    /**
     * Defines generic on/off and partial states.
     */
    public const VALUE_ON = 'on';
    public const VALUE_OFF = 'off';
    public const VALUE_PARTIAL = 'partial';

    /**
     * Defines generic operation results.
     */
    public const RESULT_OK = 'ok';
    public const RESULT_FAIL = 'fail';

    /**
     * Defines application environments.
     */
    public const ENVIRONMENT_DEV = 'dev';
    public const ENVIRONMENT_PROD = 'prod';

    /**
     * Defines health check identifiers and health states.
     */
    public const HEALTH_CHECK = 'health';
    public const HEALTH_STATUS_HEALTHY = 'healthy';
    public const HEALTH_STATUS_CRITICAL = 'critical';
    public const HEALTH_STATUS_WARNING = 'warning';


    /*
     * Application responses
     */

    /**
     * Defines response notification types.
     */
    public const RESPONSE_TYPE_SUCCESS = 'success';
    public const RESPONSE_TYPE_ERROR = 'error';
    public const RESPONSE_TYPE_WARNING = 'warning';
    public const RESPONSE_TYPE_INFO = 'info';


    /*
     * Database tables
     */

    /**
     * Defines database table names.
     */
    public const DB_TABLE_USERS = DbTableConstants::USERS;
    public const DB_TABLE_USER_STATISTICS = DbTableConstants::USER_STATISTICS;
    public const DB_TABLE_GAMES = DbTableConstants::GAMES;
    public const DB_TABLE_GAME_RULE_SET = DbTableConstants::GAME_RULE_SET;
    public const DB_TABLE_GAME_STATE = DbTableConstants::GAME_STATE;
    public const DB_TABLE_GAME_STATE_PLAYERS = DbTableConstants::GAME_STATE_PLAYERS;
    public const DB_TABLE_GAME_STATE_FIGURES = DbTableConstants::GAME_STATE_FIGURES;
    public const DB_TABLE_GAME_HISTORY = DbTableConstants::GAME_HISTORY;
    public const DB_TABLE_SYSTEM_SETTINGS = DbTableConstants::SYSTEM_SETTINGS;


    /*
     * Database columns
     */

    /**
     * Defines common database column names.
     */
    public const DB_COLUMN_ID = DbColumnConstants::ID;
    public const DB_COLUMN_NAME = DbColumnConstants::NAME;
    public const DB_COLUMN_GAME_NAME = DbColumnConstants::GAME_CREATION_ENABLED;
    public const DB_COLUMN_GAME_ID = DbColumnConstants::GAME_ID;
    public const DB_COLUMN_USER_ID = DbColumnConstants::USER_ID;
    public const DB_COLUMN_STATUS = DbColumnConstants::STATUS;
    public const DB_COLUMN_CREATED_AT = DbColumnConstants::CREATED_AT;
    public const DB_COLUMN_UPDATED_AT = DbColumnConstants::UPDATED_AT;

    /**
     * Defines game database columns.
     */
    public const DB_COLUMN_CREATED_BY_USER_ID = DbColumnConstants::CREATED_BY_USER_ID;
    public const DB_COLUMN_IS_PRIVATE = DbColumnConstants::IS_PRIVATE;
    public const DB_COLUMN_IS_LOCKED = DbColumnConstants::IS_LOCKED;
    public const DB_COLUMN_IS_TEST_GAME = DbColumnConstants::IS_TEST_GAME;

    /**
     * Defines game rule set database columns.
     */
    public const DB_COLUMN_ALLOW_BOTS = DbColumnConstants::ALLOW_BOTS;
    public const DB_COLUMN_ALL_FIGURES_START_AT_HOME = DbColumnConstants::ALL_FIGURES_START_AT_HOME;
    public const DB_COLUMN_LEAVE_HOME_ATTEMPT = DbColumnConstants::LEAVE_HOME_ATTEMPT;
    public const DB_COLUMN_LEAVE_HOME_ATTEMPTS_MAX = DbColumnConstants::LEAVE_HOME_ATTEMPTS_MAX;
    public const DB_COLUMN_EXTRA_ROLL_ON_SIX_LIMIT = DbColumnConstants::EXTRA_ROLL_ON_SIX_LIMIT;
    public const DB_COLUMN_FORCE_LEAVING_HOME_ON_SIX = DbColumnConstants::FORCE_LEAVING_HOME_ON_SIX;
    public const DB_COLUMN_FORCE_CAPTURE_ENEMY_FIGURES = DbColumnConstants::FORCE_CAPTURE_ENEMY_FIGURES;
    public const DB_COLUMN_FORCE_EXTRA_LAP_ON_OVERFLOW = DbColumnConstants::FORCE_EXTRA_LAP_ON_OVERFLOW;
    public const DB_COLUMN_ALLOW_STACK_OWN_FIGURES = DbColumnConstants::ALLOW_STACK_OWN_FIGURES;
    public const DB_COLUMN_STRICT_GOAL_ORDER = DbColumnConstants::STRICT_GOAL_ORDER;
    public const DB_COLUMN_START_FIELD_MUST_BE_CLEARED = DbColumnConstants::START_FIELD_MUST_BE_CLEARED;
    public const DB_COLUMN_ENUM_FIRST_FIGURE = DbColumnConstants::ENUM_FIRST_FIGURE;
    public const DB_COLUMN_ENUM_ALL_FIGURES = DbColumnConstants::ENUM_ALL_FIGURES;

    /**
     * Defines game state database columns.
     */
    public const DB_COLUMN_CURRENT_PLAYER_INDEX = DbColumnConstants::CURRENT_PLAYER_INDEX;
    public const DB_COLUMN_CURRENT_DICE_ROLL = DbColumnConstants::CURRENT_DICE_ROLL;
    public const DB_COLUMN_CURRENT_TURN_COUNTER = DbColumnConstants::CURRENT_TURN_COUNTER;
    public const DB_COLUMN_LEAVE_HOME_ATTEMPTS_USED = DbColumnConstants::LEAVE_HOME_ATTEMPTS_USED;
    public const DB_COLUMN_EXTRA_ROLLS_ON_SIX_USED = DbColumnConstants::EXTRA_ROLLS_ON_SIX_USED;
    public const DB_COLUMN_WINNER_USER_ID = DbColumnConstants::WINNER_USER_ID;

    /**
     * Defines game figure database columns.
     */
    public const DB_COLUMN_FIGURE_INDEX = DbColumnConstants::FIGURE_INDEX;
    public const DB_COLUMN_POSITION = DbColumnConstants::POSITION;
    public const DB_COLUMN_AREA = DbColumnConstants::AREA;

    /**
     * Defines game state player database columns.
     */
    public const DB_COLUMN_PLAYER_INDEX = DbColumnConstants::PLAYER_INDEX;

    /**
     * Defines game history database columns.
     */
    public const DB_COLUMN_STATE_INDEX = DbColumnConstants::STATE_INDEX;
    public const DB_COLUMN_STATE = DbColumnConstants::STATE;

    /**
     * Defines user database columns.
     */
    public const DB_COLUMN_USERNAME = DbColumnConstants::USERNAME;
    public const DB_COLUMN_FIRSTNAME = DbColumnConstants::FIRSTNAME;
    public const DB_COLUMN_LASTNAME = DbColumnConstants::LASTNAME;
    public const DB_COLUMN_EMAIL = DbColumnConstants::EMAIL;
    public const DB_COLUMN_ROLE = DbColumnConstants::ROLE;
    public const DB_COLUMN_PREFERRED_LANGUAGE = DbColumnConstants::PREFERRED_LANGUAGE;
    public const DB_COLUMN_PREFERRED_CAMERA_MODE = DbColumnConstants::PREFERRED_CAMERA_MODE;
    public const DB_COLUMN_PASSWORD_HASH = DbColumnConstants::PASSWORD_HASH;
    public const DB_COLUMN_RESET_TOKEN = DbColumnConstants::RESET_TOKEN;
    public const DB_COLUMN_RESET_TOKEN_EXPIRES_AT = DbColumnConstants::RESET_TOKEN_EXPIRES_AT;
    public const DB_COLUMN_LAST_LOGIN = DbColumnConstants::LAST_LOGIN;

    /**
     * Defines user statistics database columns.
     */
    public const DB_COLUMN_LOGINS = DbColumnConstants::LOGINS;
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
     * User domain values
     */

    /**
     * Defines user roles.
     */
    public const USER_ROLE_USER = UserRoleConstants::USER;
    public const USER_ROLE_ADMIN = UserRoleConstants::ADMIN; 
    public const USER_ROLE_MODERATOR = UserRoleConstants::MODERATOR;
    public const USER_ROLE_GAME_MASTER = UserRoleConstants::GAME_MASTER;

    /**
     * Defines user account statuses.
     */
    public const USER_STATUS_ACTIVE = UserStatusConstants::ACTIVE;
    public const USER_STATUS_INACTIVE = UserStatusConstants::INACTIVE;
    public const USER_STATUS_CLOSED = UserStatusConstants::CLOSED;
    public const USER_STATUS_BLOCKED = UserStatusConstants::BLOCKED;
    public const USER_STATUS_BANNED = UserStatusConstants::BANNED;

    /**
     * Defines user relation types.
     */
    public const USER_RELATION_CREATED = UserRelationConstants::CREATED;
    public const USER_RELATION_PARTICIPATED = UserRelationConstants::PARTICIPATED;
    public const USER_RELATION_WON = UserRelationConstants::WON;


    /*
     * Game domain values
     */

    /**
     * Defines game lifecycle statuses.
     */
    public const GAME_STATUS_WAITING = GameStatusConstants::WAITING;
    public const GAME_STATUS_RUNNING = GameStatusConstants::RUNNING;
    public const GAME_STATUS_FINISHED = GameStatusConstants::FINISHED;
    public const GAME_STATUS_CANCELLED = GameStatusConstants::CANCELLED;

    /**
     * Defines game rule set types.
     */
    public const GAME_RULESET_CLASSIC = GameRulesetConstants::CLASSIC;
    public const GAME_RULESET_ADVANCED = GameRulesetConstants::ADVANCED;
    public const GAME_RULESET_ORIGINAL = GameRulesetConstants::ORIGINAL;
    public const GAME_RULESET_CUSTOM = GameRulesetConstants::CUSTOM;

    /**
     * Defines game camera modes.
     */
    public const GAME_CAMERA_MODE_FIXED = GameCameraModeConstants::FIXED;
    public const GAME_CAMERA_MODE_FOLLOW = GameCameraModeConstants::FOLLOW;

    /**
     * Defines game visibility values.
     */
    public const GAME_VISIBILITY_PRIVATE = GameVisibilityConstants::PRIVATE;

    /**
     * Defines general game data keys.
     */
    public const GAME_KEY_PLAYER_COUNT = 'player_count';
    public const GAME_KEY_AVAILABLE_MOVES = 'available_moves';

    /**
     * Defines figure areas.
     */
    public const FIGURE_AREA_HOME = GameFigureAreaConstants::HOME;
    public const FIGURE_AREA_FIELD = GameFigureAreaConstants::FIELD;
    public const FIGURE_AREA_GOAL = GameFigureAreaConstants::GOAL;


    /*
     * DTO keys
     */

    /**
     * Defines game DTO keys.
     */
    public const DTO_GAME_ID = 'game_id';
    public const DTO_GAME_NAME = 'game_name';
    public const DTO_GAME_STATUS = 'game_status';
    public const DTO_GAME_TURN = 'game_turn';
    public const DTO_PLAYERS = 'players';
    public const DTO_CURRENT_PLAYER_ID = 'current_player_id';
    public const DTO_CURRENT_PLAYER_INDEX = 'current_player_index';
    public const DTO_CURRENT_PLAYER_USERNAME = 'current_player_username';
    public const DTO_WINNER_USER_ID = 'winner_user_id';
    public const DTO_WINNER_PLAYER_INDEX = 'winner_player_index';
    public const DTO_CURRENT_DICE_ROLL = 'current_dice_roll';
    public const DTO_RULESET = 'ruleset';

    /**
     * Defines player DTO keys.
     */
    public const DTO_USER_ID = 'user_id';
    public const DTO_USERNAME = 'username';
    public const DTO_PLAYER_INDEX = 'player_index';
    public const DTO_PLAYER_COUNT_EMPTY = 'empty';
    public const DTO_PLAYER_COUNT_LOW = 'low';
    public const DTO_PLAYER_COUNT_READY = 'ready';

    /**
     * Defines figure DTO keys.
     */
    public const DTO_FIGURES = 'figures';
    public const DTO_FIGURE_INDEX = 'figure_index';
    public const DTO_AREA = 'area';
    public const DTO_POSITION = 'position';

    /**
     * Defines move DTO keys.
     */
    public const DTO_MOVE = 'move';
    public const DTO_FROM = 'from';
    public const DTO_TO = 'to';
    public const DTO_ABSOLUTE_TARGET = 'absolute_target';
    public const DTO_IS_KICK = 'is_kick';
    public const DTO_KICKED_PLAYER_ID = 'kicked_player_id';
    public const DTO_KICKED_PLAYER_INDEX = 'kicked_player_index';
    public const DTO_KICKED_FIGURE_INDEX = 'kicked_figure_index';
    public const DTO_IS_GOAL_ENTRY = 'is_goal_entry';
    public const DTO_IS_LAP_OVERFLOW = 'is_lap_overflow';
    public const DTO_IS_PASS = 'is_pass';


    /*
     * HTTP request values
     */

    /**
     * Defines the server variable used to determine the request method.
     */
    public const HTTP_SERVER_REQUEST_METHOD = 'REQUEST_METHOD';

    /**
     * Defines supported HTTP request methods.
     */
    public const HTTP_METHOD_GET = 'GET';
    public const HTTP_METHOD_POST = 'POST';
    public const HTTP_METHOD_PUT = 'PUT';
    public const HTTP_METHOD_DELETE = 'DELETE';


    /*
     * Date and time formats
     */

    /**
     * Defines date and time formats used for file-related values.
     */
    public const DATE_FORMAT_FILE_DATE = 'Y-m-d';
    public const DATE_FORMAT_FILE_TIME = 'H:i:s';
    public const DATE_FORMAT_FILE_DATE_TIME = 'Y-m-d H:i:s';


    /*
     * Localization
     */

    /**
     * Defines the locale key and supported locale identifiers.
     */
    public const LOCALIZATION_LOCALE_KEY = 'locale';
    public const LOCALE_EN_US = 'en-us';
    public const LOCALE_DE_DE = 'de-de';


    /*
     * Storage
     */

    /**
     * Defines supported storage types.
     */
    public const STORAGE_TYPE_FILE = 'file';
    public const STORAGE_TYPE_DATABASE = 'database';


    /*
     * Sorting
     */

    /**
     * Defines sort directions.
     */
    public const SORT_DIRECTION_ASC = 'asc';
    public const SORT_DIRECTION_DESC = 'desc';

    /**
     * Defines supported logging sort keys.
     */
    public const SORT_KEY_TIMESTAMP = 'timestamp';
    public const SORT_KEY_CHANNEL = 'channel';
    public const SORT_KEY_LOG_LEVEL = 'log_level';


    /*
     * Deprecated constants
     *
     * These aliases preserve compatibility with existing code.
     * Migrate usages to the corresponding replacement constants.
     */

    /**
     * @deprecated Use ACTION_ACCESS instead.
     */
    public const GENERAL_ACCESS = self::ACTION_ACCESS;

    /**
     * @deprecated Use ACTION_JOIN instead.
     */
    public const GENERAL_JOIN = self::ACTION_JOIN;

    /**
     * @deprecated Use ACTION_EDIT instead.
     */
    public const GENERAL_EDIT = self::ACTION_EDIT;

    /**
     * @deprecated Use ACTION_DELETE instead.
     */
    public const GENERAL_DELETE = self::ACTION_DELETE;

    /**
     * @deprecated Use VALUE_ON instead.
     */
    public const GENERAL_ON = self::VALUE_ON;

    /**
     * @deprecated Use VALUE_OFF instead.
     */
    public const GENERAL_OFF = self::VALUE_OFF;

    /**
     * @deprecated Use VALUE_PARTIAL instead.
     */
    public const GENERAL_PARTIAL = self::VALUE_PARTIAL;

    /**
     * @deprecated Use RESULT_OK instead.
     */
    public const GENERAL_OK = self::RESULT_OK;

    /**
     * @deprecated Use RESULT_FAIL instead.
     */
    public const GENERAL_FAIL = self::RESULT_FAIL;

    /**
     * @deprecated Use HEALTH_CHECK instead.
     */
    public const GENERAL_HEALTH = self::HEALTH_CHECK;

    /**
     * @deprecated Use ENVIRONMENT_DEV instead.
     */
    public const GENERAL_DEV = self::ENVIRONMENT_DEV;

    /**
     * @deprecated Use ENVIRONMENT_PROD instead.
     */
    public const GENERAL_PROD = self::ENVIRONMENT_PROD;

    /**
     * @deprecated Use HEALTH_STATUS_HEALTHY instead.
     */
    public const GENERAL_HEALTHY = self::HEALTH_STATUS_HEALTHY;

    /**
     * @deprecated Use HEALTH_STATUS_CRITICAL instead.
     */
    public const GENERAL_CRITICAL = self::HEALTH_STATUS_CRITICAL;

    /**
     * @deprecated Use HEALTH_STATUS_WARNING instead.
     */
    public const GENERAL_WARNING = self::HEALTH_STATUS_WARNING;


    /*
     * Deprecated database table aliases
     */

    /**
     * @deprecated Use DB_TABLE_USERS instead.
     */
    public const TABLE_USERS = self::DB_TABLE_USERS;

    /**
     * @deprecated Use DB_TABLE_USER_STATISTICS instead.
     */
    public const TABLE_USERS_STATISTICS = self::DB_TABLE_USER_STATISTICS;

    /**
     * @deprecated Use DB_TABLE_GAMES instead.
     */
    public const TABLE_GAMES = self::DB_TABLE_GAMES;

    /**
     * @deprecated Use DB_TABLE_GAME_RULE_SET instead.
     */
    public const TABLE_RULES = self::DB_TABLE_GAME_RULE_SET;

    /**
     * @deprecated Use DB_TABLE_GAME_STATE instead.
     */
    public const TABLE_STATE = self::DB_TABLE_GAME_STATE;

    /**
     * @deprecated Use DB_TABLE_GAME_STATE_PLAYERS instead.
     */
    public const TABLE_PLAYERS = self::DB_TABLE_GAME_STATE_PLAYERS;

    /**
     * @deprecated Use DB_TABLE_GAME_STATE_FIGURES instead.
     */
    public const TABLE_FIGURES = self::DB_TABLE_GAME_STATE_FIGURES;

    /**
     * @deprecated Use DB_TABLE_GAME_HISTORY instead.
     */
    public const TABLE_HISTORY = self::DB_TABLE_GAME_HISTORY;

    /**
     * @deprecated Use DB_TABLE_SYSTEM_SETTINGS instead.
     */
    public const TABLE_SYSTEM_SETTINGS = self::DB_TABLE_SYSTEM_SETTINGS;


    /*
     * Deprecated database column aliases
     */

    /**
     * @deprecated Use DB_COLUMN_ID instead.
     */
    public const ID = self::DB_COLUMN_ID;

    /**
     * @deprecated Use DB_COLUMN_NAME instead.
     */
    public const NAME = self::DB_COLUMN_NAME;

    /**
     * @deprecated Use DB_COLUMN_GAME_NAME instead.
     */
    public const GAME_NAME = self::DB_COLUMN_GAME_NAME;

    /**
     * @deprecated Use DB_COLUMN_GAME_ID instead.
     */
    public const GAME_ID = self::DB_COLUMN_GAME_ID;

    /**
     * @deprecated Use DB_COLUMN_USER_ID instead.
     */
    public const USER_ID = self::DB_COLUMN_USER_ID;

    /**
     * @deprecated Use DB_COLUMN_STATUS instead.
     */
    public const STATUS = self::DB_COLUMN_STATUS;

    /**
     * @deprecated Use DB_COLUMN_CREATED_AT instead.
     */
    public const CREATED_AT = self::DB_COLUMN_CREATED_AT;

    /**
     * @deprecated Use DB_COLUMN_UPDATED_AT instead.
     */
    public const UPDATED_AT = self::DB_COLUMN_UPDATED_AT;

    /**
     * @deprecated Use DB_COLUMN_CREATED_BY_USER_ID instead.
     */
    public const CREATED_BY_USER_ID = self::DB_COLUMN_CREATED_BY_USER_ID;

    /**
     * @deprecated Use DB_COLUMN_IS_PRIVATE instead.
     */
    public const IS_PRIVATE = self::DB_COLUMN_IS_PRIVATE;

    /**
     * @deprecated Use DB_COLUMN_IS_LOCKED instead.
     */
    public const IS_LOCKED = self::DB_COLUMN_IS_LOCKED;

    /**
     * @deprecated Use DB_COLUMN_IS_TEST_GAME instead.
     */
    public const IS_TEST_GAME = self::DB_COLUMN_IS_TEST_GAME;

    /**
     * @deprecated Use DB_COLUMN_ALLOW_BOTS instead.
     */
    public const ALLOW_BOTS = self::DB_COLUMN_ALLOW_BOTS;

    /**
     * @deprecated Use DB_COLUMN_ALL_FIGURES_START_AT_HOME instead.
     */
    public const ALL_FIGURES_START_AT_HOME = self::DB_COLUMN_ALL_FIGURES_START_AT_HOME;

    /**
     * @deprecated Use DB_COLUMN_LEAVE_HOME_ATTEMPT instead.
     */
    public const LEAVE_HOME_ATTEMPT = self::DB_COLUMN_LEAVE_HOME_ATTEMPT;

    /**
     * @deprecated Use DB_COLUMN_LEAVE_HOME_ATTEMPTS_MAX instead.
     */
    public const LEAVE_HOME_ATTEMPTS_MAX = self::DB_COLUMN_LEAVE_HOME_ATTEMPTS_MAX;

    /**
     * @deprecated Use DB_COLUMN_EXTRA_ROLL_ON_SIX_LIMIT instead.
     */
    public const EXTRA_ROLL_ON_SIX_LIMIT = self::DB_COLUMN_EXTRA_ROLL_ON_SIX_LIMIT;

    /**
     * @deprecated Use DB_COLUMN_FORCE_LEAVING_HOME_ON_SIX instead.
     */
    public const FORCE_LEAVING_HOME_ON_SIX = self::DB_COLUMN_FORCE_LEAVING_HOME_ON_SIX;

    /**
     * @deprecated Use DB_COLUMN_FORCE_CAPTURE_ENEMY_FIGURES instead.
     */
    public const FORCE_CAPTURE_ENEMY_FIGURES = self::DB_COLUMN_FORCE_CAPTURE_ENEMY_FIGURES;

    /**
     * @deprecated Use DB_COLUMN_FORCE_EXTRA_LAP_ON_OVERFLOW instead.
     */
    public const FORCE_EXTRA_LAP_ON_OVERFLOW = self::DB_COLUMN_FORCE_EXTRA_LAP_ON_OVERFLOW;

    /**
     * @deprecated Use DB_COLUMN_ALLOW_STACK_OWN_FIGURES instead.
     */
    public const ALLOW_STACK_OWN_FIGURES = self::DB_COLUMN_ALLOW_STACK_OWN_FIGURES;

    /**
     * @deprecated Use DB_COLUMN_STRICT_GOAL_ORDER instead.
     */
    public const STRICT_GOAL_ORDER = self::DB_COLUMN_STRICT_GOAL_ORDER;

    /**
     * @deprecated Use DB_COLUMN_START_FIELD_MUST_BE_CLEARED instead.
     */
    public const START_FIELD_MUST_BE_CLEARED = self::DB_COLUMN_START_FIELD_MUST_BE_CLEARED;

    /**
     * @deprecated Use DB_COLUMN_ENUM_FIRST_FIGURE instead.
     */
    public const ENUM_FIRST_FIGURE = self::DB_COLUMN_ENUM_FIRST_FIGURE;

    /**
     * @deprecated Use DB_COLUMN_ENUM_ALL_FIGURES instead.
     */
    public const ENUM_ALL_FIGURES = self::DB_COLUMN_ENUM_ALL_FIGURES;

    /**
     * @deprecated Use DB_COLUMN_CURRENT_PLAYER_INDEX instead.
     */
    public const CURRENT_PLAYER_INDEX = self::DB_COLUMN_CURRENT_PLAYER_INDEX;

    /**
     * @deprecated Use DB_COLUMN_CURRENT_DICE_ROLL instead.
     */
    public const CURRENT_DICE_ROLL = self::DB_COLUMN_CURRENT_DICE_ROLL;

    /**
     * @deprecated Use DB_COLUMN_CURRENT_TURN_COUNTER instead.
     */
    public const CURRENT_TURN_COUNTER = self::DB_COLUMN_CURRENT_TURN_COUNTER;

    /**
     * @deprecated Use DB_COLUMN_LEAVE_HOME_ATTEMPTS_USED instead.
     */
    public const LEAVE_HOME_ATTEMPTS_USED = self::DB_COLUMN_LEAVE_HOME_ATTEMPTS_USED;

    /**
     * @deprecated Use DB_COLUMN_EXTRA_ROLLS_ON_SIX_USED instead.
     */
    public const EXTRA_ROLLS_ON_SIX_USED = self::DB_COLUMN_EXTRA_ROLLS_ON_SIX_USED;

    /**
     * @deprecated Use DB_COLUMN_WINNER_USER_ID instead.
     */
    public const WINNER_USER_ID = self::DB_COLUMN_WINNER_USER_ID;

    /**
     * @deprecated Use DB_COLUMN_FIGURE_INDEX instead.
     */
    public const FIGURE_INDEX = self::DB_COLUMN_FIGURE_INDEX;

    /**
     * @deprecated Use DB_COLUMN_POSITION instead.
     */
    public const POSITION = self::DB_COLUMN_POSITION;

    /**
     * @deprecated Use DB_COLUMN_AREA instead.
     */
    public const AREA = self::DB_COLUMN_AREA;

    /**
     * @deprecated Use DB_COLUMN_PLAYER_INDEX instead.
     */
    public const PLAYER_INDEX = self::DB_COLUMN_PLAYER_INDEX;

    /**
     * @deprecated Use DB_COLUMN_STATE_INDEX instead.
     */
    public const STATE_INDEX = self::DB_COLUMN_STATE_INDEX;

    /**
     * @deprecated Use DB_COLUMN_STATE instead.
     */
    public const STATE = self::DB_COLUMN_STATE;

    /**
     * @deprecated Use DB_COLUMN_USERNAME instead.
     */
    public const USERNAME = self::DB_COLUMN_USERNAME;

    /**
     * @deprecated Use DB_COLUMN_FIRSTNAME instead.
     */
    public const FIRSTNAME = self::DB_COLUMN_FIRSTNAME;

    /**
     * @deprecated Use DB_COLUMN_LASTNAME instead.
     */
    public const LASTNAME = self::DB_COLUMN_LASTNAME;

    /**
     * @deprecated Use DB_COLUMN_EMAIL instead.
     */
    public const EMAIL = self::DB_COLUMN_EMAIL;

    /**
     * @deprecated Use DB_COLUMN_ROLE instead.
     */
    public const ROLE = self::DB_COLUMN_ROLE;

    /**
     * @deprecated Use DB_COLUMN_PREFERRED_LANGUAGE instead.
     */
    public const PREFERRED_LANGUAGE = self::DB_COLUMN_PREFERRED_LANGUAGE;

    /**
     * @deprecated Use DB_COLUMN_PREFERRED_CAMERA_MODE instead.
     */
    public const PREFERRED_CAMERA_MODE = self::DB_COLUMN_PREFERRED_CAMERA_MODE;

    /**
     * @deprecated Use DB_COLUMN_PASSWORD_HASH instead.
     */
    public const PASSWORD_HASH = self::DB_COLUMN_PASSWORD_HASH;

    /**
     * @deprecated Use DB_COLUMN_RESET_TOKEN instead.
     */
    public const RESET_TOKEN = self::DB_COLUMN_RESET_TOKEN;

    /**
     * @deprecated Use DB_COLUMN_RESET_TOKEN_EXPIRES_AT instead.
     */
    public const RESET_TOKEN_EXPIRES_AT = self::DB_COLUMN_RESET_TOKEN_EXPIRES_AT;

    /**
     * @deprecated Use DB_COLUMN_LAST_LOGIN instead.
     */
    public const LAST_LOGIN = self::DB_COLUMN_LAST_LOGIN;

    /**
     * @deprecated Use DB_COLUMN_LOGINS instead.
     */
    public const LOGINS = self::DB_COLUMN_LOGINS;

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


    /*
     * Deprecated user aliases
     */

    /**
     * @deprecated Use USER_ROLE_USER instead.
     */
    public const USER = self::USER_ROLE_USER;

    /**
     * @deprecated Use USER_ROLE_ADMIN instead.
     */
    public const ADMIN = self::USER_ROLE_ADMIN;

    /**
     * @deprecated Use USER_ROLE_MODERATOR instead.
     */
    public const MODERATOR = self::USER_ROLE_MODERATOR;

    /**
     * @deprecated Use USER_ROLE_GAME_MASTER instead.
     */
    public const GAME_MASTER = self::USER_ROLE_GAME_MASTER;

    /**
     * @deprecated Use USER_STATUS_ACTIVE instead.
     */
    public const ACTIVE = self::USER_STATUS_ACTIVE;

    /**
     * @deprecated Use USER_STATUS_INACTIVE instead.
     */
    public const INACTIVE = self::USER_STATUS_INACTIVE;

    /**
     * @deprecated Use USER_STATUS_CLOSED instead.
     */
    public const CLOSED = self::USER_STATUS_CLOSED;

    /**
     * @deprecated Use USER_STATUS_BLOCKED instead.
     */
    public const BLOCKED = self::USER_STATUS_BLOCKED;

    /**
     * @deprecated Use USER_STATUS_BANNED instead.
     */
    public const BANNED = self::USER_STATUS_BANNED;


    /*
     * Deprecated game aliases
     */

    /**
     * @deprecated Use GAME_STATUS_WAITING instead.
     */
    public const STATUS_WAITING = self::GAME_STATUS_WAITING;

    /**
     * @deprecated Use GAME_STATUS_RUNNING instead.
     */
    public const STATUS_RUNNING = self::GAME_STATUS_RUNNING;

    /**
     * @deprecated Use GAME_STATUS_FINISHED instead.
     */
    public const STATUS_FINISHED = self::GAME_STATUS_FINISHED;

    /**
     * @deprecated Use GAME_STATUS_CANCELLED instead.
     */
    public const STATUS_CANCELLED = self::GAME_STATUS_CANCELLED;

    /**
     * @deprecated Use GAME_CAMERA_MODE_FIXED instead.
     */
    public const CAMERA_MODE_FIXED = self::GAME_CAMERA_MODE_FIXED;

    /**
     * @deprecated Use GAME_CAMERA_MODE_FOLLOW instead.
     */
    public const CAMERA_MODE_FOLLOW = self::GAME_CAMERA_MODE_FOLLOW;

    /**
     * @deprecated Use GAME_VISIBILITY_PRIVATE instead.
     */
    public const VISIBILITY_PRIVATE = self::GAME_VISIBILITY_PRIVATE;

    /**
     * @deprecated Use GAME_KEY_PLAYER_COUNT instead.
     */
    public const PLAYER_COUNT = self::GAME_KEY_PLAYER_COUNT;

    /**
     * @deprecated Use GAME_KEY_AVAILABLE_MOVES instead.
     */
    public const AVAILABLE_MOVES = self::GAME_KEY_AVAILABLE_MOVES;

    /**
     * @deprecated Use FIGURE_AREA_HOME instead.
     */
    public const AREA_HOME = self::FIGURE_AREA_HOME;

    /**
     * @deprecated Use FIGURE_AREA_FIELD instead.
     */
    public const AREA_FIELD = self::FIGURE_AREA_FIELD;

    /**
     * @deprecated Use FIGURE_AREA_GOAL instead.
     */
    public const AREA_GOAL = self::FIGURE_AREA_GOAL;


    /*
     * Deprecated HTTP aliases
     */

    /**
     * @deprecated Use HTTP_SERVER_REQUEST_METHOD instead.
     */
    public const REQUEST_METHOD = self::HTTP_SERVER_REQUEST_METHOD;

    /**
     * @deprecated Use HTTP_METHOD_GET instead.
     */
    public const REQUEST_METHOD_GET = self::HTTP_METHOD_GET;

    /**
     * @deprecated Use HTTP_METHOD_POST instead.
     */
    public const REQUEST_METHOD_POST = self::HTTP_METHOD_POST;

    /**
     * @deprecated Use HTTP_METHOD_PUT instead.
     */
    public const REQUEST_METHOD_PUT = self::HTTP_METHOD_PUT;

    /**
     * @deprecated Use HTTP_METHOD_DELETE instead.
     */
    public const REQUEST_METHOD_DELETE = self::HTTP_METHOD_DELETE;


    /*
     * Deprecated date and time aliases
     */

    /**
     * @deprecated Use DATE_FORMAT_FILE_DATE instead.
     */
    public const FILE_DATE_FORMAT = self::DATE_FORMAT_FILE_DATE;

    /**
     * @deprecated Use DATE_FORMAT_FILE_TIME instead.
     */
    public const FILE_TIME_FORMAT = self::DATE_FORMAT_FILE_TIME;

    /**
     * @deprecated Use DATE_FORMAT_FILE_DATE_TIME instead.
     */
    public const FILE_DATE_TIME_FORMAT = self::DATE_FORMAT_FILE_DATE_TIME;


    /*
     * Deprecated localization aliases
     */

    /**
     * @deprecated Use LOCALIZATION_LOCALE_KEY instead.
     */
    public const LOCALE = self::LOCALIZATION_LOCALE_KEY;

    /**
     * @deprecated Use LOCALE_EN_US instead.
     */
    public const EN_US = self::LOCALE_EN_US;

    /**
     * @deprecated Use LOCALE_DE_DE instead.
     */
    public const DE_DE = self::LOCALE_DE_DE;


    /*
     * Deprecated storage aliases
     */

    /**
     * @deprecated Use STORAGE_TYPE_FILE instead.
     */
    public const STORAGE_FILE = self::STORAGE_TYPE_FILE;

    /**
     * @deprecated Use STORAGE_TYPE_DATABASE instead.
     */
    public const STORAGE_DATABASE = self::STORAGE_TYPE_DATABASE;


    /*
     * Deprecated sorting aliases
     */

    /**
     * @deprecated Use SORT_DIRECTION_ASC instead.
     */
    public const ORDER_ASC = self::SORT_DIRECTION_ASC;

    /**
     * @deprecated Use SORT_DIRECTION_DESC instead.
     */
    public const ORDER_DESC = self::SORT_DIRECTION_DESC;

    /**
     * @deprecated Use SORT_KEY_TIMESTAMP instead.
     */
    public const ORDER_BY_TIMESTAMP = self::SORT_KEY_TIMESTAMP;

    /**
     * @deprecated Use SORT_KEY_CHANNEL instead.
     */
    public const ORDER_BY_CHANNEL = self::SORT_KEY_CHANNEL;

    /**
     * @deprecated Use SORT_KEY_LOG_LEVEL instead.
     */
    public const ORDER_BY_LOG_LEVEL = self::SORT_KEY_LOG_LEVEL;
}
