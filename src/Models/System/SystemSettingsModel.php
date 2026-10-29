<?php
// src/Models/System/SystemSettingsModel.php
namespace App\Models\System;

use App\Constants\Application;
use App\Constants\Database\DbColumnConstants;
use App\Constants\Database\DbTableConstants;
use App\Models\BaseModel;

/**
 * SystemSettingsModel
 */
final class SystemSettingsModel extends BaseModel
{
    private string $id;
    private bool $registration_enabled;
    private bool $login_enabled;
    private bool $system_enabled;
    private bool $logging_debug_enabled;
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
    private const DEFAULT_SYSTEM_ENABLED = false;
    private const DEFAULT_LOGGING_DEBUG_ENABLED = false;
    private const DEFAULT_GAME_CREATION_ENABLED = false;
    private const DEFAULT_GAME_PLAY_ENABLED = false;
    private const DEFAULT_MAINTENANCE_MODE_ENABLED = true;
    private const DEFAULT_MAINTENANCE_MESSAGE = 'system.message.system_unavailable';
    private const DEFAULT_SYSTEM_NOTICE_ENABLED = true;
    private const DEFAULT_SYSTEM_NOTICE_MESSAGE = 'system.message.system_recovery_mode';

    /**
     * initializeDefaultSettings
     *
     * @return self
     */
    public static function initializeDefaultSettings(): self
    {
        $system_settings = new self;

        $system_settings->registration_enabled = self::DEFAULT_REGISTRATION_ENABLED;
        $system_settings->login_enabled = self::DEFAULT_LOGIN_ENABLED;
        $system_settings->system_enabled = self::DEFAULT_SYSTEM_ENABLED;
        $system_settings->logging_debug_enabled = self::DEFAULT_LOGGING_DEBUG_ENABLED;
        $system_settings->game_creation_enabled = self::DEFAULT_GAME_CREATION_ENABLED;
        $system_settings->game_play_enabled = self::DEFAULT_GAME_PLAY_ENABLED;
        $system_settings->maintenance_mode_enabled = self::DEFAULT_MAINTENANCE_MODE_ENABLED;
        $system_settings->maintenance_message = self::DEFAULT_MAINTENANCE_MESSAGE;
        $system_settings->system_notice_enabled = self::DEFAULT_SYSTEM_NOTICE_ENABLED;
        $system_settings->system_notice_message = self::DEFAULT_SYSTEM_NOTICE_MESSAGE;

        return $system_settings;
    }

    /**
     * findSystemSettings
     *
     * @return self
     */
    public static function findSystemSettings(): self
    {
        $system_settings = new self;

        // Load all settings of system
        $row = static::fetchOne(
            sprintf(
                "SELECT *
                FROM %s s
                LIMIT 1",

                DbTableConstants::SYSTEM_SETTINGS
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

    /**
     * update
     *
     * @return void
     */
    public function update()
    {
        return static::execute(
            sprintf(
                "UPDATE 
                    %s
                SET
                    %s = :registration_enabled, 
                    %s = :login_enabled, 
                    %s = :game_creation_enabled, 
                    %s = :game_play_enabled,  
                    %s = :maintenance_mode_enabled, 
                    %s = :maintenance_message, 
                    %s = :system_notice_enabled, 
                    %s = :system_notice_message, 
                    %s = :logging_debug_enabled, 
                    %s = :system_enabled 
                WHERE 
                    %s = :id",

                DbTableConstants::SYSTEM_SETTINGS,

                DbColumnConstants::REGISTRATION_ENABLED,
                DbColumnConstants::LOGIN_ENABLED,
                DbColumnConstants::GAME_CREATION_ENABLED,
                DbColumnConstants::GAME_PLAY_ENABLED,
                DbColumnConstants::MAINTENANCE_MODE_ENABLED,
                DbColumnConstants::MAINTENANCE_MESSAGE,
                DbColumnConstants::SYSTEM_NOTICE_ENABLED,
                DbColumnConstants::SYSTEM_NOTICE_MESSAGE,
                DbColumnConstants::LOGGING_DEBUG_ENABLED,
                DbColumnConstants::SYSTEM_ENABLED,

                DbColumnConstants::ID
            ),
            [
                DbColumnConstants::REGISTRATION_ENABLED => (int)$this->registration_enabled,
                DbColumnConstants::LOGIN_ENABLED => (int)$this->login_enabled,
                DbColumnConstants::GAME_CREATION_ENABLED => (int)$this->game_creation_enabled,
                DbColumnConstants::GAME_PLAY_ENABLED => (int)$this->game_play_enabled,
                DbColumnConstants::MAINTENANCE_MODE_ENABLED => (int)$this->maintenance_mode_enabled,
                DbColumnConstants::MAINTENANCE_MESSAGE => (string)$this->maintenance_message,
                DbColumnConstants::SYSTEM_NOTICE_ENABLED => (int)$this->system_notice_enabled,
                DbColumnConstants::SYSTEM_NOTICE_MESSAGE => (string)$this->system_notice_message,
                DbColumnConstants::LOGGING_DEBUG_ENABLED => (int)$this->logging_debug_enabled,
                DbColumnConstants::SYSTEM_ENABLED => (int)$this->system_enabled,
                DbColumnConstants::ID => (string)$this->id
            ]
        );
    }

    /**
     * updateFromArray
     *
     * @param array $data
     * @return void
     */
    public function updateFromArray(array $data): void
    {
        if (array_key_exists(DbColumnConstants::REGISTRATION_ENABLED, $data)) {
            $this->registration_enabled = self::hydrateBoolean($data, DbColumnConstants::REGISTRATION_ENABLED);
        }
        if (array_key_exists(DbColumnConstants::LOGIN_ENABLED, $data)) {
            $this->login_enabled = self::hydrateBoolean($data, DbColumnConstants::LOGIN_ENABLED);
        }
        if (array_key_exists(DbColumnConstants::GAME_CREATION_ENABLED, $data)) {
            $this->game_creation_enabled = self::hydrateBoolean($data, DbColumnConstants::GAME_CREATION_ENABLED);
        }
        if (array_key_exists(DbColumnConstants::GAME_PLAY_ENABLED, $data)) {
            $this->game_play_enabled = self::hydrateBoolean($data, DbColumnConstants::GAME_PLAY_ENABLED);
        }
        if (array_key_exists(DbColumnConstants::SYSTEM_ENABLED, $data)) {
            $this->system_enabled = self::hydrateBoolean($data, DbColumnConstants::SYSTEM_ENABLED);
        }
        if (array_key_exists(DbColumnConstants::LOGGING_DEBUG_ENABLED, $data)) {
            $this->logging_debug_enabled = self::hydrateBoolean($data, DbColumnConstants::LOGGING_DEBUG_ENABLED);
        }
        if (array_key_exists(DbColumnConstants::MAINTENANCE_MODE_ENABLED, $data)) {
            $this->maintenance_mode_enabled = self::hydrateBoolean($data, DbColumnConstants::MAINTENANCE_MODE_ENABLED);
        }
        if (array_key_exists(DbColumnConstants::MAINTENANCE_MESSAGE, $data)) {
            $this->maintenance_message = self::hydrateString($data, DbColumnConstants::MAINTENANCE_MESSAGE);
        }
        if (array_key_exists(DbColumnConstants::SYSTEM_NOTICE_ENABLED, $data)) {
            $this->system_notice_enabled = self::hydrateBoolean($data, DbColumnConstants::SYSTEM_NOTICE_ENABLED);
        }
        if (array_key_exists(DbColumnConstants::SYSTEM_NOTICE_MESSAGE, $data)) {
            $this->system_notice_message = self::hydrateString($data, DbColumnConstants::SYSTEM_NOTICE_MESSAGE);
        }
    }

    /*
     * Helper
     */

    /**
     * updateBooleansToFalse
     * 
     * Helper - Set Booleans to false
     *
     * @return void
     */
    public function updateBooleansToFalse(): void
    {
        $this->registration_enabled = false;
        $this->login_enabled = false;
        $this->system_enabled = false;
        $this->logging_debug_enabled = false;
        $this->game_creation_enabled = false;
        $this->game_play_enabled = false;
        $this->maintenance_mode_enabled = false;
        $this->system_notice_enabled = false;
    }

    /**
     * isValid
     * 
     * Helper - Validate SystemSettings
     *
     * @return bool
     */
    public function isValid(): bool
    {
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

    /**
     * fromArray
     * 
     * Helper - Create SystemSettings from Array
     *
     * @param array $data
     * @return self
     */
    private static function fromArray(array $data): self
    {
        $system_settings = new self;

        if (array_key_exists(DbColumnConstants::ID, $data)) $system_settings->id = (string)$data[DbColumnConstants::ID];
        if (array_key_exists(DbColumnConstants::REGISTRATION_ENABLED, $data)) $system_settings->registration_enabled = (bool)$data[DbColumnConstants::REGISTRATION_ENABLED];
        if (array_key_exists(DbColumnConstants::LOGIN_ENABLED, $data)) $system_settings->login_enabled = (bool)$data[DbColumnConstants::LOGIN_ENABLED];
        if (array_key_exists(DbColumnConstants::SYSTEM_ENABLED, $data)) $system_settings->system_enabled = (bool)$data[DbColumnConstants::SYSTEM_ENABLED];
        if (array_key_exists(DbColumnConstants::LOGGING_DEBUG_ENABLED, $data)) $system_settings->logging_debug_enabled = (bool)$data[DbColumnConstants::LOGGING_DEBUG_ENABLED];
        if (array_key_exists(DbColumnConstants::GAME_CREATION_ENABLED, $data)) $system_settings->game_creation_enabled = (bool)$data[DbColumnConstants::GAME_CREATION_ENABLED];
        if (array_key_exists(DbColumnConstants::GAME_PLAY_ENABLED, $data)) $system_settings->game_play_enabled = (bool)$data[DbColumnConstants::GAME_PLAY_ENABLED];
        if (array_key_exists(DbColumnConstants::MAINTENANCE_MODE_ENABLED, $data)) $system_settings->maintenance_mode_enabled = (bool)$data[DbColumnConstants::MAINTENANCE_MODE_ENABLED];
        if (array_key_exists(DbColumnConstants::MAINTENANCE_MESSAGE, $data)) $system_settings->maintenance_message = (string)$data[DbColumnConstants::MAINTENANCE_MESSAGE];
        if (array_key_exists(DbColumnConstants::SYSTEM_NOTICE_ENABLED, $data)) $system_settings->system_notice_enabled = (bool)$data[DbColumnConstants::SYSTEM_NOTICE_ENABLED];
        if (array_key_exists(DbColumnConstants::SYSTEM_NOTICE_MESSAGE, $data)) $system_settings->system_notice_message = (string)$data[DbColumnConstants::SYSTEM_NOTICE_MESSAGE];
        if (array_key_exists(DbColumnConstants::UPDATED_AT, $data)) $system_settings->updated_at = (string)$data[DbColumnConstants::UPDATED_AT];
        if (array_key_exists(DbColumnConstants::UPDATED_BY, $data)) $system_settings->updated_by = (string)$data[DbColumnConstants::UPDATED_BY];

        return $system_settings;
    }

    /**
     * toArray
     * 
     * Helper - Create Array from SystemSettings
     *
     * @return array
     */
    public function toArray(): array
    {
        $system_settings_array[DbColumnConstants::REGISTRATION_ENABLED] = $this->registration_enabled;
        $system_settings_array[DbColumnConstants::LOGIN_ENABLED] = $this->login_enabled;
        $system_settings_array[DbColumnConstants::SYSTEM_ENABLED] = $this->system_enabled;
        $system_settings_array[DbColumnConstants::LOGGING_DEBUG_ENABLED] = $this->logging_debug_enabled;
        $system_settings_array[DbColumnConstants::GAME_CREATION_ENABLED] = $this->game_creation_enabled;
        $system_settings_array[DbColumnConstants::GAME_PLAY_ENABLED] = $this->game_play_enabled;
        $system_settings_array[DbColumnConstants::MAINTENANCE_MODE_ENABLED] = $this->maintenance_mode_enabled;
        $system_settings_array[DbColumnConstants::MAINTENANCE_MESSAGE] = $this->maintenance_message;
        $system_settings_array[DbColumnConstants::SYSTEM_NOTICE_ENABLED] = $this->system_notice_enabled;
        $system_settings_array[DbColumnConstants::SYSTEM_NOTICE_MESSAGE] = $this->system_notice_message;
        $system_settings_array[DbColumnConstants::UPDATED_BY] = $this->updated_by;

        return $system_settings_array;
    }

    /**
     * getMaintenanceMessageString
     * 
     * Helper - get system notice message as string
     *
     * @return string
     */
    public function getMaintenanceMessageString(): string
    {
        return ($this->maintenance_message) ?? '';
    }

    /**
     * getSystemNoticeMessageString
     * 
     * Helper - get system notice message as string
     *
     * @return string
     */
    public function getSystemNoticeMessageString(): string
    {
        return ($this->system_notice_message) ?? '';
    }

    /*
     * Getter
     */

    /**
     * getArrayOfModel
     * 
     * Getter - get array of model
     *
     * @return void
     */
    public function getArrayOfModel()
    {
        return $this->toArray();
    }

    /**
     * getRegistrationEnabled
     * 
     * Getter - get registration enabled
     *
     * @return void
     */
    public function getRegistrationEnabled()
    {
        return $this->registration_enabled;
    }

    /**
     * getLoginEnabled
     * 
     * Getter - get login enabled
     *
     * @return void
     */
    public function getLoginEnabled()
    {
        return $this->login_enabled;
    }

    /**
     * getSystemEnabled
     * 
     * Getter - get system enabled
     *
     * @return void
     */
    public function getSystemEnabled()
    {
        return $this->system_enabled;
    }

    /**
     * getLoggingDebugEnabled
     * 
     * Getter - get logging debug enabled
     *
     * @return void
     */
    public function getLoggingDebugEnabled()
    {
        return $this->logging_debug_enabled;
    }

    /**
     * getGameCreationEnabled
     * 
     * Getter - get game creation enabled
     *
     * @return void
     */
    public function getGameCreationEnabled()
    {
        return $this->game_creation_enabled;
    }

    /**
     * getGamePlayEnabled
     * 
     * Getter - get game play enabled
     *
     * @return void
     */
    public function getGamePlayEnabled()
    {
        return $this->game_play_enabled;
    }

    /**
     * getMaintenanceModeEnabled
     * 
     * Getter - get maintenance mode enabled
     *
     * @return void
     */
    public function getMaintenanceModeEnabled()
    {
        return $this->maintenance_mode_enabled;
    }
    
    /**
     * getMaintenanceMessage
     * 
     * Getter - get maintenance message
     *
     * @return void
     */
    public function getMaintenanceMessage()
    {
        return $this->maintenance_message;
    }

    /**
     * getSystemNoticeEnabled
     * 
     * Getter - get system notice enabled
     *
     * @return void
     */
    public function getSystemNoticeEnabled()
    {
        return $this->system_notice_enabled;
    }
    
    /**
     * getSystemNoticeMessage
     * 
     * Getter - get system notice message
     *
     * @return void
     */
    public function getSystemNoticeMessage()
    {
        return $this->system_notice_message;
    }

    /**
     * getUpdatedAt
     * 
     * Getter - get updated at
     *
     * @return void
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
    
    /**
     * getUpdatedBy
     * 
     * Getter - get updated by
     *
     * @return void
     */
    public function getUpdatedBy()
    {
        return $this->updated_by;
    }

    /*
     * Setter
     */
    
    /**
     * setRegistrationEnabled
     * 
     * Setter - set registration enabled
     *
     * @param bool $registration_enabled
     * @return void
     */
    public function setRegistrationEnabled(bool $registration_enabled)
    {
        $this->registration_enabled = $registration_enabled;
        return $this;
    }

    /**
     * setLoginEnabled
     * 
     * Setter - set login enabled
     *
     * @param bool $login_enabled
     * @return void
     */
    public function setLoginEnabled(bool $login_enabled)
    {
        $this->login_enabled = $login_enabled;
        return $this;
    }
    
    /**
     * setSystemEnabled
     * 
     * Setter - set system enabled
     *
     * @param bool $system_enabled
     * @return void
     */
    public function setSystemEnabled(bool $system_enabled)
    {
        $this->system_enabled = $system_enabled;
        return $this;
    }
    
    /**
     * setLoggingDebugEnabled
     * 
     * Setter - set logging debug enabled
     *
     * @param bool $logging_debug_enabled
     * @return void
     */
    public function setLoggingDebugEnabled(bool $logging_debug_enabled)
    {
        $this->logging_debug_enabled = $logging_debug_enabled;
        return $this;
    }
    
    /**
     * setGameCreationEnabled
     * 
     * Setter - set game creation enabled
     *
     * @param bool $game_creation_enabled
     * @return void
     */
    public function setGameCreationEnabled(bool $game_creation_enabled)
    {
        $this->game_creation_enabled = $game_creation_enabled;
        return $this;
    }
    
    /**
     * setGamePlayEnabled
     * 
     * Setter - set game play enabled
     *
     * @param bool $game_play_enabled
     * @return void
     */
    public function setGamePlayEnabled(bool $game_play_enabled)
    {
        $this->game_play_enabled = $game_play_enabled;
        return $this;
    }
    
    /**
     * setMaintenanceModeEnabled
     * 
     * Setter - set maintenance mode enabled
     *
     * @param bool $maintenance_mode_enabled
     * @return void
     */
    public function setMaintenanceModeEnabled(bool $maintenance_mode_enabled)
    {
        $this->maintenance_mode_enabled = $maintenance_mode_enabled;
        return $this;
    }
    
    /**
     * setMaintenanceMessage
     * 
     * Setter - set maintenance message
     *
     * @param string $maintenance_message
     * @return void
     */
    public function setMaintenanceMessage(string $maintenance_message)
    {
        $this->maintenance_message = $maintenance_message;
        return $this;
    }
    
    /**
     * setSystemNoticeEnabled
     * 
     * Setter - set system notice enabled
     *
     * @param bool $system_notice_enabled
     * @return void
     */
    public function setSystemNoticeEnabled(bool $system_notice_enabled)
    {
        $this->system_notice_enabled = $system_notice_enabled;
        return $this;
    }

    /**
     * setSystemNoticeMessage
     * 
     * Setter - set system notice message
     *
     * @param string $system_notice_message
     * @return void
     */
    public function setSystemNoticeMessage(string $system_notice_message)
    {
        $this->system_notice_message = $system_notice_message;
        return $this;
    }
    
    /**
     * setUpdatedBy
     * 
     * Setter - set updated by
     *
     * @param string $updated_by
     * @return void
     */
    public function setUpdatedBy(string $updated_by)
    {
        $this->updated_by = $updated_by;
        return $this;
    }
}
