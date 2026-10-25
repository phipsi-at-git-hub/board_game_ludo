<?php
// src/Constants/ApplicationConstants.php

namespace App\Constants;

/**
 * ApplicationConstants
 * 
 * Application-wid constants.
 * 
 * Contains generic application identifiers and values that are not
 * specific to database columns, DTOs, HTTP, or a particular domain. 
 */
class ApplicationConstants
{
    /**
     * Defines generic identifiers.
     */
    public const ID = 'id'; 
    public const NAME = 'name'; 
    public const STATUS = 'status'; 
    public const CREATED_AT = 'created_at'; 
    public const UPDATED_AT = 'updated_at'; 

    /**
     * Defines generic identifiers for relations. 
     */
    public const GAME_ID = 'game_id'; 
    public const USER_ID = 'user_id'; 

    /**
     * Defines application actions. 
     */
    public const ACTION_ACCESS = 'access'; 
    public const ACTION_JOIN = 'join'; 
    public const ACTION_EDIT = 'edit'; 
    public const ACTION_DELETE = 'delete'; 

    /**
     * Defines generic values. 
     */
    public const VALUE_ON = 'on'; 
    public const VALUE_OFF = 'off'; 
    public const VALUE_PARTIAL = 'partial'; 

    /**
     * Defines generic results. 
     */
    public const RESULT_OK = 'ok'; 
    public const RESULT_HEALTHY = 'healthy';
    public const RESULT_CRITICAL = 'critical';
    public const RESULT_WARNING = 'warning';
    public const RESULT_FAIL = 'fail'; 

    /**
     * Defines game-related application values. 
     */
    public const PLAYER_COUNT = 'player_count'; 
    public const AVAILABLE_MOVES = 'available_moves'; 

    /**
     * Defines general health-related application value. 
     */

    public const GENERAL_HEALTH = 'health'; 
}
