<?php
// Auth.php
namespace App\Core;

use App\Models\UserModel;

class Auth {
    public static function user(): ?UserModel {
        if (!self::check()) {
            return null;
        }

        return UserModel::findById($_SESSION['user_id']);
    }

    public static function check(): bool {
        return isset($_SESSION['user_id']);
    }

    public static function login(UserModel $user): void {
        $_SESSION['user_id'] = $user->getId();
    }

    public static function logout(): void {
        session_destroy();
    }
}
