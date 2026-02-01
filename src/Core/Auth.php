<?php
// Auth.php
namespace App\Core;

use App\Models\UserModel;

class Auth {
    public static function user(): ?UserModel {
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool {
        return isset($_SESSION['user']);
    }

    public static function login(UserModel $user): void {
        $_SESSION['user'] = $user;
    }

    public static function logout(): void {
        session_destroy();
    }
}
