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
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->getId();
    }

    public static function logout(): void {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                time() - 42000, 
                $params["path"], 
                $params["domain"], 
                $params["secure"], 
                $params["httponly"], 
            );
        }
        session_destroy();
    }
}
