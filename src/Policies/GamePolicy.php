<?php
// src/Policies/GamePolicy.php

namespace App\Policies;

use App\Models\GameModel;
use App\Models\UserModel;

final class GamePolicy {
    public static function canAccess(GameModel $game, UserModel $user): bool {
        // TODO:
        return true;
    }

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

    public static function canLeave(GameModel $game, UserModel $user): bool {
        return $game->hasPlayer($user->getId());
    }

    public static function canEdit(GameModel $game, UserModel $user): bool {
        return (
            $game->isWaiting()
            && (
                $user->isAdmin()
                || $user->getId() === $game->getCreatedByUserId()
            )
        );
    }

    public static function canDelete(GameModel $game, UserModel $user): bool {
        return (
            $user->isAdmin()
            || (
                $game->isWaiting()
                && $user->getId() === $game->getCreatedByUserId()
            )
        );
    }

    public static function canStart(GameModel $game, UserModel $user): bool {
        return (
            $game->isWaiting()
            && (
                $user->isAdmin()
                || $user->getId() === $game->getCreatedByUserId()
            )
        );
    }

    public static function canPause(GameModel $game, UserModel $user): bool {
        return (
            $game->isRunning()
            && (
                $user->isAdmin()
                || $user->getId() === $game->getCreatedByUserId()
            )
        );
    }

    public static function canReset(GameModel $game, UserModel $user): bool {
        return (
            $user->isAdmin()
            || (
                $game->isWaiting()
                && $user->getId() === $game->getCreatedByUserId()
            )
        );
    }

    public static function canCancel(GameModel $game, UserModel $user): bool {
        return (
            $user->isAdmin()
            || (
                $game->isWaiting()
                && $user->getId() === $game->getCreatedByUserId()
            )
        );
    }
}
