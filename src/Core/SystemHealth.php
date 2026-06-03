<?php 
// Core/SystemHealth.php
namespace App\Core; 

use App\Constants\Application; 
use Throwable;

final class SystemHealth {
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

            return [
                'status' => 'ok', 
                'reachable' => 'true', 
                'latency_ms' => $time_latency, 
                'threads_connected' => $connection_info[0]['Value'] ?? null, 
                'connections_ok' => (isset($connection_info[0]['Value']) && $connection_info[0]['Value'] > 0) ? true : false,  
                'db_name' => $_ENV['DB_NAME'] ?? null, 
                'db_host' => $_ENV['DB_HOST'] ?? null, 
                'db_version' => $version_info['version'] ?? null, 
                'db_comment' => $version_info['comment'] ?? null, 
            ]; 
        } catch (Throwable $e) {
            return [
                'status' => 'fail', 
                'reachable' => 'false', 
                'latency_ms' => null, 
                'threads_connected' => null, 
                'connections_ok' => false,  
                'db_name' => $_ENV['DB_NAME'] ?? null, 
                'db_host' => $_ENV['DB_HOST'] ?? null, 
                'db_version' => null, 
                'db_comment' => null, 
                'error' => $e->getMessage(), 
            ]; 
        }
    }

    // Health - Environment
    public static function getEnvironmentDetails(): array {
        return [
            'status' => (self::checkEnvironment()) ? 'ok' : 'fail', 
            'app_env' => Env::get(), 
            'is_dev' => Env::isDev(), 
            'is_prod' => Env::isProd(), 
            'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN), 
            'app_name' => $_ENV['APP_NAME'] ?? null, 
            'php_version' => PHP_VERSION, 
            'memory_limit' => ini_get('memory_limit'), 
            'timezone' => date_default_timezone_get(), 
        ]; 
    }

    // Health - Game
    public static function getGameDetails(): array {
        return GameHealth::getApiHealthDetails();
    }

    /**
     * Helper
     */
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