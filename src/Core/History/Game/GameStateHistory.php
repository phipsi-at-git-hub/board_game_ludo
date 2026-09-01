<?php
// src/Core/History/Game/GameStateHistory.php

namespace App\Core\History\Game;

use App\Models\Game\GameHistoryModel;

final class GameStateHistory {
    private string $game_id;
    private int $state_index;
    private array $state;
    private string $created_at;

    private function __construct(
        string $game_id,
        int $state_index,
        array $state,
        string $created_at
    ) {
        $this->game_id = $game_id;
        $this->state_index = $state_index;
        $this->state = $state;
        $this->created_at = $created_at;
    }

    /**
     * Create new history entry
     */
    public static function create(string $game_id, array $state): bool {
        $state_index = GameHistoryModel::getNextStateIndex($game_id);
        return GameHistoryModel::create(
            $game_id,
            $state_index,
            $state
        );
    }

    /**
     * Load complete history of game
     */
    public static function findByGameId(string $game_id): array {
        $rows = GameHistoryModel::findByGameId($game_id);
        return array_map(
            fn(array $row) => self::fromArray($row),
            $rows
        );
    }

    /**
     * Load latest state
     */
    public static function findLatest(string $game_id): ?self {
        $row = GameHistoryModel::findLatest($game_id);
        return $row
            ? self::fromArray($row)
            : null;
    }

    /**
     * Load specific state
     */
    public static function findByIndex(string $game_id, int $state_index): ?self {
        $row = GameHistoryModel::findByGameIdAndIndex($game_id, $state_index);
        return $row
            ? self::fromArray($row)
            : null;
    }

    /**
     * Create object from database row
     */
    private static function fromArray(array $data): self {
        return new self(
            $data['game_id'],
            (int)$data['state_index'],
            json_decode(
                $data['state'],
                true,
                512,
                JSON_THROW_ON_ERROR
            ),
            $data['created_at']
        );
    }

    /**
     * Getter - Game ID
     */
    public function getGameId(): string {
        return $this->game_id;
    }

    /**
     * Getter - State index
     */
    public function getStateIndex(): int {
        return $this->state_index;
    }

    /**
     * Getter - State
     */
    public function getState(): array {
        return $this->state;
    }

    /**
     * Getter - Creation timestamp
     */
    public function getCreatedAt(): string {
        return $this->created_at;
    }
}
