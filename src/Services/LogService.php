<?php
// src/Services/LogService.php

namespace App\Services;

use App\Constants\Application;
use App\Constants\DateTimeFormatConstants;
use App\Constants\FileSystem\FSFieldConstants;
use App\Constants\SortingConstants;
use App\Core\Date\DateRange;
use App\Core\Logging\LogEntry;
use App\Core\Logging\LoggingConfiguration;
use App\Models\System\LoggingModel;
use DateTimeImmutable;

final class LogService
{
    private array $channels;
    private DateRange $dateRange;
    private array $logLevels = [];
    private array $entries = [];

    /**
     * __construct
     *
     * @param  mixed $channels
     * @param  mixed $dates
     * @param  mixed $log_levels
     * @return void
     */
    public function __construct(array $channels, array $dates, array $log_levels = [])
    {
        $this->channels = $channels;
        $this->dateRange = new DateRange($dates);
        $this->logLevels = $log_levels;

        $this->load();
    }

    /**
     * Load logs from configured channels and dates
     * 
     * load
     *
     * @return void
     */
    private function load(): void
    {
        if ($this->dateRange->isEmpty()) {
            return;
        }
        foreach ($this->channels as $channel) {
            foreach ($this->dateRange->getDays() as $date) {
                $entries = LoggingModel::find($channel, $date->format(DateTimeFormatConstants::FILE_DATE));
                foreach ($entries as $entry) {
                    if (!$this->dateRange->contains(new DateTimeImmutable($entry->getTimestamp()))) {
                        continue;
                    }
                    if (!empty($this->logLevels) && !in_array($entry->getLevel(), $this->logLevels, true)) {
                        continue;
                    }
                    $this->entries[] = $entry;
                }
            }
        }
    }

    /**
     * Get loaded log entries
     * 
     * getEntries
     *
     * @return array
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * Get log entry with UUID
     * 
     * getEntryById
     *
     * @param  mixed $id
     * @return LogEntry
     */
    public function getEntryById(string $id): ?LogEntry
    {
        foreach ($this->entries as $entry) {
            if ($entry->getId() === $id) {
                return $entry;
            }
        }
        return null;
    }

    /**
     * Order loaded log entries by field and direction
     * 
     * orderBy
     *
     * @param  mixed $field
     * @param  mixed $direction
     * @return self
     */
    public function orderBy(string $field = FSFieldConstants::TIMESTAMP, string $direction = SortingConstants::ASC): self
    {
        if (!in_array($field, [FSFieldConstants::TIMESTAMP, FSFieldConstants::CHANNEL, FSFieldConstants::LOG_LEVEL], true)) {
            $field = FSFieldConstants::TIMESTAMP;
        }

        if (!in_array($direction, [SortingConstants::ASC, SortingConstants::DESC], true)) {
            $direction = SortingConstants::ASC;
        }

        usort($this->entries, function ($a, $b) use ($field, $direction) {
            switch ($field) {
                case FSFieldConstants::CHANNEL:
                    $comparison = $a->getChannel() <=> $b->getChannel();
                    break;

                case FSFieldConstants::LOG_LEVEL:
                    $comparison = $a->getLevel() <=> $b->getLevel();
                    break;

                case FSFieldConstants::TIMESTAMP:
                default:
                    $comparison = new DateTimeImmutable($a->getTimestamp()) <=> new DateTimeImmutable($b->getTimestamp());
                    break;
            }
            return $direction === SortingConstants::DESC ? -$comparison : $comparison;
        });
        return $this;
    }

    /**
     * Get date range of the log entries as a formatted string
     * 
     * getDateRangeAsString
     *
     * @return String
     */
    public function getDateRangeAsString(): String
    {
        return $this->dateRange->getDateRangeAsString();
    }

    /**
     * Get all available log channels
     */
    public function getChannels(): array
    {
        return LoggingConfiguration::getChannels();
    }

    /**
     * Get selected log levels
     * 
     * getLogLevels
     *
     * @return array
     */
    public function getLogLevels(): array
    {
        return $this->logLevels;
    }

    /**
     * Get all available log levels
     * 
     * getLevels
     *
     * @return array
     */
    public function getLevels(): array
    {
        return LoggingConfiguration::getLevels();
    }

    /**
     * Get total number of loaded logs
     * 
     * getCount
     *
     * @return int
     */
    public function getCount(): int
    {
        return count($this->entries);
    }

    /**
     * Get count by level
     * 
     * getCountByLevel
     *
     * @param  mixed $level
     * @return int
     */
    public function getCountByLevel(string $level): int
    {
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
     * 
     * getStatistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
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
     * 
     * getHighestLevel
     *
     * @param  mixed $statistics
     * @return string
     */
    public function getHighestLevel(array $statistics): ?string
    {
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
     * 
     * hasCriticalLogs
     *
     * @return bool
     */
    public function hasCriticalLogs(): bool
    {
        return (
            $this->getCountByLevel(LoggingConfiguration::LEVEL_EMERGENCY)
            + $this->getCountByLevel(LoggingConfiguration::LEVEL_ALERT)
            + $this->getCountByLevel(LoggingConfiguration::LEVEL_CRITICAL)
        ) > 0;
    }

    /**
     * Check if errors exist
     * 
     * hasErrors
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return $this->getCountByLevel(
            LoggingConfiguration::LEVEL_ERROR
        ) > 0;
    }

    /**
     * Get latest entries
     * 
     * getLatest
     *
     * @param  mixed $limit
     * @return array
     */
    public function getLatest(int $limit = 100): array
    {
        return array_slice(
            array_reverse($this->entries),
            0,
            $limit
        );
    }

    /**
     * Get dashboard overview
     * 
     * getDashboardStatistics
     *
     * @return array
     */
    public function getDashboardStatistics(): array
    {
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
