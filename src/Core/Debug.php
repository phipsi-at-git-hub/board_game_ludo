<?php
// Core/Debug.php
namespace App\Core;

class Debug {
    private static float $start_time;
    private static array $data = [];

    public static function start(): void {
        self::$start_time = microtime(true);
    }

    // Set key value pairs
    public static function set(string $key, mixed $value): void {
        self::$data[$key] = $value;
    }

    // Set route debug info
    public static function setRoute(string $method, string $uri, string $action): void {
        self::set('route_info', "{$method} {$uri} → {$action}");
    }

    // Get all debug info
    public static function all(): array {
        return self::$data;
    }

    // Render a toolbar
    public static function render(): void {
        if (!Env::isDev()) {
            return;
        }

        $route_info = htmlspecialchars(self::$data['route_info'] ?? 'n\a');
        $duration = round((microtime(true) - self::$start_time) * 1000, 2);
        $memory = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

        $files = count(get_included_files());

        echo <<<HTML
        <style>
            #debug-bar {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: #111;
                color: #0f0;
                font: 12px monospace;
                padding: 6px 10px;
                z-index: 9999;
            }
            #debug-bar span {
                margin-right: 15px;
            }
        </style>

        <div id="debug-bar">
            <span><strong>ENV:</strong> dev</span>
            <span><strong>Route:</strong> {$route_info}</span>
            <span><strong>Time:</strong> {$duration} ms</span>
            <span><strong>Memory:</strong> {$memory} MB</span>
            <span><strong>Includes:</strong> {$files}</span>
        </div>
        HTML;
    }
}