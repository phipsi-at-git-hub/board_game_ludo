<?php
// src/Core/Logging/Logger.php

namespace App\Core\Logging;

use App\Core\Application\App;
use App\Models\System\LoggingModel;
use App\Services\SystemService;
use Throwable;

final class Logger {
    private string $channel;

    private function __construct(string $channel) {
        $this->channel = $channel;
    }

    // Create Application Logger 
    public static function app(): self { 
        return new self(LoggingConfiguration::CHANNEL_APPLICATION); 
    }

    // Create System Logger 
    public static function system(): self { 
        return new self(LoggingConfiguration::CHANNEL_SYSTEM); 
    }

    // Create Game Logger 
    public static function game(): self { 
        return new self(LoggingConfiguration::CHANNEL_GAME); 
    }

    // Log emergency message 
    public function emergency(string $message, array $context = []): void {
        $this->log('emergency', $message, $context);
    }

    // Log alert message 
    public function alert(string $message, array $context = []): void {
        $this->log('alert', $message, $context);
    }

    // Log critical message 
    public function critical(string $message, array $context = []): void {
        $this->log('critical', $message, $context);
    }

    // Log error message 
    public function error(string $message, array $context = []): void {
        $this->log('error', $message, $context);
    }

    // Log warning message 
    public function warning(string $message, array $context = []): void {
        $this->log('warning', $message, $context);
    }

    // Log notice message 
    public function notice(string $message, array $context = []): void {
        $this->log('notice', $message, $context);
    }

    // Log info message 
    public function info(string $message, array $context = []): void {
        $this->log('info', $message, $context);
    }

    // Log debug message 
    public function debug(string $message, array $context = []): void {
        $this->log('debug', $message, context: $context);
    }

    // Create LogEntry and persist it 
    private function log(
        string $level,
        string $message,
        array $context = []
    ): void {
        if ($level === LoggingConfiguration::LEVEL_DEBUG && !App::instance()->resolve(SystemService::class)->isLoggingDebugEnabled()) {
            return; 
        }

        $entry = LogEntry::create(
            $level,
            $message,
            $context,
            $this->channel
        );

        try {
            LoggingModel::create($entry); 
        }  catch (Throwable $e) {
            error_log('Logging failed: ' . $e->getMessage()); 
        } 
    }
}
