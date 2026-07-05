<?php
// src/Core/Http/Response.php
namespace App\Core\Http;

use App\Constants\Application;

final class Response {
    /* PRIVATE CORE BUILDER (Single Source of Truth) */
    private static function build(
        bool $success,
        string $type,
        ?string $message = null,
        array $data = [],
        array $errors = []
    ): array {
        return [
            'success' => $success,
            'type' => $type,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
        ];
    }

    /* SUCCESS */
    public static function success(array $data = [], ?string $message = null): array {
        return self::build(
            true,
            Application::RESPONSE_TYPE_SUCCESS,
            $message,
            $data
        );
    }

    /* ERROR */
    public static function error(?string $message = null, array $errors = []): array {
        return self::build(
            false,
            Application::RESPONSE_TYPE_ERROR,
            $message,
            [],
            $errors
        );
    }

    /* WARNING */
    public static function warning(?string $message = null, array $data = []): array {
        return self::build(
            true,
            Application::RESPONSE_TYPE_WARNING,
            $message,
            $data
        );
    }

    /* INFO */
    public static function info(?string $message = null, array $data = []): array {
        return self::build(
            true,
            Application::RESPONSE_TYPE_INFO,
            $message,
            $data
        );
    }
}