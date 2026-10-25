<?php
// src/Constants/Dto/GameMoveDtoConstants.php 

namespace App\Constants\Dto;

final class GameMoveDtoConstants
{
    /**
     * Defines move DTO keys.
     */
    public const MOVE = 'move'; 
    public const FROM = 'from';
    public const TO = 'to';
    public const ABSOLUTE_TARGET = 'absolute_target';
    public const IS_KICK = 'is_kick';
    public const KICKED_PLAYER_ID = 'kicked_player_id';
    public const KICKED_PLAYER_INDEX = 'kicked_player_index';
    public const KICKED_FIGURE_INDEX = 'kicked_figure_index';
    public const IS_GOAL_ENTRY = 'is_goal_entry';
    public const IS_LAP_OVERFLOW = 'is_lap_overflow';
    public const IS_PASS = 'is_pass';
}
