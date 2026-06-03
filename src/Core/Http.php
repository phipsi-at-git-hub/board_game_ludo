<?php
// Core/Http.php
namespace App\Core; 

final class Http {
    public static function baseUrl(): string {
        $protocol = (
            !empty($_SERVER['HTTP']) 
            && $_SERVER['HTTPS'] !== 'off' 
        ) ? 'https' : 'http' ;
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost'; 
        return $protocol . '://' . $host; 
    }

    public static function url(string $path): string {
        return rtrim(self::baseUrl(), '/') . '/' . ltrim($path, '/'); 
    }

    // HTTP - Simple HTTP request for health checks, lightweight API calls
    public static function callUrlSimple(string $url, int $timeout = 3): ?string {
        $context = stream_context_create([
            'http' => [
                'timeout' => $timeout, 
                'ignore_errors' => true
            ]
        ]);
        $response = @file_get_contents($url, false, $context); 
        return $response !== false ? $response : null; 
    }

    // HTTP - Detailed HTTP request (cURL) for health diagnostics, debugging, system monitoring
    public static function callUrlDetailed(string $url, int $timeout = 5): ?array {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, 
            CURLOPT_TIMEOUT => $timeout, 
            CURLOPT_FOLLOWLOCATION => true, 
        ]);
        $response = curl_exec($ch); 
        $result = [
            'body' => $response, 
            'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE), 
            'error' => curl_error($ch), 
            'errno' => curl_errno($ch), 
        ];
        curl_close($ch); 

        if ($response === false) {
            return null;
        }
        return $result; 
    }
}