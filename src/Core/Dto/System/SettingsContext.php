<?php
// src/Core/Dto/System/SettingsContext.php

namespace App\Core\Dto\System;

use App\Constants\ApplicationConstants;
use App\Core\Localization;
use App\Models\System\SystemSettingsModel;
use App\Services\SystemService;

/**
 * SettingsContext
 */
final readonly class SettingsContext
{
    public bool $system_enabled; 
    public bool $maintenance_mode_enabled; 
    public bool $system_notice_enabled; 

    public bool $registration_enabled; 
    public bool $login_enabled; 

    public bool $game_creation_enabled; 
    public bool $game_play_enabled; 

    public string $system_state_label; 
    public string $system_state_status; 

    public string $authentication_status; 
    public string $authentication_status_classes; 
    public string $authentication_status_label; 

    public string $games_status; 
    public string $games_status_classes; 
    public string $games_status_label; 

    public string $maintenance_status; 
    public string $maintenance_status_classes; 
    public string $maintenance_status_label; 

    /**
     * __construct
     *
     * @param SystemSettingsModel $settings
     * @return void
     */
    public function __construct(SystemSettingsModel $settings) 
    {
        $system_settings = new SystemService();

        // Raw settings
        $this->system_enabled = $settings->getSystemEnabled();
        $this->maintenance_mode_enabled = $settings->getMaintenanceModeEnabled();
        $this->system_notice_enabled = $settings->getSystemNoticeEnabled();

        $this->registration_enabled = $settings->getRegistrationEnabled(); 
        $this->login_enabled = $settings->getLoginEnabled();

        $this->game_creation_enabled = $settings->getGameCreationEnabled();
        $this->game_play_enabled = $settings->getGamePlayEnabled();

        // Overview State 
        $this->system_state_label = $system_settings->isSystemEnabled() ? strtoupper(Localization::get('application.general.online')) : strtoupper(Localization::get('application.general.offline'));
        $this->system_state_status = $system_settings->isSystemEnabled() ? ApplicationConstants::RESULT_OK : ApplicationConstants::RESULT_FAIL;

        // Authentication Badge 
        $authentication_enabled = $system_settings->isAuthenticationEnabled();
        $this->authentication_status = $authentication_enabled ? ApplicationConstants::RESULT_OK : ApplicationConstants::RESULT_FAIL;
        $this->authentication_status_classes = 'status-' . $this->authentication_status;  // status is css class prefix
        $this->authentication_status_label = strtoupper($authentication_enabled ? ApplicationConstants::VALUE_ON : ApplicationConstants::VALUE_OFF);

        // Games Badge 
        $games_enabled = $system_settings->isGameEnabled();
        $this->games_status = $games_enabled ? ApplicationConstants::RESULT_OK : ApplicationConstants::RESULT_FAIL;
        $this->games_status_classes = 'status-' . $this->games_status;  // status is css class prefix
        $this->games_status_label = strtoupper($games_enabled ? ApplicationConstants::VALUE_ON : ApplicationConstants::VALUE_OFF);

        // Maintenance Badge 
        $maintenance_enabled = $system_settings->isMaintenanceActive();
        $this->maintenance_status = !$maintenance_enabled ? ApplicationConstants::RESULT_OK : ApplicationConstants::RESULT_WARNING;
        $this->maintenance_status_classes = 'status-' . $this->maintenance_status;  // status is css class prefix
        $this->maintenance_status_label = strtoupper(!$maintenance_enabled ? ApplicationConstants::VALUE_OFF : ApplicationConstants::VALUE_ON);
    } 

    /**
     * fromSystem 
     * 
     * Returns an array for compatibility reasons. 
     *
     * @param SystemSettingsModel $settings
     * @return array
     */
    public static function fromSystem(SystemSettingsModel $settings): array
    {
        $context = new self(settings: $settings); 

        return $context->jsonSerialize(); 
    }

    /**
     * jsonSerialize
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'system_enabled' => $this->system_enabled, 
            'maintenance_mode_enabled' => $this->maintenance_mode_enabled, 
            'system_notice_enabled' => $this->system_notice_enabled, 

            'registration_enabled' => $this->registration_enabled, 
            'login_enabled' => $this->login_enabled, 

            'game_creation_enabled' => $this->game_creation_enabled, 
            'game_play_enabled' => $this->game_play_enabled, 

            'system_state_label' => $this->system_state_label, 
            'system_state_status' => $this->system_state_status, 

            'authentication_status' => $this->authentication_status, 
            'authentication_status_classes' => $this->authentication_status_classes, 
            'authentication_status_label' => $this->authentication_status_label, 

            'games_status' => $this->games_status, 
            'games_status_classes' => $this->games_status_classes, 
            'games_status_label' => $this->games_status_label, 

            'maintenance_status' => $this->maintenance_status, 
            'maintenance_status_classes' => $this->maintenance_status_classes, 
            'maintenance_status_label' => $this->maintenance_status_label, 
        ]; 
    }
}
