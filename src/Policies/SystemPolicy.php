<?php
// src/Policies/SystemPolicy.php

namespace App\Policies;

use App\Models\SystemSettingsModel;

final class SystemPolicy {
    // Is the system globally enabled
    public static function isSystemEnabled(SystemSettingsModel $settings): bool {
        return $settings->getSystemEnabled();
    }

    // Is authentication available
    public static function isAuthenticationEnabled(SystemSettingsModel $settings): bool {
        return (
            $settings->getRegistrationEnabled()
            && $settings->getLoginEnabled()
        );
    }

    // Is registration available
    public static function isRegistrationEnabled(SystemSettingsModel $settings): bool {
        return $settings->getRegistrationEnabled();
    }

    // Is login available
    public static function isLoginEnabled(SystemSettingsModel $settings): bool {
        return $settings->getLoginEnabled();
    }

    // Is game creation available
    public static function isGameCreationEnabled(SystemSettingsModel $settings): bool {
        return $settings->getGameCreationEnabled();
    }

    // Is game play available
    public static function isGamePlayEnabled(SystemSettingsModel $settings): bool {
        return $settings->getGamePlayEnabled();
    }

    // Are games generally available
    public static function isGameEnabled(SystemSettingsModel $settings): bool {
        return (
            $settings->getGameCreationEnabled()
            && $settings->getGamePlayEnabled()
        );
    }

    // Is maintenance mode enabled
    public static function isMaintenanceModeEnabled(SystemSettingsModel $settings): bool {
        return $settings->getMaintenanceModeEnabled();
    }

    // Is a system notice active
    public static function isSystemNoticeEnabled(SystemSettingsModel $settings): bool {
        return $settings->getSystemNoticeEnabled();
    }

    // Is any maintenance related state active
    public static function isMaintenanceActive(SystemSettingsModel $settings): bool {
        return (
            $settings->getMaintenanceModeEnabled()
            || $settings->getSystemNoticeEnabled()
        );
    }

    // Is the system fully operational
    public static function isFullyOperational(SystemSettingsModel $settings): bool {
        return (
            self::isSystemEnabled($settings)
            && self::isAuthenticationEnabled($settings)
            && self::isGameEnabled($settings)
            && !self::isMaintenanceActive($settings)
        );
    }
}
