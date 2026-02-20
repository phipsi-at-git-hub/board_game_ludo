<?php
// src/Policies/GamePolicy.php
namespace App\Policies;

use App\Constants\Application;
use App\Models\GameModel;
use App\Models\UserModel;

class GamePolicy {
    public static function canAccess(UserModel $user, GameModel $game): bool {
        // ToDo: implement
        return true;
    }

    public static function canEdit(UserModel $user, GameModel $game): bool {
        return $user->getId() === $game->getCreatedByUserId();
    }

    public static function canDelete(UserModel $user, GameModel $game): bool {
        return $user->getId() === $game->getCreatedByUserId();
    }

    public static function canJoin(UserModel $user, GameModel $game): bool {
        // Game is closed
        if ($game->isLocked()) {
            return false;
        }

        // Game is already full
        if ($game->isFull()) {
            return false;
        }

        // User is already player of the game
        if ($game->isParticipant($user)) {
            return false;
        }

        // If game is private check for invites for given user

        return true;
    }

    public static function permissions(?UserModel $user, GameModel $game): array {
        if (!$user) {
            return [
                Application::GENERAL_ACCESS => false, 
                Application::GENERAL_JOIN => false, 
                Application::GENERAL_EDIT => false, 
                Application::GENERAL_DELETE => false, 
            ];
        }

        return [
            Application::GENERAL_ACCESS => self::canAccess($user, $game), 
            Application::GENERAL_JOIN => self::canJoin($user, $game), 
            Application::GENERAL_EDIT => self::canEdit($user, $game), 
            Application::GENERAL_DELETE => self::canDelete($user, $game), 
        ];
    }
}