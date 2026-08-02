<?php
// src/Models/LoggingModel.php

namespace App\Models;

use App\Core\Logging\LogEntry;
use App\Core\Logging\LoggingConfiguration;
use App\Core\Persistence\FileSystem;

final class LoggingModel extends BaseModel {
    /**
     * Create application/system log
     */
    public static function create(LogEntry $entry): bool {
        $file = LoggingConfiguration::getLogFile($entry->getChannel());
        if ($file === null) {
            return false;
        }

        return FileSystem::getInstance()->appendRotated(
            $file, 
            $entry->toString() . PHP_EOL, 
            LoggingConfiguration::getMaxFileSize($entry->getChannel()) 
        );
    }

    /**
     * Read log file
     */
    public static function find(string $channel, string $date, int $start = 0, ?int $limit = null): array {
        $filename = LoggingConfiguration::getLogFileByDate($channel, $date);
        if ($filename === null) {
            return [];
        }

        $entries = [];
        $files = FileSystem::getInstance()->getRelatedRotatedFilenames($filename); 

        foreach (FileSystem::getInstance()->readLinesFromFiles($files, $start, $limit) as $line) {
            $entries[] = LogEntry::fromString($line);
        }
        return $entries;
    }

    /**
     * Find available log files
     */
    public static function findFiles(string $channel): array {
        if (!LoggingConfiguration::usesFileSystem($channel)) {
            return [];
        }

        $pattern = sprintf('%s-*.log', LoggingConfiguration::getLogFileName($channel));
        return FileSystem::getInstance()->listFiles(LOG_PATH, $pattern);
    }

    /**
     * Find only base files of available log files
     */
    public static function findBaseFiles(string $channel): array {
        if (!LoggingConfiguration::usesFileSystem($channel)) {
            return []; 
        } 

        $pattern = sprintf('%s-*.log', LoggingConfiguration::getLogFileName($channel)); 
        return FileSystem::getInstance()->listBaseFiles(LOG_PATH, $pattern); 
    }

    /**
     * Delete log file
     */
    public static function delete(string $channel, string $date): bool {
        $filename = LoggingConfiguration::getLogFileByDate($channel, $date);
        if ($filename === null) {
            return false;
        }

        $files = FileSystem::getInstance()->getRelatedRotatedFilenames($filename); 
        $success= true; 
        foreach($files as $file) {
            $success = FileSystem::getInstance()->delete($file) && $success; 
        } 
        
        return $success; 
    }

    /**
     * Check whether log file exists
     */
    public static function exists(string $channel, string $date): bool {
        $filename = LoggingConfiguration::getLogFileByDate($channel, $date);
        if ($filename === null) {
            return false;
        }
        return count(FileSystem::getInstance()->getRelatedRotatedFilenames($filename)) > 0; 
    }

    /**
     * Get log file size
     */
    public static function size(string $channel, string $date): ?int {
        $filename = LoggingConfiguration::getLogFileByDate($channel, $date);
        if ($filename === null) {
            return null;
        }

        $total = 0; 
        foreach (FileSystem::getInstance()->getRelatedRotatedFilenames($filename) as $file) {
            $total += FileSystem::getInstance()->size($file) ?? 0; 
        }
        return $total; 
    }

    /**
     * Get log file modification time
     */
    public static function lastModified(string $channel, string $date): ?int {
        $filename = LoggingConfiguration::getLogFileByDate($channel, $date);
        if ($filename === null) {
            return null;
        }

        $lastModified = null; 
        foreach (FileSystem::getInstance()->getRelatedRotatedFilenames($filename) as $file) {
            $timestamp = FileSystem::getInstance()->lastModified($file); 

            if ($timestamp !== null && ($lastModified === null || $timestamp > $lastModified)) {
                $lastModified = $timestamp; 
            }
        }
        return $lastModified; 
    }
}
