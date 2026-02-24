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

    // Database field names - game_rule_set
    public const ALLOW_BOTS = 'allow_bots';
    public const EXTRA_ROLL_LIMIT = 'extra_roll_limit';
    public const ALLOW_STACK_OWN_FIGURES = 'allow_stack_own_figures';
    public const STRICT_GOAL_ORDER = 'strict_goal_order';
    public const START_FIELD_MUST_BE_CLEARED = 'start_field_must_be_cleared';

    // Database field names - game_state
    public const CURRENT_PLAYER_INDEX = 'current_player_index';

    // database field names - game_state_figures
    public const FIGURE_INDEX = 'figure_index';
    public const POSITION = 'position';
    public const AREA = 'area';

    // Database field names - users
    public const USERNAME = 'username';
    public const FIRSTNAME = 'firstname';
    public const LASTNAME = 'lastname';
    public const EMAIL = 'email';
    public const ROLE = 'role';
    public const PASSWORD_HASH = 'password_hash';
    public const RESET_TOKEN = 'reset_token';
    public const RESET_TOKEN_EXPIRES_AT = 'reset_token_expires_at';

    // User roles
    public const USER = 'user';
    public const ADMIN = 'admin';
    public const MODERATOR = 'moderator';
    public const GAME_MASTER = 'game_master';

    // Game status
    public const STATUS_WAITING  = 'WAITING';
    public const STATUS_RUNNING  = 'RUNNING';
    public const STATUS_FINISHED = 'FINISHED';
    public const STATUS_CANCELLED = 'CANCELLED';

    // Game general
    public const PLAYER_COUNT = 'player_count';
    // Game visibility 
    public const VISIBILITY_PRIVATE = '';

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
