<?php 
// Core/SystemSettings.php
namespace App\Core;

use App\Constants\Application;
use App\Models\SystemSettingsModel;
use App\Models\UserModel;

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
    // Helper - Is system settings valid
    public static function isValid(): bool {
        return self::get()->isValid();
    }
    
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

    // Helper - show maintenance message
    public static function showMaintenanceMessage(): String|null {
        return self::get()->getMaintenanceMessage(); 
    }

    // Helper - Is system notice enabled
    public static function isSystemNoticeEnabled(): bool {
        return self::get()->getSystemNoticeEnabled(); 
    }

    // Helper - Show system notice enabled
    public static function showSystemNoticeMessage(): String|null {
        return self::get()->getSystemNoticeMessage(); 
    }

    // Helper - Updated at
    public static function wasUpdatedAt(): String {
        return self::get()->getUpdatedAt(); 
    }

    // Helper - Updated by
    public static function wasUpdatedBy(): ?UserModel {
        return UserModel::findById(self::get()->getUpdatedBy()); 
    }

    // Helper - Get system settings authentication status as String
    public static function getAuthenticationStatus(): string {
        $login = self::get()->getLoginEnabled(); 
        $registration = self::get()->getRegistrationEnabled(); 
        if ($login && $registration) {
            return Application::GENERAL_ON; 
        }
        if ($login xor $registration) {
            return Application::GENERAL_PARTIAL; 
        }
        return Application::GENERAL_OFF; 
    }

    // Helper - Get system settings games status as String
    public static function getGamesStatus(): string {
        $creation = self::get()->getGameCreationEnabled(); 
        $play = self::get()->getGamePlayEnabled(); 
        if ($creation && $play) {
            return Application::GENERAL_ON; 
        }
        if ($creation xor $play) {
            return Application::GENERAL_PARTIAL; 
        }
        return Application::GENERAL_OFF; 
    }

    // Helper - Get system settings maintenance status as String
    public static function getMaintenanceStatus(): string {
        $maintenance_mode = self::get()->getMaintenanceModeEnabled(); 
        $system_notice = self::get()->getSystemNoticeEnabled(); 
        if ($maintenance_mode && $system_notice) {
            return Application::GENERAL_ON; 
        }
        if ($maintenance_mode xor $system_notice) {
            return Application::GENERAL_PARTIAL; 
        }
        return Application::GENERAL_OFF;
    }
}
