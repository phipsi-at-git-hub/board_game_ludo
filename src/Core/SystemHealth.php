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
            && self::checkSystemSettings() 
            && self::checkEnvironment(); 
    }

    // Status for admin dashboard
    public static function getStatus(): string {
        if (!self::checkDatabase() || !self::checkEnvironment()) {
            return Application::GENERAL_CRITICAL; 
        }
        return Application::GENERAL_HEALTHY; 
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
        return true;
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