<?php
// Env.php
namespace App\Core;

final class Env {
    private static ?string $env = null;

    // Get current app environment
    public static function get(): string {
        if (self::$env === null) {
            self::$env = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?? 'prod';
        }
        return self::$env;
    }

    public static function isDev(): bool {
        return self::get() === 'dev';
    }

    public static function isProd(): bool {
        return self::get() === 'prod';
    }

    public static function is(string $env): bool {
        return self::get() === $env;
    }
}