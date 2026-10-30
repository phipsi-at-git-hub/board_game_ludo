<?php
// src/Core/Dto/System/SettingsContext.php

namespace App\Core\Dto\System;

use App\Constants\ApplicationConstants;
use App\Core\Localization;
use App\Models\System\SystemSettingsModel;
use App\Services\SystemService;

final class SettingsContext
{
    private static function create(): array
    {
        return [
            'system_enabled' => false,
            'maintenance_mode_enabled' => false,
            'system_notice_enabled' => false,

            'registration_enabled' => false,
            'login_enabled' => false,

            'game_creation_enabled' => false,
            'game_play_enabled' => false,

            // Overview badges
            'system_state' => null,
            'system_state_classes' => null,
            'system_state_status' => null,

            'authentication_status' => null,
            'authentication_status_classes' => null,
            'authentication_status_label' => null,

            'games_status' => null,
            'games_status_classes' => null,
            'games_status_label' => null,

            'maintenance_status' => null,
            'maintenance_status_classes' => null,
            'maintenance_status_label' => null,
        ];
    }

    public static function fromSystem(SystemSettingsModel $settings): array
    {
        $dto = self::create();
        $system_settings = new SystemService();

        // Raw settings
        $dto['system_enabled'] = $settings->getSystemEnabled();
        $dto['maintenance_mode_enabled'] = $settings->getMaintenanceModeEnabled();
        $dto['system_notice_enabled'] = $settings->getSystemNoticeEnabled();

        $dto['registration_enabled'] = $settings->getRegistrationEnabled();
        $dto['login_enabled'] = $settings->getLoginEnabled();

        $dto['game_creation_enabled'] = $settings->getGameCreationEnabled();
        $dto['game_play_enabled'] = $settings->getGamePlayEnabled();

        // Overview State 
        $dto['system_state_label'] = $system_settings->isSystemEnabled() ? strtoupper(Localization::get('application.general.online')) : strtoupper(Localization::get('application.general.offline'));
        $dto['system_state_status'] = $system_settings->isSystemEnabled() ? ApplicationConstants::RESULT_OK : ApplicationConstants::RESULT_FAIL;

        // Authentication Badge 
        $authentication_enabled = $system_settings->isAuthenticationEnabled();
        $dto['authentication_status'] = $authentication_enabled ? ApplicationConstants::RESULT_OK : ApplicationConstants::RESULT_FAIL;
        $dto['authentication_status_classes'] = 'status-' . $dto['authentication_status'];  // status is css class prefix
        $dto['authentication_status_label'] = strtoupper($authentication_enabled ? ApplicationConstants::VALUE_ON : ApplicationConstants::VALUE_OFF);

        // Games Badge 
        $games_enabled = $system_settings->isGameEnabled();
        $dto['games_status'] = $games_enabled ? ApplicationConstants::RESULT_OK : ApplicationConstants::RESULT_FAIL;
        $dto['games_status_classes'] = 'status-' . $dto['games_status'];  // status is css class prefix
        $dto['games_status_label'] = strtoupper($games_enabled ? ApplicationConstants::VALUE_ON : ApplicationConstants::VALUE_OFF);

        // Maintenance Badge 
        $maintenance_enabled = $system_settings->isMaintenanceActive();
        $dto['maintenance_status'] = !$maintenance_enabled ? ApplicationConstants::RESULT_OK : ApplicationConstants::RESULT_WARNING;
        $dto['maintenance_status_classes'] = 'status-' . $dto['maintenance_status'];  // status is css class prefix
        $dto['maintenance_status_label'] = strtoupper(!$maintenance_enabled ? ApplicationConstants::VALUE_OFF : ApplicationConstants::VALUE_ON);

        return $dto;
    }
}
