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
        return self::createFileLog($entry);
    }

    /**
     * Save application/system logs into filesystem
     */
    private static function createFileLog(LogEntry $entry): bool {
        $fileSystem = FileSystem::getInstance();
        $filename = LoggingConfiguration::getLogFile(
            $entry->getChannel()
        );
        if ($filename === null) {
            return false;
        }

        $fileSystem->append(
            $filename,
            $entry->toString() . PHP_EOL
        );
        return true;
    }
}
