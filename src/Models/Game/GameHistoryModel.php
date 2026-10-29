<?php
// src/Models/Game/GameHistoryModel.php

namespace App\Models\Game;

use App\Constants\Database\DbColumnConstants;
use App\Constants\Database\DbTableConstants;
use App\Models\BaseModel;

final class GameHistoryModel extends BaseModel
{
    /**
     * Create game history entry
     */
    public static function create(string $game_id, int $state_index, array $state): bool
    {
        return static::execute(
            sprintf(
                "
                INSERT INTO %s
                (
                    %s,
                    %s,
                    %s,
                    %s,
                    %s
                )
                VALUES
                (
                    :id,
                    :game_id,
                    :state_index,
                    :state,
                    NOW()
                )
                ",
                DbTableConstants::GAME_HISTORY,
                DbColumnConstants::ID,
                DbColumnConstants::GAME_ID,
                DbColumnConstants::STATE_INDEX,
                DbColumnConstants::STATE,
                DbColumnConstants::CREATED_AT
            ),
            [
                DbColumnConstants::ID => self::generateUUID(),
                DbColumnConstants::GAME_ID => $game_id,
                DbColumnConstants::STATE_INDEX => $state_index,
                DbColumnConstants::STATE => json_encode(
                    $state,
                    JSON_THROW_ON_ERROR
                )
            ]
        );
    }

    /**
     * Get complete game history
     */
    public static function findByGameId(string $game_id): array
    {
        return static::fetchAll(
            sprintf(
                "
                SELECT *
                FROM %s
                WHERE %s = :game_id
                ORDER BY %s ASC
                ",
                DbTableConstants::GAME_HISTORY,
                DbColumnConstants::GAME_ID,
                DbColumnConstants::STATE_INDEX
            ),
            [
                DbColumnConstants::GAME_ID => $game_id
            ]
        );
    }

    /**
     * Get latest game history entry
     */
    public static function findLatest(string $game_id): ?array
    {
        return static::fetchOne(
            sprintf(
                "
                SELECT *
                FROM %s
                WHERE %s = :game_id
                ORDER BY %s DESC
                LIMIT 1
                ",
                DbTableConstants::GAME_HISTORY,
                DbColumnConstants::GAME_ID,
                DbColumnConstants::STATE_INDEX
            ),
            [
                DbColumnConstants::GAME_ID => $game_id
            ]
        );
    }

    /**
     * Get next state index for game
     */
    public static function getNextStateIndex(string $game_id): int
    {
        $row = static::fetchOne(
            sprintf(
                "
                SELECT MAX(%s) AS max_index
                FROM %s
                WHERE %s = :game_id
                ",
                DbColumnConstants::STATE_INDEX,
                DbTableConstants::GAME_HISTORY,
                DbColumnConstants::GAME_ID
            ),
            [
                DbColumnConstants::GAME_ID => $game_id
            ]
        );
        return ($row['max_index'] ?? -1) + 1;
    }

    /**
     * Get single state by index
     */
    public static function findByGameIdAndIndex(string $game_id, int $state_index): ?array
    {
        return static::fetchOne(
            sprintf(
                "
                SELECT *
                FROM %s
                WHERE 
                    %s = :game_id
                    AND %s = :state_index
                LIMIT 1
                ",
                DbTableConstants::GAME_HISTORY,
                DbColumnConstants::GAME_ID,
                DbColumnConstants::STATE_INDEX
            ),
            [
                DbColumnConstants::GAME_ID => $game_id,
                DbColumnConstants::STATE_INDEX => $state_index
            ]
        );
    }

    /**
     * Reset game history - delete all entries of game
     */
    public static function resetByGameId(string $game_id): bool
    {
        return static::execute(
            "DELETE FROM game_history WHERE game_id = :game_id",
            ['game_id' => $game_id]
        );
    }
}
