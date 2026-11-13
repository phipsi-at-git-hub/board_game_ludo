<?php
// src/Core/Dto/Logging/EntryFilterContext.php

namespace App\Core\Dto\Logging;

use App\Core\Logging\LoggingConfiguration;

/**
 * EntryFilterContext
 */
final readonly class EntryFilterContext
{
    public array $channels; 
    public array $available_channels; 

    public string $date_range; 
    public int $entries_count; 
    public array $statistics; 

    /**
     * __construct
     *
     * @param array $channels
     * @param string $date_range
     * @param array $available_channels
     * @param int $entries_count
     * @param array $statistics
     * @return void
     */
    public function __construct(
        array $channels, 
        array $available_channels, 
        string $date_range, 
        int $entries_count, 
        array $statistics, 
    )
    {
        $this->channels = $channels; 
        $this->available_channels = $available_channels; 

        $this->date_range = $date_range; 
        $this->entries_count = $entries_count; 
        $this->statistics = [
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
    } 

    /**
     * fromFilter
     * 
     * Returns an array for compatibility reasons. 
     *
     * @param array $channels
     * @param string $date_range
     * @param array $available_channels
     * @param int $entries_count
     * @param array $statistics
     * @return array
     */
    public static function fromFilter(
        array $channels, 
        string $date_range, 
        array $available_channels, 
        int $entries_count, 
        array $statistics, 
    ): array
    {
        $context = new self(
            channels: $channels, 
            available_channels: $available_channels, 
            date_range: $date_range, 
            entries_count: $entries_count, 
            statistics: $statistics, 
        ); 

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
            'channels' => $this->channels, 
            'available_channels' => $this->available_channels, 

            'date_range' => $this->date_range, 

            'entries_count' => $this->entries_count, 

            'statistics' => $this->statistics, 
        ]; 
    }
}
