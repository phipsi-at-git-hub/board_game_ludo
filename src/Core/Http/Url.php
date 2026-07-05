<?php
// Core/Http/Url.php
namespace App\Core\Http;

final class Url {
    public static function baseUrl(): string {
        $protocol = (
            !empty($_SERVER['HTTP'])
            && $_SERVER['HTTPS'] !== 'off'
        ) ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host;
    }

    public static function url(string $path): string {
        return rtrim(self::baseUrl(), '/') . '/' . ltrim($path, '/');
    }
}
