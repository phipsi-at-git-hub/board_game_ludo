<?php
// src/Core/Email/Mailer.php

namespace App\Core\Email;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

final class Mailer {
    private EmailConfiguration $configuration;

    public function __construct(?EmailConfiguration $configuration = null) {
        $this->configuration = $configuration ?? new EmailConfiguration();
    }

    /**
     * Send email
     */
    public function send(Email $email): bool {
        if (!$this->configuration->isValid()) {
            return false;
        }

        try {
            $mailer = new PHPMailer(true);

            $mailer->isSMTP();

            $mailer->Host = $this->configuration->getHost();
            $mailer->Port = $this->configuration->getPort();

            $mailer->SMTPAuth = ($this->configuration->getUsername() !== '' && $this->configuration->getPassword() !== '');
            $mailer->Username = $this->configuration->getUsername();
            $mailer->Password = $this->configuration->getPassword();

            switch ($this->configuration->getEncryption()) {
                case 'ssl':
                    $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    break;

                case 'tls':
                    $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    break;

                case 'none':
                    $mailer->SMTPSecure = '';
                    $mailer->SMTPAutoTLS = false;
                    break;
            }

            $mailer->CharSet = 'UTF-8';

            $mailer->setFrom(
                $this->configuration->getFromAddress(),
                $this->configuration->getFromName()
            );

            $mailer->addAddress(
                $email->getTo(),
                $email->getToName() ?? ''
            );

            $mailer->Subject = $email->getSubject();
            $mailer->isHTML($email->hasHtmlBody());

            if ($email->hasHtmlBody()) {
                $mailer->Body = $email->getHtmlBody();
                $mailer->AltBody = $email->getBody();
            } else {
                $mailer->Body = $email->getBody();
            }

            return $mailer->send();

        } catch (Exception) {
            return false;
        }
    }
}
