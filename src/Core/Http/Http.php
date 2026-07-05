<?php
// Core/Http/Http.php
namespace App\Core\Http; 

final class Http { 
    public static function baseUrl(): string {
        return Url::baseUrl();
    }

    public static function url(string $path): string {
        return Url::url($path);
    }

    public static function callUrlSimple(string $url, int $timeout = 3): ?string {
        return Request::callUrlSimple($url, $timeout);
    }

    public static function callUrlDetailed(string $url, int $timeout = 5): ?array {
        return Request::callUrlDetailed($url, $timeout);
    }
}