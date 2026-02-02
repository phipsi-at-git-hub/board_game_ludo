<?php
// Middleware.php
namespace App\Core;

use App\Core\Auth;
use App\Core\Csrf;

class Middleware {
    public static function auth(): void {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }
    }

    public static function guest(): void {
        if (Auth::check()) {
            header('Location: /');
            exit;
        }
    }

    public static function csrf(array $request): void{
        $method = $_SERVER['REQUEST_METHOD'];
        // POST, PUT, DELETE prüfen
        if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
            $token = $request['_csrf_token'] ?? '';
            if (!Csrf::validate($token)) {
                http_response_code(403);
                die('Invalid CSRF token');
            }
        }
    }
    /*
    public static function csrf(array $post_data): void {
        if (!Csrf::validate($post_data['_csrf_token'] ?? null)) {
            http_response_code(403);
            die('Invalid CSRF token');
        }
    }
    */
}