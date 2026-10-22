<?php
// src/Core/Dto/Game/GameFilterContext.php

namespace App\Core\Dto\Game;

use App\Constants\Application;

/**
 * GameFilterContext
 */
final readonly class GameFilterContext
{
    public array $statuses;
    public array $user_relations;
    public ?string $date_range;
    public int $games_count;
    public array $available_statuses;
    public array $available_user_relations;

    /**
     * __construct
     *
     * @param array $statuses
     * @param array $user_relations
     * @param ?string $date_range
     * @param array $games
     * @return void
     */
    public function __construct(
        array $statuses,
        array $user_relations,
        ?string $date_range,
        array $games,
    ) {
        $this->statuses = $statuses;
        $this->user_relations = $user_relations;
        $this->date_range = $date_range;
        $this->games_count = count($games);

        $this->available_statuses = [
            Application::STATUS_WAITING,
            Application::STATUS_RUNNING,
            Application::STATUS_FINISHED,
            Application::STATUS_CANCELLED,
        ];
        $this->available_user_relations = [
            Application::GAMES_CREATED,
            Application::GAMES_PARTICIPATED,
            Application::GAMES_WON,
        ];
    }

    /**
     * fromFilter
     *
     * @param array $statuses
     * @param array $user_relations
     * @param ?string $date_range
     * @param array $games
     * @return array
     */
    public static function fromFilter(
        array $statuses,
        array $user_relations,
        ?string $date_range,
        array $games,
    ): array {
        $context = new self(
            statuses: $statuses,
            user_relations: $user_relations,
            date_range: $date_range,
            games: $games,
        );

        return $context->jsonSerialize();
    }

    /**
     * jsonSerialize
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'statuses' => $this->statuses,
            'user_relations' => $this->user_relations,
            'date_range' => $this->date_range,
            'available_statuses' => $this->available_statuses,
            'available_user_relations' => $this->available_user_relations,
            'games_count' => $this->games_count,
        ];
    }
}
