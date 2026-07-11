<?php
// src/Policies/GamePolicy.php

namespace App\Policies;

use App\Core\SystemSettings;
use App\Models\GameModel;
use App\Models\UserModel;

final class GamePolicy {
    // Is user owner of the game
    public static function isOwner(GameModel $game, UserModel $user): bool {
        return $game->getCreatedByUserId() === $user->getId(); 
    }

    // Can user access the game
    public static function canAccess(GameModel $game, UserModel $user): bool {
        // TODO:
        return true;
    }

    // Can user join the game
    public static function canJoin(GameModel $game, UserModel $user): bool {
        if (!$game->isWaiting()) {
            return false;
        }

        if ($game->isPrivate()) {
            return false;
        }

        if ($game->isLocked()) {
            return false;
        }

        if ($game->isFull()) {
            return false;
        }

        if ($game->hasPlayer($user->getId())) {
            return false;
        }
        return true;
    }

    // Can user leave the game
    public static function canLeave(GameModel $game, UserModel $user): bool {
        if (!$game->hasPlayer($user->getId())) {
            return false; 
        } 
        if ($game->isRunning()) {
            return false; 
        }
        return true; 
    }

    // Can user edit the game
    public static function canEdit(GameModel $game, UserModel $user): bool {
        return (
            $game->isWaiting()
            && (
                $user->isAdmin()
                || $user->getId() === $game->getCreatedByUserId()
            )
        );
    }

    // Can user delete the game
    public static function canDelete(GameModel $game, UserModel $user): bool {
        return (
            $user->isAdmin()
            && (
                $game->isFinished()
                || $game->isCancelled() 
            )
        );
    }

    // can user start the game
    public static function canStart(GameModel $game, UserModel $user): bool {
        return (
            $game->isWaiting()
            && (
                $user->isAdmin()
                || $user->getId() === $game->getCreatedByUserId()
            )
        );
    }

    // Can user play the game 
    public static function canPlay(GameModel $game, UserModel $user): bool {
        return (
            $game->isRunning() 
            && (
                (
                    SystemSettings::isGamePlayEnabled() 
                    && $user->getId() === $game->getCreatedByUserId()
                ) 
                || $user->isAdmin()  
            )
        );
        return (
            $game->isRunning()
            && (
                $user->isAdmin()
                || $user->getId() === $game->getCreatedByUserId()
            )
        );
    }

    // Can user pause the game 
    public static function canPause(GameModel $game, UserModel $user): bool {
        return (
            $game->isRunning()
            && (
                $user->isAdmin()
                || $user->getId() === $game->getCreatedByUserId()
            )
        );
    }

    // Can user reset the game 
    public static function canReset(GameModel $game, UserModel $user): bool {
        return (
            $user->isAdmin()
            || (
                $game->isWaiting()
                && $user->getId() === $game->getCreatedByUserId()
            )
        );
    }

    // Can user cancel the game 
    public static function canCancel(GameModel $game, UserModel $user): bool {
        return (
            (
                $user->isAdmin()
                && (!$game->isCancelled() && !$game->isFinished()) 
            )
            || (
                ($game->isWaiting() || $game->isRunning()) 
                && self::isOwner($game, $user)
            )
        );
    }
}
