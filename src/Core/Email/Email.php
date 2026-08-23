<?php
// src/Core/Email/Email.php

namespace App\Core\Email;

final class Email {
    private string $to;
    private string $subject;
    private string $body;

    private ?string $toName;
    private ?string $htmlBody;

    private function __construct() {}

    /**
     * Create email
     */
    public static function create(
        string $to,
        string $subject,
        string $body,
        ?string $toName = null,
        ?string $htmlBody = null
    ): self {
        $email = new self();

        $email->to = $to;
        $email->subject = $subject;
        $email->body = $body;
        $email->toName = $toName;
        $email->htmlBody = $htmlBody;

        return $email;
    }

    /**
     * Get recipient address
     */
    public function getTo(): string {
        return $this->to;
    }

    /**
     * Get recipient name
     */
    public function getToName(): ?string {
        return $this->toName;
    }

    /**
     * Get subject
     */
    public function getSubject(): string {
        return $this->subject;
    }

    /**
     * Get plain text body
     */
    public function getBody(): string {
        return $this->body;
    }

    /**
     * Get HTML body
     */
    public function getHtmlBody(): ?string {
        return $this->htmlBody;
    }

    /**
     * Has HTML body
     */
    public function hasHtmlBody(): bool {
        return $this->htmlBody !== null;
    }
}
 