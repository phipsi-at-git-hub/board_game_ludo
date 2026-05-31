<?php
// src/Constants/Application.php
namespace App\Constants;

class Application {
    // Application general
    public const GENERAL_ACCESS = 'access';
    public const GENERAL_JOIN = 'join';
    public const GENERAL_EDIT = 'edit';
    public const GENERAL_DELETE = 'delete';

    // Database tables
    public const TABLE_USERS = 'users'; 
    public const TABLE_GAMES = 'games';
    public const TABLE_RULES = 'game_rule_set';
    public const TABLE_STATE = 'game_state';
    public const TABLE_PLAYERS = 'game_state_players';
    public const TABLE_FIGURES = 'game_state_figures';
    public const TABLE_SYSTEM_SETTINGS = 'system_settings'; 

    // Database field names - common
    public const ID = 'id';
    public const NAME = 'name';
    public const GAME_NAME = 'name';
    public const GAME_ID = 'game_id';
    public const USER_ID = 'user_id';
    public const STATUS = 'status';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Database field names - games
    public const CREATED_BY_USER_ID = 'created_by_user_id';
    public const IS_PRIVATE = 'is_private';
    public const IS_LOCKED = 'is_locked';
    public const IS_TEST_GAME = 'is_test_game'; 

    // Database field names - game_rule_set
    public const ALLOW_BOTS = 'allow_bots';
    public const ALL_FIGURES_START_AT_HOME = 'all_figures_start_at_home';
    public const LEAVE_HOME_ATTEMPT = 'leave_home_attempt'; 
    public const LEAVE_HOME_ATTEMPTS_MAX = 'leave_home_attempts_max'; 
    public const EXTRA_ROLL_ON_SIX_LIMIT = 'extra_roll_on_six_limit';
    public const FORCE_LEAVING_HOME_ON_SIX = 'force_leaving_home_on_six'; 
    public const FORCE_CAPTURE_ENEMY_FIGURES = 'force_capture_enemy_figures'; 
    public const FORCE_EXTRA_LAP_ON_OVERFLOW = 'force_extra_lap_on_overflow'; 
    public const ALLOW_STACK_OWN_FIGURES = 'allow_stack_own_figures';
    public const STRICT_GOAL_ORDER = 'strict_goal_order';
    public const START_FIELD_MUST_BE_CLEARED = 'start_field_must_be_cleared';
    public const ENUM_FIRST_FIGURE = 'first_figure';
    public const ENUM_ALL_FIGURES = 'all_figures';

    // Database field names - game_state
    public const CURRENT_PLAYER_INDEX = 'current_player_index';
    public const CURRENT_DICE_ROLL = 'current_dice_roll';
    public const CURRENT_TURN_COUNTER = 'current_turn_counter'; 
    public const LEAVE_HOME_ATTEMPTS_USED = 'leave_home_attempts_used'; 
    public const EXTRA_ROLLS_ON_SIX_USED = 'extra_rolls_on_six_used'; 
    public const WINNER_USER_ID = 'winner_user_id'; 

    // Database field names - game_state_figures
    public const FIGURE_INDEX = 'figure_index';
    public const POSITION = 'position';
    public const AREA = 'area';

    // Database field names - game_state_figures
    public const PLAYER_INDEX = 'player_index';

    // Database field names - users
    public const USERNAME = 'username';
    public const FIRSTNAME = 'firstname';
    public const LASTNAME = 'lastname';
    public const EMAIL = 'email';
    public const ROLE = 'role';
    public const PASSWORD_HASH = 'password_hash';
    public const RESET_TOKEN = 'reset_token';
    public const RESET_TOKEN_EXPIRES_AT = 'reset_token_expires_at';
    public const LAST_LOGIN = 'last_login';

    // Database field names - system_settings
    public const REGISTRATION_ENABLED = 'registration_enabled'; 
    public const LOGIN_ENABLED = 'login_enabled'; 
    public const SYSTEM_ENABLED = 'system_enabled'; 
    public const GAME_CREATION_ENABLED = 'game_creation_enabled'; 
    public const GAME_PLAY_ENABLED = 'game_play_enabled'; 
    public const MAINTENANCE_MODE_ENABLED = 'maintenance_mode_enabled'; 
    public const MAINTENANCE_MESSAGE = 'maintenance_message'; 
    public const SYSTEM_NOTICE_ENABLED = 'system_notice_enabled'; 
    public const SYSTEM_NOTICE_MESSAGE = 'system_notice_message'; 
    public const UPDATED_BY = 'updated_by'; 

    // User roles
    public const USER = 'user';
    public const ADMIN = 'admin';
    public const MODERATOR = 'moderator';
    public const GAME_MASTER = 'game_master';

    // User status
    public const ACTIVE = 'ACTIVE'; 
    public const INACTIVE = 'INACTIVE'; 
    public const CLOSED = 'CLOSED'; 
    public const BLOCKED = 'BLOCKED'; 
    public const BANNED = 'BANNED'; 

    // Game status
    public const STATUS_WAITING  = 'WAITING';
    public const STATUS_RUNNING  = 'RUNNING';
    public const STATUS_FINISHED = 'FINISHED';
    public const STATUS_CANCELLED = 'CANCELLED';

    // Game general
    public const PLAYER_COUNT = 'player_count';
    public const AVAILABLE_MOVES = 'available_moves'; 
    // Game visibility 
    public const VISIBILITY_PRIVATE = '';

    // Game  DTO keys
    public const DTO_GAME_ID = 'game_id'; 
    public const DTO_GAME_NAME = 'game_name'; 
    public const DTO_GAME_STATUS = 'game_status'; 
    public const DTO_GAME_TURN = 'game_turn'; 
    public const DTO_PLAYERS = 'players'; 
    public const DTO_CURRENT_PLAYER_ID = 'current_player_id'; 
    public const DTO_CURRENT_PLAYER_INDEX = 'current_player_index'; 
    public const DTO_CURRENT_PLAYER_USERNAME = 'current_player_username'; 
    public const DTO_USER_ID = 'user_id'; 
    public const DTO_USERNAME = 'username'; 
    public const DTO_PLAYER_INDEX = 'player_index'; 
    public const DTO_FIGURES = 'figures'; 
    public const DTO_FIGURE_INDEX = 'figure_index'; 
    public const DTO_MOVE = 'move'; 
    public const DTO_FROM = 'from'; 
    public const DTO_TO = 'to'; 
    public const DTO_AREA = 'area'; 
    public const DTO_POSITION = 'position'; 
    public const DTO_ABSOLUTE_TARGET = 'absolute_target'; 
    public const DTO_IS_KICK = 'is_kick'; 
    public const DTO_KICKED_PLAYER_ID = 'kicked_player_id'; 
    public const DTO_KICKED_PLAYER_INDEX = 'kicked_player_index'; 
    public const DTO_KICKED_FIGURE_INDEX = 'kicked_figure_index'; 
    public const DTO_WINNER_USER_ID = 'winner_user_id'; 
    public const DTO_WINNER_PLAYER_INDEX = 'winner_player_index'; 
    public const DTO_IS_GOAL_ENTRY = 'is_goal_entry'; 
    public const DTO_IS_LAP_OVERFLOW = 'is_lap_overflow'; 
    public const DTO_IS_PASS = 'is_pass'; 
    public const DTO_CURRENT_DICE_ROLL = 'current_dice_roll'; 

    // Figure Areas
    public const AREA_HOME  = 'home';
    public const AREA_FIELD = 'field';
    public const AREA_GOAL  = 'goal';

    // HTTP requests
    public const REQUEST_METHOD = 'REQUEST_METHOD';
    public const REQUEST_METHOD_GET = 'GET';
    public const REQUEST_METHOD_POST = 'POST';
    public const REQUEST_METHOD_PUT = 'PUT';
    public const REQUEST_METHOD_DELETE = 'DELETE';
}
