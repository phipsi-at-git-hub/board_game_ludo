<?php
// Core/Http/Request.php
namespace App\Core\Http;

final class Request {
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
        $time_start = microtime(true);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $errno = curl_errno($ch);

        curl_close($ch);

        $time_latency = round((microtime(true) - $time_start) * 1000, 2);

        $result = [
            'url' => $url,
            'reachable' => $response !== false && $http_code === 200,
            'http_code' => $http_code,
            'latency' => $time_latency,
            'body' => $response,
            'error' => $error,
            'errno' => $errno,
        ];

        if ($response === false) {
            return null;
        }
        return $result;
    }
}
