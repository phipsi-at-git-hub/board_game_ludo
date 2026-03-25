<?php
// src/Models/MoveDTO.php
namespace App\Models;

use App\Constants\Application;

final class MoveDTO {
    public static function create(): array {
        return [
            Application::DTO_GAME_TURN => null, 
            Application::DTO_FIGURE_INDEX => null, 
            Application::DTO_FROM => [
                Application::DTO_AREA => null, 
                Application::DTO_POSITION => null 
            ], 
            Application::DTO_TO => [
                Application::DTO_AREA => null, 
                Application::DTO_POSITION => null 
            ], 
            Application::DTO_WINNER_USER_ID => null, 
            Application::DTO_WINNER_PLAYER_INDEX => null, 
            Application::DTO_IS_PASS => false, 
            Application::DTO_ABSOLUTE_TARGET => null,
            Application::DTO_IS_KICK => false,
            Application::DTO_KICKED_PLAYER_ID => null,
            Application::DTO_KICKED_PLAYER_INDEX => null, 
            Application::DTO_KICKED_FIGURE_INDEX => null,
            Application::DTO_IS_GOAL_ENTRY => false,
            Application::DTO_IS_LAP_OVERFLOW => false,
        ];
    }

    public static function createPass(): array {
        $move = self::create();
        $move[Application::DTO_IS_PASS] = true;
        return $move;
    }
}