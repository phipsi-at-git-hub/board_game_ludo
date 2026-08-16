<?php
// src/Core/Dto/Logging/EntryFilterContext.php

namespace App\Core\Dto\Logging;

use App\Core\Logging\LoggingConfiguration;

final class EntryFilterContext {
    private static function create(): array {
        return [
            // Filter
            'channels' => [],
            'date_range' => null,

            // Available filter options
            'available_channels' => [],

            // Filtered entries
            'entries_count' => 0,

            // Filtered entry statistics 
            'statistics' => [
                LoggingConfiguration::LEVEL_EMERGENCY . '_label' => 0, 
                LoggingConfiguration::LEVEL_ALERT . '_label' => 0, 
                LoggingConfiguration::LEVEL_CRITICAL . '_label' => 0, 
                LoggingConfiguration::LEVEL_ERROR . '_label' => 0, 
                LoggingConfiguration::LEVEL_WARNING . '_label' => 0, 
                LoggingConfiguration::LEVEL_NOTICE . '_label' => 0, 
                LoggingConfiguration::LEVEL_INFO . '_label' => 0, 
                LoggingConfiguration::LEVEL_DEBUG . '_label' => 0, 

                LoggingConfiguration::LEVEL_EMERGENCY . '_classes' => 'status-default', 
                LoggingConfiguration::LEVEL_ALERT . '_classes' => 'status-default', 
                LoggingConfiguration::LEVEL_CRITICAL . '_classes' => 'status-default', 
                LoggingConfiguration::LEVEL_ERROR . '_classes' => 'status-default', 
                LoggingConfiguration::LEVEL_WARNING . '_classes' => 'status-default', 
                LoggingConfiguration::LEVEL_NOTICE . '_classes' => 'status-default', 
                LoggingConfiguration::LEVEL_INFO . '_classes' => 'status-default', 
                LoggingConfiguration::LEVEL_DEBUG . '_classes' => 'status-default', 

                LoggingConfiguration::LEVEL_EMERGENCY . '_disabled' => true, 
                LoggingConfiguration::LEVEL_ALERT . '_disabled' => true, 
                LoggingConfiguration::LEVEL_CRITICAL . '_disabled' => true, 
                LoggingConfiguration::LEVEL_ERROR . '_disabled' => true, 
                LoggingConfiguration::LEVEL_WARNING . '_disabled' => true, 
                LoggingConfiguration::LEVEL_NOTICE . '_disabled' => true, 
                LoggingConfiguration::LEVEL_INFO . '_disabled' => true, 
                LoggingConfiguration::LEVEL_DEBUG . '_disabled' => true, 
            ], 
        ];
    }


    public static function fromFilter(array $channels, String $date_range, array $available_channels,  int $entries_count, array $statistics): array {
        $dto = self::create();

        // Filter
        $dto['channels'] = $channels;
        $dto['date_range'] = $date_range;

        // Available filter options
        $dto['available_channels'] = $available_channels;

        // Filtered entries count
        $dto['entries_count'] = $entries_count;

        // Filtered entry statistics 
        $dto['statistics'] = [
            LoggingConfiguration::LEVEL_EMERGENCY . '_label' => $statistics[LoggingConfiguration::LEVEL_EMERGENCY] ?? 0, 
            LoggingConfiguration::LEVEL_ALERT . '_label' => $statistics[LoggingConfiguration::LEVEL_ALERT] ?? 0, 
            LoggingConfiguration::LEVEL_CRITICAL . '_label' => $statistics[LoggingConfiguration::LEVEL_CRITICAL] ?? 0, 
            LoggingConfiguration::LEVEL_ERROR . '_label' => $statistics[LoggingConfiguration::LEVEL_ERROR] ?? 0, 
            LoggingConfiguration::LEVEL_WARNING . '_label' => $statistics[LoggingConfiguration::LEVEL_WARNING] ?? 0, 
            LoggingConfiguration::LEVEL_NOTICE . '_label' => $statistics[LoggingConfiguration::LEVEL_NOTICE] ?? 0, 
            LoggingConfiguration::LEVEL_INFO . '_label' => $statistics[LoggingConfiguration::LEVEL_INFO] ?? 0, 
            LoggingConfiguration::LEVEL_DEBUG . '_label' => $statistics[LoggingConfiguration::LEVEL_DEBUG] ?? 0, 

            LoggingConfiguration::LEVEL_EMERGENCY . '_classes' => ($statistics[LoggingConfiguration::LEVEL_EMERGENCY] && $statistics[LoggingConfiguration::LEVEL_EMERGENCY] > 0) ? 'level-' . LoggingConfiguration::LEVEL_EMERGENCY : 'status-default', 
            LoggingConfiguration::LEVEL_ALERT . '_classes' => ($statistics[LoggingConfiguration::LEVEL_ALERT] && $statistics[LoggingConfiguration::LEVEL_ALERT] > 0) ? 'level-' . LoggingConfiguration::LEVEL_ALERT : 'status-default', 
            LoggingConfiguration::LEVEL_CRITICAL . '_classes' => ($statistics[LoggingConfiguration::LEVEL_CRITICAL] && $statistics[LoggingConfiguration::LEVEL_CRITICAL] > 0) ? 'level-' . LoggingConfiguration::LEVEL_CRITICAL : 'status-default', 
            LoggingConfiguration::LEVEL_ERROR . '_classes' => ($statistics[LoggingConfiguration::LEVEL_ERROR] && $statistics[LoggingConfiguration::LEVEL_ERROR] > 0) ? 'level-' . LoggingConfiguration::LEVEL_ERROR : 'status-default', 
            LoggingConfiguration::LEVEL_WARNING . '_classes' => ($statistics[LoggingConfiguration::LEVEL_WARNING] && $statistics[LoggingConfiguration::LEVEL_WARNING] > 0) ? 'level-' . LoggingConfiguration::LEVEL_WARNING : 'status-default', 
            LoggingConfiguration::LEVEL_NOTICE . '_classes' => ($statistics[LoggingConfiguration::LEVEL_NOTICE] && $statistics[LoggingConfiguration::LEVEL_NOTICE] > 0) ? 'level-' . LoggingConfiguration::LEVEL_NOTICE : 'status-default', 
            LoggingConfiguration::LEVEL_INFO . '_classes' => ($statistics[LoggingConfiguration::LEVEL_INFO] && $statistics[LoggingConfiguration::LEVEL_INFO] > 0) ? 'level-' . LoggingConfiguration::LEVEL_INFO : 'status-default', 
            LoggingConfiguration::LEVEL_DEBUG . '_classes' => ($statistics[LoggingConfiguration::LEVEL_DEBUG] && $statistics[LoggingConfiguration::LEVEL_DEBUG] > 0) ? 'level-' . LoggingConfiguration::LEVEL_DEBUG : 'status-default', 

            LoggingConfiguration::LEVEL_EMERGENCY . '_disabled' => ($statistics[LoggingConfiguration::LEVEL_EMERGENCY] && $statistics[LoggingConfiguration::LEVEL_EMERGENCY] > 0) ? false : true, 
            LoggingConfiguration::LEVEL_ALERT . '_disabled' => ($statistics[LoggingConfiguration::LEVEL_ALERT] && $statistics[LoggingConfiguration::LEVEL_ALERT] > 0) ? false : true, 
            LoggingConfiguration::LEVEL_CRITICAL . '_disabled' => ($statistics[LoggingConfiguration::LEVEL_CRITICAL] && $statistics[LoggingConfiguration::LEVEL_CRITICAL] > 0) ? false : true, 
            LoggingConfiguration::LEVEL_ERROR . '_disabled' => ($statistics[LoggingConfiguration::LEVEL_ERROR] && $statistics[LoggingConfiguration::LEVEL_ERROR] > 0) ? false : true, 
            LoggingConfiguration::LEVEL_WARNING . '_disabled' => ($statistics[LoggingConfiguration::LEVEL_WARNING] && $statistics[LoggingConfiguration::LEVEL_WARNING] > 0) ? false : true, 
            LoggingConfiguration::LEVEL_NOTICE . '_disabled' => ($statistics[LoggingConfiguration::LEVEL_NOTICE] && $statistics[LoggingConfiguration::LEVEL_NOTICE] > 0) ? false : true, 
            LoggingConfiguration::LEVEL_INFO . '_disabled' => ($statistics[LoggingConfiguration::LEVEL_INFO] && $statistics[LoggingConfiguration::LEVEL_INFO] > 0) ? false : true, 
            LoggingConfiguration::LEVEL_DEBUG . '_disabled' => ($statistics[LoggingConfiguration::LEVEL_DEBUG] && $statistics[LoggingConfiguration::LEVEL_DEBUG] > 0) ? false : true, 
        ]; 

        return $dto;
    }
}
