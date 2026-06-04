<?php 
// Core/SystemHealth.php
namespace App\Core; 

use App\Constants\Application;
use AppendIterator;
use Throwable;

final class SystemHealth {
    // DB latency thresholds
    private const DB_LATENCY_OK = 10;
    private const DB_LATENCY_WARNING = 30; 

    // Environment disk space thresholds in percent 
    private const ENV_DISK_SPACE_OK = 70; 
    private const ENV_DISK_SPACE_WARNING = 90; 

    // Game API latency thresholds
    private const GAME_API_LATENCY_OK = 80; 
    private const GAME_API_LATENCY_WARNING = 200; 

    // Overall System Health
    public static function isHealthy(): bool {
        return 
            self::checkDatabase() 
            && self::checkGame() 
            && self::checkEnvironment(); 
    }

    // Status for admin dashboard
    public static function getStatus(): string {
        if (!self::checkDatabase() || !self::checkEnvironment()) {
            return Application::GENERAL_CRITICAL; 
        }
        if (!self::isGameHealthy()) {
            return Application::GENERAL_WARNING;
        }
        return Application::GENERAL_HEALTHY; 
    }

    // Health - Database
    public static function getDatabaseDetails(): array {
        $db = Database::getInstance(); 
        $time_start = microtime(true); 

        try {
            $result = $db->fetch('SELECT 1 AS ok'); 
            $time_latency = round((microtime(true) - $time_start) * 1000, 2); 
            $connection_info = $db->query("SHOW STATUS LIKE 'Threads_connected'"); 
            $version_info = $db->fetch("SELECT VERSION() AS version, @@version_comment AS comment");
            $size = $db->fetch("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb FROM information_schema.tables WHERE table_schema = DATABASE()");

            return [
                'status' => Application::GENERAL_OK, 
                'reachable' => true, 
                'latency_ms' => $time_latency, 
                'latency_state' => self::getDatabaseLatencyState($time_latency), 
                'threads_connected' => $connection_info[0]['Value'] ?? null, 
                'connections_ok' => (isset($connection_info[0]['Value']) && $connection_info[0]['Value'] > 0) ? true : false,  
                'db_name' => $_ENV['DB_NAME'] ?? null, 
                'db_host' => $_ENV['DB_HOST'] ?? null, 
                'db_system' => 'MySQL', 
                'db_version' => $version_info['version'] ?? null, 
                'db_comment' => $version_info['comment'] ?? null, 
                'db_size' => $size['size_mb'] . ' MB', 
            ]; 
        } catch (Throwable $e) {
            return [
                'status' => Application::GENERAL_FAIL, 
                'reachable' => false, 
                'latency_ms' => null, 
                'latency_state' => null, 
                'threads_connected' => null, 
                'connections_ok' => false,  
                'db_name' => $_ENV['DB_NAME'] ?? null, 
                'db_host' => $_ENV['DB_HOST'] ?? null, 
                'db_system' => null, 
                'db_version' => null, 
                'db_comment' => null, 
                'error' => $e->getMessage(), 
            ]; 
        }
    }

    // Health - Environment
    public static function getEnvironmentDetails(): array {
        $disk_free_space = disk_free_space('/'); 
        $disk_total_space = disk_total_space('/'); 
        $disk_space_used_percentage = self::getPercentage($disk_free_space, $disk_total_space); 
        return [
            'status' => (self::checkEnvironment()) ? Application::GENERAL_OK : Application::GENERAL_FAIL, 
            'app_env' => Env::get(), 
            'is_dev' => Env::isDev(), 
            'is_prod' => Env::isProd(), 
            'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN), 
            'app_name' => $_ENV['APP_NAME'] ?? null, 
            'php_version' => PHP_VERSION, 
            'memory_limit' => ini_get('memory_limit'), 
            'timezone' => date_default_timezone_get(), 
            'disk_free_space' => self::formatBytes($disk_free_space), 
            'disk_total_space' => self::formatBytes($disk_total_space), 
            'disk_free_2_total_space' => $disk_space_used_percentage . '%', 
            'disk_free_2_total_space_state' => self::getEnvironmentDiskSpaceState($disk_space_used_percentage), 
        ]; 
    }

    // Health - Game
    public static function getGameDetails(): array {
        $game_health = GameHealth::getApiHealthDetails();
        $game_health['latency_state'] = Application::GENERAL_FAIL; 
        if (isset($game_health['status_ok']) && $game_health['status_ok'] === true && isset($game_health['latency'])) {
            $game_health['latency_state'] = self::getGameApiLatencyState($game_health['latency']); 
        }
        return $game_health; 
    }

    /**
     * Helper
     */
    // Helper - Format bytes
    private static function formatBytes(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB']; 
        $i = 0; 

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024; 
            $i++; 
        }
        return round($bytes, 2) . ' ' . $units[$i]; 
    }

    // Helper - 
    private static function getPercentage(float|int $value, float|int $total): float|null {
        return round(100 - (($value / $total) * 100), 2); 
    }
    
    // Helper - Get Latency state by given latency
    private static function getLatencyState(float|int|null $ms, int $ok, int $warning): string {
        if ($ms === null) {
            return Application::GENERAL_FAIL;
        }
        if ($ms <= $ok) {
            return Application::GENERAL_OK; 
        }
        if ($ms <= $warning) {
            return Application::GENERAL_WARNING; 
        }
        return Application::GENERAL_FAIL; 
    }

    // Helper - Get latency state for DB
    public static function getDatabaseLatencyState(float|int|null $ms): string {
        return self::getLatencyState($ms, self::DB_LATENCY_OK, self::DB_LATENCY_WARNING); 
    }

    // Helper - Get latency state for API
    public static function getGameApiLatencyState(float|int|null $ms): string {
        return self::getLatencyState($ms, self::GAME_API_LATENCY_OK, self::GAME_API_LATENCY_WARNING); 
    }

    // Helper - Get percentage state for environment disk space
    public static function getEnvironmentDiskSpaceState(float|int|null $percentage): string {
        return self::getLatencyState($percentage, self::ENV_DISK_SPACE_OK, self::ENV_DISK_SPACE_WARNING); 
    }

    // Helper - Is database healthy
    public static function checkDatabase(): bool {
        try {
            $result = Database::getInstance()->fetch('SELECT 1 AS health'); 
            return isset($result[Application::GENERAL_HEALTH]) && (int)$result[Application::GENERAL_HEALTH] === 1; 
        } catch (Throwable $e) {
            return false; 
        }
    }

    // Helper - Are System Settings okay
    public static function checkSystemSettings(): bool {
        try {
            $system_settings = SystemSettings::get(); 
            return method_exists($system_settings, 'isValid') ? $system_settings->isValid() : true; 
        } catch (Throwable $e) {
            return false; 
        }
    }

    // Helper - Environment check
    public static function checkEnvironment(): bool {
        try {
            $env = Env::get(); 
            return in_array($env, [Application::GENERAL_DEV, Application::GENERAL_PROD], true); 
        } catch (Throwable $e) {
            return false; 
        }
    }

    // Helper - Game Check
    public static function checkGame(): bool {
        return GameHealth::getStatus();
    }

    // Helper - Is database healthy
    public static function isDatabaseHealthy(): bool {
        return self::checkDatabase(); 
    }

    // Helper - Is system settings healthy
    public static function isSystemSettingsHealthy(): bool {
        return self::checkSystemSettings();
    }

    // Helper - Is environment healthy
    public static function isEnvironmentHealthy(): bool {
        return self::checkEnvironment(); 
    }

    // Helper - Is game healthy
    public static function isGameHealthy(): bool {
        return GameHealth::isHealthy();
    }
}