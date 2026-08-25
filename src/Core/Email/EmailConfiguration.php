<?php
// src/Core/Email/EmailConfiguration.php

namespace App\Core\Email;

final class EmailConfiguration {
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $encryption;

    private string $fromAddress;
    private string $fromName;

    public function __construct() {
        $this->host = $_ENV['MAIL_HOST'] ?? '';
        $this->port = (int)($_ENV['MAIL_PORT'] ?? 587);
        $this->username = $_ENV['MAIL_USERNAME'] ?? '';
        $this->password = $_ENV['MAIL_PASSWORD'] ?? '';
        $this->encryption = strtolower($_ENV['MAIL_ENCRYPTION'] ?? 'tls');

        $this->fromAddress = $_ENV['MAIL_FROM_ADDRESS'] ?? '';
        $this->fromName = $_ENV['MAIL_FROM_NAME'] ?? '';
    }

    /**
     * Check whether configuration is valid
     */
    public function isValid(): bool {
        return (
            $this->host !== ''
            && $this->port > 0
            && $this->fromAddress !== ''
            && filter_var($this->fromAddress, FILTER_VALIDATE_EMAIL) !== false
            && in_array($this->encryption, ['tls', 'ssl', 'none'], true)
        );
    }

    /**
     * Get SMTP host
     */
    public function getHost(): string {
        return $this->host;
    }

    /**
     * Get SMTP port
     */
    public function getPort(): int {
        return $this->port;
    }

    /**
     * Get SMTP username
     */
    public function getUsername(): string {
        return $this->username;
    }

    /**
     * Get SMTP password
     */
    public function getPassword(): string {
        return $this->password;
    }

    /**
     * Get SMTP encryption
     */
    public function getEncryption(): string {
        return $this->encryption;
    }

    /**
     * Get sender address
     */
    public function getFromAddress(): string {
        return $this->fromAddress;
    }

    /**
     * Get sender name
     */
    public function getFromName(): string {
        return $this->fromName;
    }
}
