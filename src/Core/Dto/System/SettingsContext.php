<?php
// src/Core/Dto/System/SettingsContext.php

namespace App\Core\Dto\System;

use App\Constants\Application;
use App\Core\Localization;
use App\Core\SystemSettings;
use App\Models\SystemSettingsModel;

final class SettingsContext {
    private static function create(): array {
        return [
            'system_enabled' => false,
            'maintenance_mode_enabled' => false,

            'registration_enabled' => false,
            'login_enabled' => false,

            'game_creation_enabled' => false,
            'game_play_enabled' => false,

            'system_notice_enabled' => false,

            // Overview badges
            'system_state' => null,

            'authentication_status' => null,
            'authentication_status_label' => null,

            'games_status' => null,
            'games_status_label' => null,

            'maintenance_status' => null,
            'maintenance_status_label' => null,
        ];
    }

    public static function fromSystem(SystemSettingsModel $settings): array {
        $dto = self::create();

        // Raw settings
        $dto['system_enabled'] = $settings->getSystemEnabled();
        $dto['maintenance_mode_enabled'] = $settings->getMaintenanceModeEnabled();

        $dto['registration_enabled'] = $settings->getRegistrationEnabled();
        $dto['login_enabled'] = $settings->getLoginEnabled();

        $dto['game_creation_enabled'] = $settings->getGameCreationEnabled();
        $dto['game_play_enabled'] = $settings->getGamePlayEnabled();

        $dto['system_notice_enabled'] = $settings->getSystemNoticeEnabled();

        // Overview State
        $dto['system_state'] = SystemSettings::isSystemEnabled() ? strtoupper(Localization::get('application.general.online')) : strtoupper(Localization::get('application.general.offline'));

        // Authentication Badge
        $authentication_enabled = $settings->getRegistrationEnabled() && $settings->getLoginEnabled();
        $dto['authentication_status'] = $authentication_enabled ? Application::GENERAL_OK : Application::GENERAL_FAIL;
        $dto['authentication_status_label'] = strtoupper($authentication_enabled ? Application::GENERAL_ON : Application::GENERAL_OFF);

        // Games Badge
        $games_enabled = $settings->getGameCreationEnabled() && $settings->getGamePlayEnabled();
        $dto['games_status'] = $games_enabled ? Application::GENERAL_OK : Application::GENERAL_FAIL;
        $dto['games_status_label'] = strtoupper($games_enabled ? Application::GENERAL_ON : Application::GENERAL_OFF);

        // Maintenance Badge
        $maintenance_enabled = !$settings->getMaintenanceModeEnabled();
        $dto['maintenance_status'] = $maintenance_enabled ? Application::GENERAL_OK : Application::GENERAL_WARNING;
        $dto['maintenance_status_label'] = strtoupper($maintenance_enabled ? Application::GENERAL_ON : Application::GENERAL_OFF);
        
        return $dto;
    }
}
