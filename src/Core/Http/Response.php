<?php
// src/Core/Http/Response.php
namespace App\Core\Http;

use App\Constants\Application;

final class Response {
    /* Core Builder */
    private static function build(
        bool $success,
        string $type,
        ?string $message = null,
        array $data = [],
        array $errors = [], 
        array $views = [] 
    ): array {
        return [
            'success' => $success,
            'type' => $type,
            'message' => $message,
            'data' => $data,
            'errors' => $errors, 
            'views' => $views, 
        ];
    }

    /* Success */
    public static function success(array $data = [], ?string $message = null, array $views = []): array {
        return self::build(
            true,
            Application::RESPONSE_TYPE_SUCCESS,
            $message,
            $data, 
            [], // no errors 
            $views 
        );
    }

    /* Error */
    public static function error(?string $message = null, array $errors = []): array {
        return self::build(
            false,
            Application::RESPONSE_TYPE_ERROR,
            $message,
            [], // no data
            $errors
        );
    }

    /* Warning */
    public static function warning(?string $message = null, array $data = []): array {
        return self::build(
            true,
            Application::RESPONSE_TYPE_WARNING,
            $message,
            $data
        );
    }

    /* Info */
    public static function info(?string $message = null, array $data = []): array {
        return self::build(
            true,
            Application::RESPONSE_TYPE_INFO,
            $message,
            $data
        );
    }
}