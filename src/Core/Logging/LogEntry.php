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
    private ?string $session_id;
    private ?string $user_id;

    private function __construct(
        string $level,
        string $message,
        array $context,
        string $channel
    ) {
        $this->level = $level;
        $this->message = $message;
        $this->context = $context;

        $this->channel = $channel;

        $this->timestamp = date('Y-m-d H:i:s');

        $this->request_id = $_SERVER['HTTP_X_REQUEST_ID'] ?? null;

        $this->session_id = session_id() ?: null;

        $this->user_id = $_SESSION['user_id'] ?? null;
    }

    public static function create(
        string $level,
        string $message,
        array $context,
        string $channel
    ): self {
        return new self(
            $level,
            $message,
            $context,
            $channel
        );
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

    public function getSessionId(): ?string {
        return $this->session_id;
    }

    public function getUserId(): ?string {
        return $this->user_id;
    }

    /**
     * Format for filesystem logs
     */
    public function toString(): string {
        return sprintf(
            '[%s] %s: %s %s',
            $this->timestamp,
            strtoupper($this->level),
            $this->message,
            json_encode($this->context)
        );
    }

    /**
     * Format for database persistence
     */
    public function toArray(): array {
        return [
            'level' => $this->level,
            'message' => $this->message,
            'context' => json_encode($this->context),
            'channel' => $this->channel,
            'timestamp' => $this->timestamp,
            'request_id' => $this->request_id,
            'session_id' => $this->session_id,
            'user_id' => $this->user_id
        ];
    }
}
