<?php
// src/Services/Game/GameService.php

namespace App\Services\Game;

use App\Models\Game\GameModel;
use App\Models\User\UserModel;
use App\Policies\GamePolicy;

/**
 * GameService
 */
final class GameService
{
    /**
     * Join game
     *
     * join
     * 
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public function join(GameModel $game, UserModel $user): bool
    {
        if (!GamePolicy::canJoin($game, $user)) {
            return false;
        }

        return $game->join($user->getId());
    }

    /**
     * Leave game
     * 
     * leave
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public function leave(GameModel $game, UserModel $user): bool
    {
        if (!GamePolicy::canLeave($game, $user)) {
            return false;
        }

        return $game->leave($user->getId());
    }

    /**
     * Start game
     * 
     * start
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public function start(GameModel $game, UserModel $user): bool
    {
        if (!GamePolicy::canStart($game, $user)) {
            return false;
        }

        $game->startGame();
        if ($game->isRunning()) {
            // Update user statistics for all player
            foreach ($game->getAllPlayers() as $player) {
                $user = UserModel::findById($player->getUserId());
                $user->getUserStatistics()->incrementGamesParticipated();
            }
            return true;
        }
        return false;
    }

    // ToDo: Remove, since pausing a game is obsolete
    /**
     * Pause game 
     * 
     * pause
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public function pause(GameModel $game, UserModel $user): bool
    {
        if (!GamePolicy::canPause($game, $user)) {
            return false;
        }

        $game->pauseGame();
        if ($game->isWaiting()) {
            return true;
        }
        return false;
    }

    /**
     * Reset game
     * 
     * reset
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public function reset(GameModel $game, UserModel $user): bool
    {
        if (!GamePolicy::canReset($game, $user)) {
            return false;
        }

        return $game->resetGame();
    }

    /**
     * Cancel game
     * 
     * cancel
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public function cancel(GameModel $game, UserModel $user): bool
    {
        if (!GamePolicy::canCancel($game, $user)) {
            return false;
        }

        $game->cancelGame();
        if ($game->isCancelled()) {
            return true;
        }
        return false;
    }

    /**
     * Delete game
     * 
     * delete
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public function delete(GameModel $game, UserModel $user): bool
    {
        if (!GamePolicy::canDelete($game, $user)) {
            return false;
        }

        return $game->delete();
    }

    /**
     * Helper
     */
    /**
     * gamesArrayForUser
     *
     * @param array $games
     * @param UserModel $user
     * @return ?array
     */
    public static function gamesArrayForUser(array $games, UserModel $user): ?array
    {
        $gamesArrayForUser = [];
        foreach ($games as $game) {
            if (GamePolicy::canAccess($game, $user)) {
                $gamesArrayForUser[] = $game;
            }
        }
        return $gamesArrayForUser;
    }
}
