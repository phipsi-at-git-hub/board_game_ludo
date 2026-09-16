<?php
// src/Core/Dto/Game/GameFilterContext.php

namespace App\Core\Dto\Game;

use App\Constants\Application;

final class GameFilterContext
{
    private static function create(): array
    {
        return [
            // Filter
            'statuses' => [],
            'user_relations' => [],
            'date_range' => null,

            // Available filter options
            'available_statuses' => [
                Application::STATUS_WAITING, 
                Application::STATUS_RUNNING, 
                Application::STATUS_FINISHED, 
                Application::STATUS_CANCELLED, 
            ],
            'available_user_relations' => [
                Application::GAMES_CREATED, 
                Application::GAMES_PARTICIPATED, 
                Application::GAMES_WON, 
            ],

            // Filtered games
            'games_count' => 0,
        ];
    }

    public static function fromFilter(
        array $statuses,
        array $user_relations,
        string $date_range,
        array $games,
    ): array {
        $dto = self::create();

        // Filter
        $dto['statuses'] = $statuses;
        $dto['user_relations'] = $user_relations;
        $dto['date_range'] = $date_range;

        // Filtered games
        $dto['games_count'] = count($games);

        return $dto;
    }
}
