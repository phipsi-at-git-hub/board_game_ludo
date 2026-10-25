<?php
// src/Constants/Database/DbColumnConstants.php

namespace App\Constants\Database;

final class DbColumnConstants
{
    /**
     * Defines common database column names.
     */
    public const ID = 'id';
    public const NAME = 'name';
    public const GAME_NAME = 'name';
    public const GAME_ID = 'game_id';
    public const USER_ID = 'user_id';
    public const STATUS = 'status';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    /**
     * Defines game database columns.
     */
    public const CREATED_BY_USER_ID = 'created_by_user_id';
    public const IS_PRIVATE = 'is_private';
    public const IS_LOCKED = 'is_locked';
    public const IS_TEST_GAME = 'is_test_game';

    /**
     * Defines game rule set database columns.
     */
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

    /**
     * Defines game state database columns.
     */
    public const CURRENT_PLAYER_INDEX = 'current_player_index';
    public const CURRENT_DICE_ROLL = 'current_dice_roll';
    public const CURRENT_TURN_COUNTER = 'current_turn_counter';
    public const LEAVE_HOME_ATTEMPTS_USED = 'leave_home_attempts_used';
    public const EXTRA_ROLLS_ON_SIX_USED = 'extra_rolls_on_six_used';
    public const WINNER_USER_ID = 'winner_user_id';

    /**
     * Defines game figure database columns.
     */
    public const FIGURE_INDEX = 'figure_index';
    public const POSITION = 'position';
    public const AREA = 'area';

    /**
     * Defines game state player database columns.
     */
    public const PLAYER_INDEX = 'player_index';

    /**
     * Defines game history database columns.
     */
    public const STATE_INDEX = 'state_index';
    public const STATE = 'state';

    /**
     * Defines user database columns.
     */
    public const USERNAME = 'username';
    public const FIRSTNAME = 'firstname';
    public const LASTNAME = 'lastname';
    public const EMAIL = 'email';
    public const ROLE = 'role';
    public const PREFERRED_LANGUAGE = 'preferred_language';
    public const PREFERRED_CAMERA_MODE = 'preferred_camera_mode';
    public const PASSWORD_HASH = 'password_hash';
    public const RESET_TOKEN = 'reset_token';
    public const RESET_TOKEN_EXPIRES_AT = 'reset_token_expires_at';
    public const LAST_LOGIN = 'last_login';

    /**
     * Defines user statistics database columns.
     */
    public const LOGINS = 'logins';
    public const GAMES_CREATED = 'games_created';
    public const GAMES_PARTICIPATED = 'games_participated';
    public const GAMES_WON = 'games_won';

    /**
     * Defines system settings database columns.
     */
    public const REGISTRATION_ENABLED = 'registration_enabled';
    public const LOGIN_ENABLED = 'login_enabled';
    public const SYSTEM_ENABLED = 'system_enabled';
    public const LOGGING_DEBUG_ENABLED = 'logging_debug_enabled';
    public const GAME_CREATION_ENABLED = 'game_creation_enabled';
    public const GAME_PLAY_ENABLED = 'game_play_enabled';
    public const MAINTENANCE_MODE_ENABLED = 'maintenance_mode_enabled';
    public const MAINTENANCE_MESSAGE = 'maintenance_message';
    public const SYSTEM_NOTICE_ENABLED = 'system_notice_enabled';
    public const SYSTEM_NOTICE_MESSAGE = 'system_notice_message';
    public const UPDATED_BY = 'updated_by';
}
