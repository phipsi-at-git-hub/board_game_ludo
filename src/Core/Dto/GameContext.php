<?php
// src/Core/Dto/GameContext.php

namespace App\Core\Dto;

use App\Models\GameModel;
use App\Models\UserModel;

final class GameContext {
    private static function create(): array {
        return [
            'game_id' => null,
            'game_name' => null,

            'status' => null,
            'ruleset' => null,

            'player_count' => 0,
            'player_max' => 0,

            'is_private' => false,
            'is_locked' => false,

            'is_joined' => false,
            'is_owner' => false,
            'is_admin' => false,

            'permissions' => [
                'join' => false,
                'leave' => false,
                'edit' => false,
                'delete' => false,
                'lock' => false,
                'unlock' => false,
                'start' => false,
                'reset' => false,
            ],
        ];
    }

    public static function fromGame(GameModel $game, UserModel $user): array {
        $dto = self::create();

        $is_owner = ($game->getCreatedByUserId() === $user->getId()); 
        $is_admin = $user->isAdmin(); 
        $is_joined = $game->hasPlayer($user->getId()); 

        $dto['game_id'] = $game->getId();
        $dto['game_name'] = $game->getName();

        $dto['status'] = $game->getStatus();

        $dto['ruleset'] = $game->getRuleSetModel()->getPreset();

        $dto['player_count'] = $game->getPlayerCount();
        $dto['player_max'] = $game->getPlayerMax();

        $dto['is_private'] = $game->isPrivate();
        $dto['is_locked'] = $game->isLocked();

        $dto['is_joined'] = $is_joined;
        $dto['is_owner'] = $is_owner;
        $dto['is_admin'] = $is_admin;

        $dto['permissions'] = [
            'join' => (
                $game->isWaiting()
                && !$game->isLocked()
                && !$game->isPrivate()
                && !$is_joined
            ),

            'leave' => $is_joined,

            'edit' => (
                ($is_owner || $is_admin)
                && $game->isWaiting()
            ),

            'delete' => (
                ($is_owner && $game->isWaiting())
                || $is_admin
            ),

            'lock' => (
                ($is_owner || $is_admin)
                && !$game->isLocked()
            ),

            'unlock' => (
                ($is_owner || $is_admin)
                && $game->isLocked()
            ),

            'start' => (
                ($is_owner || $is_admin)
                && $game->isWaiting()
            ),

            'reset' => (
                $is_owner || $is_admin
            ),
        ];
        return $dto;
    }
}