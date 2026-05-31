<?php 
// src/Models/SystemSettingsModel.php
namespace App\Models; 

use App\Constants\Application; 

final class SystemSettingsModel extends BaseModel {
    private bool $registration_enabled; 
    private bool $login_enabled; 
    private bool $game_creation_enabled; 
    private bool $game_play_enabled; 
    private bool $maintenance_mode_enabled; 
    private string|null $maintenance_message; 
    private bool $system_notice_enabled; 
    private string|null $system_notice_message; 
    private string $updated_at; 
    private string $updated_by; 

    // Define default values
    private const DEFAULT_REGISTRATION_ENABLED = false; 
    private const DEFAULT_LOGIN_ENABLED = false; 
    private const DEFAULT_GAME_CREATION_ENABLED = false; 
    private const DEFAULT_GAME_PLAY_ENABLED = false; 
    private const DEFAULT_MAINTENANCE_MODE_ENABLED = true; 
    private const DEFAULT_MAINTENANCE_MESSAGE = 'system.message.system_unavailable'; 
    private const DEFAULT_SYSTEM_NOTICE_ENABLED = true; 
    private const DEFAULT_SYSTEM_NOTICE_MESSAGE = 'system.message.system_recovery_mode'; 

    public function initializeDefaultSettings(): self {
        $system_settings = new self; 

        $system_settings->registration_enabled = self::DEFAULT_REGISTRATION_ENABLED; 
        $system_settings->login_enabled = self::DEFAULT_LOGIN_ENABLED; 
        $system_settings->game_creation_enabled = self::DEFAULT_GAME_CREATION_ENABLED; 
        $system_settings->game_play_enabled = self::DEFAULT_GAME_PLAY_ENABLED; 
        $system_settings->maintenance_mode_enabled = self::DEFAULT_MAINTENANCE_MODE_ENABLED; 
        $system_settings->maintenance_message = self::DEFAULT_MAINTENANCE_MESSAGE; 
        $system_settings->system_notice_enabled = self::DEFAULT_SYSTEM_NOTICE_ENABLED; 
        $system_settings->system_notice_message = self::DEFAULT_SYSTEM_NOTICE_MESSAGE; 

        return $system_settings; 
    }
}
