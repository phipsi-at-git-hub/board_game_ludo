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

    public static function initializeDefaultSettings(): self {
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

    public static function findSystemSettings(): self {
        $system_settings = new self;

        // Load all settings of system
        $row = static::fetchOne(
            sprintf(
                "SELECT *
                FROM %s s
                LIMIT 1", 

                Application::TABLE_SYSTEM_SETTINGS
            ), 
            []
        );

        /*
        if ($row === null || !self::isValid($row)) {
            return self::initializeDefaultSettings(); 
        }
        */

        $system_settings = self::fromArray($row);

        if (!$system_settings->isValid()) {
            return self::initializeDefaultSettings(); 
        }
        return $system_settings; 
    }

    // Helper - Validate SystemSettings
    public function isValid(): bool {
        if ($this->updated_by === null) {
            return false; 
        }

        if ($this->maintenance_mode_enabled && (trim($this->maintenance_message) === '' || $this->maintenance_message === null)) {
            return false;
        }

        if ($this->system_notice_enabled && (trim($this->system_notice_message) === '' || $this->system_notice_message === null)) {
            return false; 
        }
        return true;
    }

    // Helper - Create SystemSettings from Array
    private static function fromArray(array $data): self {
        $system_settings = new self;

        /*
        $system_settings->registration_enabled = $data[Application::REGISTRATION_ENABLED];
        $system_settings->login_enabled = $data[Application::LOGIN_ENABLED];
        $system_settings->game_creation_enabled = $data[Application::GAME_CREATION_ENABLED];
        $system_settings->game_play_enabled = $data[Application::GAME_PLAY_ENABLED];
        $system_settings->maintenance_mode_enabled = $data[Application::MAINTENANCE_MODE_ENABLED];
        $system_settings->maintenance_message = $data[Application::MAINTENANCE_MESSAGE]; 
        $system_settings->system_notice_enabled = $data[Application::SYSTEM_NOTICE_ENABLED]; 
        $system_settings->system_notice_message = $data[Application::SYSTEM_NOTICE_MESSAGE]; 
        $system_settings->updated_at = $data[Application::UPDATED_AT]; 
        $system_settings->updated_by = $data[Application::UPDATED_BY]; 
        */

        $system_settings->registration_enabled = self::hydrateBoolean($data, Application::REGISTRATION_ENABLED);
        $system_settings->login_enabled = self::hydrateBoolean($data, Application::LOGIN_ENABLED);
        $system_settings->game_creation_enabled = self::hydrateBoolean($data, Application::GAME_CREATION_ENABLED);
        $system_settings->game_play_enabled = self::hydrateBoolean($data, Application::GAME_PLAY_ENABLED);
        $system_settings->maintenance_mode_enabled = self::hydrateBoolean($data, Application::MAINTENANCE_MODE_ENABLED);
        $system_settings->maintenance_message = self::hydrateStringOrNull($data, Application::MAINTENANCE_MESSAGE);
        $system_settings->system_notice_enabled = self::hydrateBoolean($data, Application::SYSTEM_NOTICE_ENABLED);
        $system_settings->system_notice_message = self::hydrateStringOrNull($data, Application::SYSTEM_NOTICE_MESSAGE);
        $system_settings->updated_at = self::hydrateString($data, Application::UPDATED_AT);
        $system_settings->updated_by = self::hydrateUUIDOrNull($data, Application::UPDATED_BY);

        return $system_settings;
    }

    // Helper - Create Array from SystemSettings
    private function toArray(): array {
        $system_settings_array[Application::REGISTRATION_ENABLED] = $this->registration_enabled;
        $system_settings_array[Application::LOGIN_ENABLED] = $this->login_enabled;
        $system_settings_array[Application::GAME_CREATION_ENABLED] = $this->game_creation_enabled;
        $system_settings_array[Application::GAME_PLAY_ENABLED] = $this->game_play_enabled;
        $system_settings_array[Application::MAINTENANCE_MODE_ENABLED] = $this->maintenance_mode_enabled;
        $system_settings_array[Application::MAINTENANCE_MESSAGE] = $this->maintenance_message;
        $system_settings_array[Application::SYSTEM_NOTICE_ENABLED] = $this->system_notice_enabled; 
        $system_settings_array[Application::SYSTEM_NOTICE_MESSAGE] = $this->system_notice_message; 
        $system_settings_array[Application::UPDATED_BY] = $this->updated_by; 

        return $system_settings_array;
    }
}
