<?php
// src/Services/LogService.php

namespace App\Services;

use App\Constants\Application;
use App\Core\Date\DateRange;
use App\Core\Logging\LoggingConfiguration;
use App\Models\LoggingModel;
use DateTimeImmutable;

final class LogService {
    private array $channels;
    private DateRange $dateRange; 
    private array $entries = [];

    public function __construct(array $channels, array $dates) {
        $this->channels = $channels;
        $this->dateRange = new DateRange($dates); 

        $this->load();
    }

    /**
     * Load logs from configured channels and dates
     */
    private function load(): void {
        if ($this->dateRange->isEmpty()) {
            return; 
        } 
        foreach ($this->channels as $channel) {
            foreach ($this->dateRange->getDays() as $date) {
                $entries = LoggingModel::find($channel, $date->format(Application::FILE_DATE_FORMAT)); 
                foreach ($entries as $entry) {
                    if ($this->dateRange->contains(new DateTimeImmutable($entry->getTimestamp()))) {
                        $this->entries[] = $entry; 
                    }
                }
            }
        }
    }

    /**
     * Get loaded log entries
     */
    public function getEntries(): array {
        return $this->entries;
    }

    /**
     * Get date range of the log entries as a formatted string
     */
    public function getDateRangeAsString(): String {
        return $this->dateRange->getDateRangeAsString(); 
    }

    /**
     * Get all available log channels
     */
    public function getChannels(): array {
        return LoggingConfiguration::getChannels();
    }

    /**
     * Get all available log levels
     */
    public function getLevels(): array {
        return LoggingConfiguration::getLevels();
    }

    /**
     * Get total number of loaded logs
     */
    public function getCount(): int {
        return count($this->entries);
    }

    /**
     * Get count by level
     */
    public function getCountByLevel(string $level): int {
        $count = 0;

        foreach ($this->entries as $entry) {
            if ($entry->getLevel() === $level) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array {
        $statistics = [];

        foreach (LoggingConfiguration::getLevels() as $level) {
            $statistics[$level] = $this->getCountByLevel($level);
        }

        $statistics['total'] = $this->getCount();
        $statistics['highest_level'] = $this->getHighestLevel($statistics) ?? 'none';

        return $statistics;
    }

    /**
     * Get highest occurring severity
     */
    public function getHighestLevel(array $statistics): ?string {
        foreach (
            [
                LoggingConfiguration::LEVEL_EMERGENCY,
                LoggingConfiguration::LEVEL_ALERT,
                LoggingConfiguration::LEVEL_CRITICAL,
                LoggingConfiguration::LEVEL_ERROR,
                LoggingConfiguration::LEVEL_WARNING,
                LoggingConfiguration::LEVEL_NOTICE,
                LoggingConfiguration::LEVEL_INFO,
                LoggingConfiguration::LEVEL_DEBUG
            ] as $level
        ) {
            if (($statistics[$level] ?? 0) > 0) {
                return $level;
            }
        }
        return null;
    }

    /**
     * Check if critical logs exist
     */
    public function hasCriticalLogs(): bool {
        return (
            $this->getCountByLevel(LoggingConfiguration::LEVEL_EMERGENCY)
            + $this->getCountByLevel(LoggingConfiguration::LEVEL_ALERT)
            + $this->getCountByLevel(LoggingConfiguration::LEVEL_CRITICAL)
        ) > 0;
    }

    /**
     * Check if errors exist
     */
    public function hasErrors(): bool {
        return $this->getCountByLevel(
            LoggingConfiguration::LEVEL_ERROR
        ) > 0;
    }

    /**
     * Get latest entries
     */
    public function getLatest(int $limit = 100): array {
        return array_slice(
            array_reverse($this->entries),
            0,
            $limit
        );
    }

    /**
     * Get dashboard overview
     */
    public function getDashboardStatistics(): array {
        $statistics = $this->getStatistics();

        return [
            'total' => $statistics['total'],
            'emergency' => $statistics[LoggingConfiguration::LEVEL_EMERGENCY],
            'alert' => $statistics[LoggingConfiguration::LEVEL_ALERT],
            'critical' => $statistics[LoggingConfiguration::LEVEL_CRITICAL],
            'error' => $statistics[LoggingConfiguration::LEVEL_ERROR],
            'warning' => $statistics[LoggingConfiguration::LEVEL_WARNING],
            'highest_level' => $statistics['highest_level']
        ];
    }
}
