<?php
// src/Services/MailService.php

namespace App\Services;

use App\Constants\Application;
use App\Core\Email\Email;
use App\Core\Email\Mailer;
use App\Core\Logging\LoggingConfiguration;
use App\Core\Localization;
use App\Models\UserModel;
use DateTimeInterface;

final class MailService {
    private Mailer $mailer;

    public function __construct(?Mailer $mailer = null) {
        $this->mailer = $mailer ?? new Mailer();
    }

    /**
     * Send email to user
     */
    public function sendToUser(UserModel $user, string $subject, string $body, ?string $htmlBody = null): bool {
        $email = Email::create(
            $user->getEmail(),
            $subject,
            $body,
            $user->getUsername(),
            $htmlBody
        );

        return $this->mailer->send($email);
    }

    /**
     * Send email to all system administrators
     */
    public function sendToAdministrators(string $subject, string $body, ?string $htmlBody = null): bool {
        $administrators = UserModel::findByRole(Application::ADMIN);
        $success = true;

        foreach ($administrators as $administrator) {
            if (!$this->sendToUser(
                $administrator,
                $subject,
                $body,
                $htmlBody
            )) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Send registration email
     */
    public function sendRegistration(UserModel $user, string $verificationUrl): bool {
        $subject = Localization::get('mail.registration.subject');

        $body = Localization::get(
            'mail.registration.body',
            [
                'name' => $user->getUsername(),
                'verification_url' => $verificationUrl
            ]
        );

        return $this->sendToUser(
            $user,
            $subject,
            $body
        );
    }
    
    /**
     * Send password reset email
     */
    public function sendPasswordReset(UserModel $user, string $resetUrl): bool {
        return $this->sendPasswordActionMail(
            $user,
            $resetUrl,
            'mail.password_reset.subject',
            'mail.password_reset.body'
        );
    }

    /**
     * Send email for a user created by an administrator
     */
    public function sendUserCreatedByAdmin(UserModel $user, string $resetUrl): bool {
        return $this->sendPasswordActionMail(
            $user,
            $resetUrl,
            'mail.user_created.subject',
            'mail.user_created.body'
        );
    }

    /**
     * Send password related user email
     */
    private function sendPasswordActionMail(UserModel $user, string $resetUrl, string $subjectKey, string $bodyKey): bool {
        $subject = Localization::get($subjectKey);

        $body = Localization::get(
            $bodyKey,
            [
                'name' => $user->getUsername(),
                'reset_url' => $resetUrl
            ]
        );

        $body = $resetUrl;

        return $this->sendToUser(
            $user,
            $subject,
            $body
        );
    }

    /**
     * Send log notification based on log level
     */
    public function sendLogNotificationByLogLevel(string $logLevel, int $logCount, DateTimeInterface $logTime): bool {
        switch ($logLevel) {
            case LoggingConfiguration::LEVEL_EMERGENCY:
            case LoggingConfiguration::LEVEL_ALERT:
            case LoggingConfiguration::LEVEL_CRITICAL:
                return $this->sendLogNotification(
                    $logLevel,
                    $logCount,
                    $logTime
                );

            default:
                return false;
        }
    }

    /**
     * Prepare and send log notification
     */
    private function sendLogNotification(string $logLevel, int $logCount, DateTimeInterface $logTime): bool {
        $levelName = $this->getLogLevelName($logLevel);
        if ($levelName === null) {
            return false;
        }

        $subject = Localization::get(
            'mail.log_notification.subject',
            [
                'level' => $levelName
            ]
        );

        $body = Localization::get(
            'mail.log_notification.body',
            [
                'level' => $levelName,
                'count' => $logCount,
                'time' => $logTime->format('Y-m-d H:i:s')
            ]
        );

        return $this->sendToAdministrators(
            $subject,
            $body
        );
    }

    /**
     * Get localized log level name
     */
    private function getLogLevelName(string $logLevel): ?string {
        return match ($logLevel) {
            LoggingConfiguration::LEVEL_EMERGENCY => Localization::get('log.level.emergency'),
            LoggingConfiguration::LEVEL_ALERT => Localization::get('log.level.alert'),
            LoggingConfiguration::LEVEL_CRITICAL => Localization::get('log.level.critical'),
            default => null
        };
    }
}
