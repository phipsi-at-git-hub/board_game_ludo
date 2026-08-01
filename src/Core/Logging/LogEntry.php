<?php
// src/Core/Logging/LogEntry.php

namespace App\Core\Logging;

final class LogEntry {
    private string $level;
    private string $message;
    private array $context;

    private string $channel;

    private string $timestamp;

    private ?string $request_id;
    private ?string $request_method;
    private ?string $request_uri;

    private ?string $client_ip;
    private ?string $runtime;

    private ?string $class;
    private ?string $method;
    private ?string $file;
    private ?int $line;

    private ?string $session_id;
    private ?string $user_id;

    private function __construct() {}

    /**
     * Create new log entry
     */
    public static function create(
        string $level,
        string $message,
        array $context = [],
        string $channel = LoggingConfiguration::CHANNEL_APPLICATION
    ): self {
        $entry = new self();

        $entry->level = $level;
        $entry->message = $message;
        $entry->context = $context;
        $entry->channel = $channel;

        $entry->timestamp = date('Y-m-d H:i:s');

        $entry->request_id = $_SERVER['HTTP_X_REQUEST_ID'] ?? null;
        $entry->request_method = $_SERVER['REQUEST_METHOD'] ?? null;
        $entry->request_uri = $_SERVER['REQUEST_URI'] ?? null;

        $entry->client_ip = $_SERVER['REMOTE_ADDR'] ?? null;

        $entry->runtime = PHP_SAPI === 'cli'
            ? 'cli'
            : 'web';

        $entry->session_id = session_id() ?: null;
        $entry->user_id = $_SESSION['user_id'] ?? null;

        $entry->resolveCaller();

        return $entry;
    }

    /**
     * Create LogEntry from array
     */
    public static function fromArray(array $data): self {
        $entry = new self();

        $entry->level = $data['level'];
        $entry->message = $data['message'];
        $entry->context = $data['context'] ?? [];

        $entry->channel = $data['channel'];

        $entry->timestamp = $data['timestamp'];

        $entry->request_id = $data['request_id'] ?? null;
        $entry->request_method = $data['request_method'] ?? null;
        $entry->request_uri = $data['request_uri'] ?? null;

        $entry->client_ip = $data['client_ip'] ?? null;
        $entry->runtime = $data['runtime'] ?? null;

        $entry->class = $data['class'] ?? null;
        $entry->method = $data['method'] ?? null;
        $entry->file = $data['file'] ?? null;
        $entry->line = isset($data['line'])
            ? (int)$data['line']
            : null;

        $entry->session_id = $data['session_id'] ?? null;
        $entry->user_id = $data['user_id'] ?? null;

        return $entry;
    }

    /**
     * Create LogEntry from JSON string
     */
    public static function fromString(string $json): self {
        return self::fromArray(
            json_decode(
                $json,
                true,
                512,
                JSON_THROW_ON_ERROR
            )
        );
    }

    /**
     * Resolve the caller of the Logger
     */
    private function resolveCaller(): void {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach ($trace as $index => $frame) {
            $class = $frame['class'] ?? null;
            $function = $frame['function'] ?? null;

            // Leave logger stack 
            if ($class === Logger::class && $function === 'log') {
                $caller = $trace[$index + 1] ?? null;
                if ($caller === null) {
                    return;
                }
                
                $this->class = $caller['class'] ?? null;
                $this->method = $caller['function'] ?? null;

                $file = $caller['file'] ?? null;
                if ($file !== null && defined('BASE_PATH')) {
                    $file = str_replace(BASE_PATH . DIRECTORY_SEPARATOR, '', $file); 
                }

                $this->file = str_replace(DIRECTORY_SEPARATOR, '/', $file);
                $this->line = $caller['line'] ?? null;
                return;
            }
        }
    }

    /**
     * JSON representation for log file
     */
    public function toString(): string {
        return json_encode(
            $this->toArray(),
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_THROW_ON_ERROR
        );
    }

    /**
     * Array representation
     */
    public function toArray(): array {
        return [
            'timestamp' => $this->timestamp,

            'level' => $this->level,
            'channel' => $this->channel,

            'message' => $this->message,
            'context' => $this->context,

            'user_id' => $this->user_id,
            'session_id' => $this->session_id,

            'request_id' => $this->request_id,
            'request_method' => $this->request_method,
            'request_uri' => $this->request_uri,

            'client_ip' => $this->client_ip,
            'runtime' => $this->runtime,

            'class' => $this->class,
            'method' => $this->method,
            'file' => $this->file,
            'line' => $this->line
        ];
    }

    public function getLevel(): string {
        return $this->level;
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function getContext(): array {
        return $this->context;
    }

    public function getChannel(): string {
        return $this->channel;
    }

    public function getTimestamp(): string {
        return $this->timestamp;
    }

    public function getRequestId(): ?string {
        return $this->request_id;
    }

    public function getRequestMethod(): ?string {
        return $this->request_method;
    }

    public function getRequestUri(): ?string {
        return $this->request_uri;
    }

    public function getClientIp(): ?string {
        return $this->client_ip;
    }

    public function getRuntime(): ?string {
        return $this->runtime;
    }

    public function getClass(): ?string {
        return $this->class;
    }

    public function getMethod(): ?string {
        return $this->method;
    }

    public function getFile(): ?string {
        return $this->file;
    }

    public function getLine(): ?int {
        return $this->line;
    }

    public function getSessionId(): ?string {
        return $this->session_id;
    }

    public function getUserId(): ?string {
        return $this->user_id;
    }
}
