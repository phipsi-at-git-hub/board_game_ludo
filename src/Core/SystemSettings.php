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

    public static function isSystemOffline(): bool {
        self::get();
        return !self::$system_settings->getSystemEnabled();
    }
}
