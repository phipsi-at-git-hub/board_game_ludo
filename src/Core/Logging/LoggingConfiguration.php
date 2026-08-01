<?php
// src/Core/Logging/LoggingConfiguration.php

namespace App\Core\Logging;

use App\Constants\Application;
use App\Core\Application\App;

final class LoggingConfiguration {
    /**
     * Channels
     */
    public const CHANNEL_APPLICATION = 'application';
    public const CHANNEL_SYSTEM = 'system';
    public const CHANNEL_GAME = 'game';

    /**
     * Log levels
     */
    public const LEVEL_EMERGENCY = 'emergency';
    public const LEVEL_ALERT     = 'alert';
    public const LEVEL_CRITICAL  = 'critical';
    public const LEVEL_ERROR     = 'error';
    public const LEVEL_WARNING   = 'warning';
    public const LEVEL_NOTICE    = 'notice';
    public const LEVEL_INFO      = 'info';
    public const LEVEL_DEBUG     = 'debug';

    /**
     * Channel configuration
     */
    private const CHANNELS = [
        self::CHANNEL_APPLICATION => [
            'storage'        => Application::STORAGE_FILE,
            'file'           => 'application',
            'minimum_level'  => self::LEVEL_INFO,
            'retention_days' => 30,
        ],

        self::CHANNEL_SYSTEM => [
            'storage'        => Application::STORAGE_FILE,
            'file'           => 'system',
            'minimum_level'  => self::LEVEL_WARNING,
            'retention_days' => 90,
        ],

        self::CHANNEL_GAME => [
            'storage' => Application::STORAGE_DATABASE,
        ]
    ];

    /**
     * Returns all configured channels
     */
    public static function getChannels(): array {
        return array_keys(self::CHANNELS);
    }

    /**
     * Checks whether a channel exists
     */
    public static function isValidChannel(string $channel): bool {
        return isset(self::CHANNELS[$channel]);
    }

    /**
     * Returns storage type
     */
    public static function getStorageTypeForChannel(string $channel): ?string {
        return self::CHANNELS[$channel]['storage'] ?? null;
    }

    /**
     * Uses filesystem?
     */
    public static function usesFileSystem(string $channel): bool {
        return self::getStorageTypeForChannel($channel) === 'file';
    }

    /**
     * Uses database?
     */
    public static function usesDatabase(string $channel): bool {
        return self::getStorageTypeForChannel($channel) === 'database';
    }


    /**
     * Returns today's logfile
     */
    public static function getLogFile(string $channel): ?string {
        if (!self::usesFileSystem($channel)) {
            return null;
        }

        return sprintf(
            '%s/%s-%s.log',
            LOG_PATH,
            self::CHANNELS[$channel]['file'],
            date(Application::FILE_DATE_FORMAT)
        );
    }

    /**
     * Returns configured retention time
     */
    public static function getRetentionDays(string $channel): int {
        return self::CHANNELS[$channel]['retention_days'] ?? 0;
    }

    /**
     * Returns configured minimum log level
     */
    public static function getMinimumLevel(string $channel): ?string {
        return self::CHANNELS[$channel]['minimum_level'] ?? null;
    }
}
