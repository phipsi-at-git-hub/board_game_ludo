<?php
// src/Models/LoggingModel.php

namespace App\Models;

use App\Core\Logging\LogEntry;
use App\Core\Logging\LoggingConfiguration;
use App\Core\Persistence\FileSystem;

final class LoggingModel extends BaseModel {
    // Create log entry 
    public static function create(LogEntry $entry): bool {
        switch ($entry->getChannel()) {
            case 'game':
                return self::createGameLog($entry);

            case 'application':
                return self::createFileLog($entry);

            default:
                return false;
        }
    }

    // Save game logs into database 
    private static function createGameLog(LogEntry $entry): bool {
        return static::execute(
            "
            INSERT INTO game_logs
            (
                level,
                message,
                context,
                request_id,
                session_id,
                user_id,
                created_at
            )
            VALUES
            (
                :level,
                :message,
                :context,
                :request_id,
                :session_id,
                :user_id,
                :created_at
            )
            ",
            [
                'level' => $entry->getLevel(),
                'message' => $entry->getMessage(),
                'context' => json_encode($entry->getContext()),
                'request_id' => $entry->getRequestId(),
                'session_id' => $entry->getSessionId(),
                'user_id' => $entry->getUserId(),
                'created_at' => $entry->getTimestamp()
            ]
        );
    }

    // Save application/system logs into filesystem 
    private static function createFileLog(LogEntry $entry): bool {
        $fileSystem = FileSystem::getInstance(); 
        $filename = LoggingConfiguration::getLogFile($entry->getChannel()); 
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
