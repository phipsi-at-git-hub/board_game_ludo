<?php 
// Core/SystemSettings.php
namespace App\Core; 

use App\Models\SystemSettingsModel; 

final class SystemSettings {
    private static ?SystemSettingsModel $system_settings = null; 

    public static function get(): SystemSettingsModel {
        if (self::$system_settings === null) {
            self::$system_settings = SystemSettingsModel::findSystemSettings();
        }
        return self::$system_settings; 
    }

    public static function reload(): SystemSettingsModel {
        self::$system_settings = SystemSettingsModel::findSystemSettings(); 
        return self::$system_settings; 
    }

    public static function isLoaded(): bool {
        return self::$system_settings !== null; 
    }

    /**
     * Helper
     */
    // Helper - Is system offline
    public static function isOffline(): bool {
        return !self::isSystemEnabled();
    }
    
    // Helper - Is system enabled
    public static function isSystemEnabled(): bool {
        return self::get()->getSystemEnabled();
    }

    // Helper - Is user login enabled
    public static function isLoginEnabled(): bool {
        return self::get()->getLoginEnabled();
    }

    // Helper - Is user registration enabled
    public static function isRegistrationEnabled(): bool {
        return self::get()->getRegistrationEnabled();
    }

    // Helper - Is game creation enabled
    public static function isGameCreationEnabled(): bool {
        return self::get()->getGameCreationEnabled();
    }

    // Helper - Is game play enabled
    public static function isGamePlayEnabled(): bool {
        return self::get()->getGamePlayEnabled();
    }

    // Helper - Is maintenance mode enabled
    public static function isMaintenanceModeEnabled(): bool {
        return self::get()->getMaintenanceModeEnabled(); 
    }
}
