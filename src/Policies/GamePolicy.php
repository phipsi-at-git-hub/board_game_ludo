<?php
// src/Policies/GamePolicy.php

namespace App\Policies;

use App\Core\Application\App;
use App\Models\Game\GameModel;
use App\Models\User\UserModel;
use App\Services\SystemService;

/**
 * GamePolicy
 */
final class GamePolicy 
{ 
    /**
     * Is user owner of the game
     * isOwner
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public static function isOwner(GameModel $game, UserModel $user): bool 
    {
        return $game->getCreatedByUserId() === $user->getId(); 
    }

    /**
     * Can user access the game
     * canAccess
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public static function canAccess(GameModel $game, UserModel $user): bool 
    {
        if ($game->isPrivate() && !$game->isParticipant($user)) {
            return false; 
        }
        return true;
    }

    /**
     * Can user join the game
     * 
     * canJoin
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public static function canJoin(GameModel $game, UserModel $user): bool 
    {
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

    /**
     * Can user leave the game
     * canLeave
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public static function canLeave(GameModel $game, UserModel $user): bool 
    {
        if (!$game->hasPlayer($user->getId())) {
            return false; 
        } 
        if ($game->isRunning()) {
            return false; 
        }
        return true; 
    }

    /**
     * Can user edit the game
     * 
     * canEdit
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public static function canEdit(GameModel $game, UserModel $user): bool 
    {
        return (
            $game->isWaiting()
            && (
                $user->isAdmin()
                || $user->getId() === $game->getCreatedByUserId()
            )
        );
    }

    /**
     * Can user delete the game
     * 
     * canDelete
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public static function canDelete(GameModel $game, UserModel $user): bool 
    {
        return (
            $user->isAdmin()
            && (
                $game->isFinished()
                || $game->isCancelled() 
            )
        );
    }

    /**
     * Can user start the game
     * canStart
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public static function canStart(GameModel $game, UserModel $user): bool 
    {
        return (
            $game->isWaiting()
            && (
                $user->isAdmin()
                || $user->getId() === $game->getCreatedByUserId()
            )
            && $game->hasParticipants()
        );
    }

    /**
     * Can user play the game
     * 
     * canPlay
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public static function canPlay(GameModel $game, UserModel $user): bool 
    {
        $systemService = App::instance()->resolve(SystemService::class); 
        return (
            $game->isRunning() 
            && (
                (
                    $systemService->isGamePlayEnabled() 
                    && $user->getId() === $game->getCreatedByUserId()
                ) 
                || $user->isAdmin()  
            )
        );
    }

    // ToDo: Remove pausing a game is obsolete
    /**
     * Can user pause the game
     * 
     * canPause
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public static function canPause(GameModel $game, UserModel $user): bool 
    {
        return (
            $game->isRunning()
            && (
                $user->isAdmin()
                || $user->getId() === $game->getCreatedByUserId()
            )
        );
    }

    /**
     * Can user reset the game
     * canReset
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public static function canReset(GameModel $game, UserModel $user): bool 
    {
        return (
            $game->isRunning()
            && (
                $user->isAdmin()
                || $user->getId() === $game->getCreatedByUserId()
            )
        );
    }

    /**
     * Can user cancel the game 
     * 
     * canCancel
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public static function canCancel(GameModel $game, UserModel $user): bool 
    {
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

    /**
     * Can user create a test game 
     * 
     * canCreateTestGame
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return bool
     */
    public static function canCreateTestGame(GameModel $game, UserModel $user): bool 
    {
        return (
            $user->isAdmin() 
            && !$game->isTestGame() 
        );
    }
}
