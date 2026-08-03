<?php
// src/Services/SystemService.php

namespace App\Services;

use App\Constants\Application;
use App\Models\SystemSettingsModel;
use App\Models\UserModel;

final class SystemService {
    private SystemSettingsModel $settings;

    public function __construct(?SystemSettingsModel $settings = null) {
        $this->settings = $settings ?? SystemSettingsModel::findSystemSettings();
    }

    /**
     * Settings
     */
    public function getSettings(): SystemSettingsModel {
        return $this->settings;
    }

    public function reload(): SystemSettingsModel {
        $this->settings = SystemSettingsModel::findSystemSettings();
        return $this->settings;
    }

    /**
     * Basic state
     */
    // Helper - Is system settings valid
    public function isValid(): bool {
        return $this->settings->isValid();
    }

    // Helper - Is system enabled
    public function isSystemEnabled(): bool {
        return $this->settings->getSystemEnabled();
    }

    // Helper - Is system disabled
    public function isSystemDisabled(): bool {
        return !$this->isSystemEnabled();
    }

    // Helper - Is loggings debugs enabled
    public function isLoggingDebugEnabled(): bool {
        return $this->settings->getLoggingDebugEnabled(); 
    }

    // Helper - Is user registration enabled enabled
    public function isRegistrationEnabled(): bool {
        return $this->settings->getRegistrationEnabled();
    }

    // Helper - Is user login enabled enabled
    public function isLoginEnabled(): bool {
        return $this->settings->getLoginEnabled();
    }

    // Helper - Is game creation enabled
    public function isGameCreationEnabled(): bool {
        return $this->settings->getGameCreationEnabled();
    }

    // Helper - Is game play enabled
    public function isGamePlayEnabled(): bool {
        return $this->settings->getGamePlayEnabled();
    }

    // Helper - Is maintenance mode enabled
    public function isMaintenanceModeEnabled(): bool {
        return $this->settings->getMaintenanceModeEnabled();
    }

    // Helper - Is system notice enabled
    public function isSystemNoticeEnabled(): bool {
        return $this->settings->getSystemNoticeEnabled();
    }

    /**
     * Messages
     */
    // Helper - show maintenance message
    public function getMaintenanceMessage(): ?string {
        return $this->settings->getMaintenanceMessage();
    }

    // Helper - show maintenance message as a string
    public function getMaintenanceMessageString(): ?string {
        return $this->settings->getMaintenanceMessageString();
    }

    // Helper - show system notice message
    public function getSystemNoticeMessage(): ?string {
        return $this->settings->getSystemNoticeMessage();
    }

    // Helper - show system notice message as a string
    public function getSystemNoticeMessageString(): ?string {
        return $this->settings->getSystemNoticeMessageString();
    }

    /**
     * Metadata
     */
    // Helper - Updated at
    public function getUpdatedAt(): string {
        return $this->settings->getUpdatedAt();
    }

    // Helper - Updated by
    public function getUpdatedBy(): ?UserModel {
        return UserModel::findById($this->settings->getUpdatedBy());
    }

    /**
     * Aggregated states
     */
    // Helper - Is authentication available
    public function isAuthenticationEnabled(): bool {
        return ($this->isRegistrationEnabled() && $this->isLoginEnabled());
    }

    // Helper - Are games generally available
    public function isGameEnabled(): bool {
        return ($this->isGameCreationEnabled() && $this->isGamePlayEnabled());
    }

    // Is any maintenance related state active
    public function isMaintenanceActive(): bool {
        return ($this->isMaintenanceModeEnabled() || $this->isSystemNoticeEnabled()) || $this->isLoggingDebugEnabled();
    }

    // Is the system fully operational
    public function isFullyOperational(): bool {
        return (
            $this->isSystemEnabled()
            && $this->isAuthenticationEnabled()
            && $this->isGameEnabled()
            && !$this->isMaintenanceActive()
        );
    }

    /**
     * Status helpers
     */
    // Helper - Get system settings authentication status as String
    public function getAuthenticationStatus(): string {
        if ($this->isRegistrationEnabled() && $this->isLoginEnabled()) {
            return Application::GENERAL_ON;
        }

        if ($this->isRegistrationEnabled() xor $this->isLoginEnabled()) {
            return Application::GENERAL_PARTIAL;
        }
        return Application::GENERAL_OFF;
    }

    // Helper - Get system settings games status as String
    public function getGamesStatus(): string {
        if ($this->isGameCreationEnabled() && $this->isGamePlayEnabled()) {
            return Application::GENERAL_ON;
        }

        if ($this->isGameCreationEnabled() xor $this->isGamePlayEnabled()) {
            return Application::GENERAL_PARTIAL;
        }
        return Application::GENERAL_OFF;
    }

    // Helper - Get system settings maintenance status as String
    public function getMaintenanceStatus(): string {
        if ($this->isMaintenanceModeEnabled() && $this->isSystemNoticeEnabled()) {
            return Application::GENERAL_ON;
        }

        if ($this->isMaintenanceModeEnabled() xor $this->isSystemNoticeEnabled()) {
            return Application::GENERAL_PARTIAL;
        }
        return Application::GENERAL_OFF;
    }
}
