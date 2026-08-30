<?php
// src/Services/GameService.php

namespace App\Services;

use App\Models\GameModel;
use App\Models\UserModel;
use App\Policies\GamePolicy;

final class GameService {
    // Join game
    public function join(GameModel $game, UserModel $user): bool {
        if (!GamePolicy::canJoin($game, $user)) {
            return false;
        }

        return $game->join($user->getId());
    }

    // Leave game
    public function leave(GameModel $game, UserModel $user): bool {
        if (!GamePolicy::canLeave($game, $user)) {
            return false;
        }

        return $game->leave($user->getId());
    }

    // Start game
    public function start(GameModel $game, UserModel $user): bool {
        if (!GamePolicy::canStart($game, $user)) {
            return false;
        }

        $game->startGame();
        if($game->isRunning()) {
            return true; 
        } 
        return false; 
    }

    // Pause game 
    // ToDo: Remove, since pausing a game is obsolete
    public function pause(GameModel $game, UserModel $user): bool {
        if (!GamePolicy::canPause($game, $user)) {
            return false;
        }

        $game->pauseGame();
        if ($game->isWaiting()) {
            return true; 
        } 
        return false; 
    }

    // Reset game
    public function reset(GameModel $game, UserModel $user): bool {
        if (!GamePolicy::canReset($game, $user)) {
            return false;
        }

        return $game->resetGame();
    }

    // Cancel game
    public function cancel(GameModel $game, UserModel $user): bool {
        if (!GamePolicy::canCancel($game, $user)) {
            return false;
        }

        $game->cancelGame(); 
        if ($game->isCancelled()) {
            return true; 
        } 
        return false; 
    }

    // Delete game
    public function delete(GameModel $game, UserModel $user): bool {
        if (!GamePolicy::canDelete($game, $user)) {
            return false;
        }

        return $game->delete();
    }
}
