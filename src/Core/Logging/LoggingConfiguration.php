<?php
// src/Core/Logging/LoggingConfiguration.php

namespace App\Core\Logging;

final class LoggingConfiguration {
    public const CHANNEL_APPLICATION = 'application';
    public const CHANNEL_SYSTEM = 'system';
    public const CHANNEL_GAME = 'game';

    private const LOG_STORAGE = [
        'system' => 'file', 
        'application' => 'file', 
        'game' => 'database', 
    ]; 

    private const LOG_FILES = [
        self::CHANNEL_APPLICATION => LOG_PATH . '/application.log', 
        self::CHANNEL_SYSTEM => LOG_PATH . '/system.log', 
    ]; 

    public const LEVEL_EMERGENCY = 'emergency';
    public const LEVEL_ALERT = 'alert';
    public const LEVEL_CRITICAL = 'critical';
    public const LEVEL_ERROR = 'error';
    public const LEVEL_WARNING = 'warning';
    public const LEVEL_NOTICE = 'notice';
    public const LEVEL_INFO = 'info';
    public const LEVEL_DEBUG = 'debug';

    /**
     * Get storage type
     */
    public static function getStorageTypeForChannel(string $channel): string {
        return self::LOG_STORAGE[$channel] ?? null; 
    } 

    /**
     * Get all available channels
     */
    public static function getChannels(): array {
        return [
            self::CHANNEL_APPLICATION,
            self::CHANNEL_SYSTEM,
            self::CHANNEL_GAME
        ];
    }

    /**
     * Check if channel is valid
     */
    public static function isValidChannel(string $channel): bool {
        return in_array(
            $channel,
            self::getChannels(),
            true
        );
    }

    /**
     * Get log file for filesystem channel
     */
    public static function getLogFile(string $channel): ?string {
        return self::LOG_FILES[$channel] ?? null; 
    }

    /**
     * Check if channel uses database persistence
     */
    public static function usesDatabase(string $channel): bool {
        return $channel === self::CHANNEL_GAME;
    }

    /**
     * Check if channel uses filesystem persistence
     */
    public static function usesFileSystem(string $channel): bool {
        return isset(self::LOG_FILES[$channel]); 
    }
}
