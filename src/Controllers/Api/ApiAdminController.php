<?php
// src/Controllers/Api/ApiAdminController.php

namespace App\Controllers\Api;

use App\Constants\Application;
use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Date\DateRange;
use App\Core\Dto\Logging\EntryFilterContext;
use App\Core\Dto\System\SettingsContext;
use App\Core\Dto\User\UserContext;
use App\Core\Http\Http;
use App\Core\Http\Response;
use App\Core\Localization;
use App\Core\Logging\Logger;
use App\Core\Logging\LoggingConfiguration;
use App\Models\System\SystemSettingsModel;
use App\Models\User\UserModel;
use App\Services\LogService;
use App\Services\MailService;
use App\Services\UserService;

final class ApiAdminController extends BaseController {
    /*
    public function __construct() {
        // ToDo: add SystemService in Models to provide main methods if needed
    }
    */

    /**
     * User section
     */
    /**
     * updateUserProfile
     * User - Update user information / account
     *
     * @return void
     */
    public function updateUserProfile(): void {
        $user = UserModel::findById($_POST['user_id']); 
        $data = [
            'username' => $_POST['username'] ?? null,
            'email' => $_POST['email'] ?? null,
        ];
        $userService = new UserService();

        $success = $userService->update($user, $data);
        if (!$success) {
            $this->jsonClean(
                Response::error(
                    Localization::get(
                        'application.response.messages.save.failed'
                    )
                ),
                400
            );
        }

        $context = UserContext::fromUser($user);

        // Logging
        Logger::app()->info('User account profile updated', ['user_id' => $user->getId()]);

        $this->jsonClean(
            Response::success(
                $context,
                Localization::get(
                    'application.response.messages.save.success'
                )
            )
        );
    }

    /**
     * updateUserRole
     * User - Update user role
     *
     * @return void
     */
    public function updateUserRole(): void {
        $user = UserModel::findById($_POST['user_id']); 
        $data = [
            'role' => $_POST['role'] ?? null,
        ];

        // Check if the user to be updated is the current user
        if ($user->getId() === Auth::user()->getId()) {
            $this->jsonClean(
                Response::error(
                    Localization::get(
                        'application.response.messages.save.failed'
                    )
                ),
                400
            );
        }
        
        $userService = new UserService();

        $success = $userService->update($user, $data);
        if (!$success) {
            $this->jsonClean(
                Response::error(
                    Localization::get(
                        'application.response.messages.save.failed'
                    )
                ),
                400
            );
        }

        $context = UserContext::fromUser($user);

        // Logging
        Logger::app()->info('User account profile updated', ['user_id' => $user->getId()]);

        $this->jsonClean(
            Response::success(
                $context,
                Localization::get(
                    'application.response.messages.save.success'
                )
            )
        );
    }

    /**
     * updateUserStatus
     *
     * @return void
     */
    public function updateUserStatus(): void {
        $user = UserModel::findById($_POST['user_id']); 
        $data = [
            'status' => $_POST['status'] ?? null,
        ];

        // Check if the user to be updated is the current user
        if ($user->getId() === Auth::user()->getId()) {
            $this->jsonClean(
                Response::error(
                    Localization::get(
                        'application.response.messages.save.failed'
                    )
                ),
                400
            );
        }

        $userService = new UserService();

        $success = $userService->update($user, $data);
        if (!$success) {
            $this->jsonClean(
                Response::error(
                    Localization::get(
                        'application.response.messages.save.failed'
                    )
                ),
                400
            );
        }

        $context = UserContext::fromUser($user);

        // Logging
        Logger::app()->info('User account profile updated', ['user_id' => $user->getId()]);

        $this->jsonClean(
            Response::success(
                $context,
                Localization::get(
                    'application.response.messages.save.success'
                )
            )
        );
    }

    /**
     * updateUserLocale
     *
     * @return void
     */
    public function updateUserLocale(): void {
        $user = UserModel::findById($_POST['user_id']); 
        $data = [
            'preferred_language' => $_POST['preferred_language'] ?? null,
        ];
        $userService = new UserService();

        $success = $userService->update($user, $data);
        if (!$success) {
            $this->jsonClean(
                Response::error(
                    Localization::get(
                        'application.response.messages.save.failed'
                    )
                ),
                400
            );
        }

        $context = UserContext::fromUser($user);

        // Logging
        Logger::app()->info('User account profile updated', ['user_id' => $user->getId()]);

        $this->jsonClean(
            Response::success(
                $context,
                Localization::get(
                    'application.response.messages.save.success'
                )
            )
        );
    }

    /**
     * updateUserSettings
     *
     * @return void
     */
    public function updateUserSettings(): void {
        $user = UserModel::findById($_POST['user_id']); 
        $data = [
            'preferred_camera_mode' => $_POST['preferred_camera_mode'] ?? null,
        ];
        $userService = new UserService();

        $success = $userService->update($user, $data);
        if (!$success) {
            $this->jsonClean(
                Response::error(
                    Localization::get(
                        'application.response.messages.save.failed'
                    )
                ),
                400
            );
        }

        $context = UserContext::fromUser($user);

        // Logging
        Logger::app()->info('User account profile updated', ['user_id' => $user->getId()]);

        $this->jsonClean(
            Response::success(
                $context,
                Localization::get(
                    'application.response.messages.save.success'
                )
            )
        );
    } 

    /**
     * sendUserResetMail
     *
     * @return void
     */
    public function sendUserResetMail(): void {
        // ToDo: Implement
        $user = UserModel::findById($_POST['user_id']); 
        if (!$user) {
            $this->jsonClean(
                Response::error(
                    Localization::get(
                        'application.response.messages.save.failed'
                    )
                ),
                400
            );
        }

        $email = $user->getEmail(); 
        $token = UserModel::createPasswordToken($email);
        if ($token === null) {
            $this->redirect('/forgot-password'); 
        }

        $resetUrl = Http::url('/reset-password/'. $token);

        // Send Email for password reset
        $mailService = new MailService(); 
        $mailService->sendPasswordReset($user, $resetUrl); 

        $context = UserContext::fromUser($user);

        // Logging
        Logger::app()->notice('Reset password link sent to user' . $user->getId(), ['user_id' => Auth::user()->getId()]);

        $this->jsonClean(
            Response::success(
                $context,
                Localization::get(
                    //'application.response.messages.save.success'
                    'account.reset_password_sent.title'
                )
            )
        );
    }

    /**
     * Settings section
     */
    /**
     * Helper - Current system settings
     */
    private function settings(): SystemSettingsModel {
        return SystemSettingsModel::findSystemSettings();
    }

    /**
     * Helper - DTO settings context
     */
    private function settingsContext(): array {
        return SettingsContext::fromSystem(
            $this->settings()
        );
    }

    /**
     * Helper - DTO logging filter context
     */
    private function entryFilterContext(
        array $channels, 
        string $date_range, 
        array $available_channels, 
        int $entries_count, 
        array $entry_statistics  
    ): array {
        return EntryFilterContext::fromFilter(
            $channels, 
            $date_range, 
            $available_channels, 
            $entries_count, 
            $entry_statistics 
        ); 
    } 

    /**
     * Generic settings update handler
     */
    private function updateSettings(callable $callback): void {
        if ($_SERVER[Application::REQUEST_METHOD] !== Application::REQUEST_METHOD_POST) {
            $this->jsonClean(
                Response::error('Invalid request'),
                400
            );
        }

        $settings = $this->settings();
        $success = $callback($settings);
        
        // Logging
        Logger::app()->info('Admin settings updated', ['user_id' => Auth::user()->getId()]);

        if (!$success) {
            $this->jsonClean(
                Response::error(
                    Localization::get(
                        'application.response.messages.save.failed'
                    )
                ),
                400
            );
        }

        $this->jsonClean(
            Response::success(
                $this->settingsContext(),
                Localization::get(
                    'application.response.messages.save.success'
                )
            )
        );
    }

    /**
     * Update all system settings
     */
    public function updateSystemSettings(): void { 
        $this->updateSettings(
            function (SystemSettingsModel $settings): bool {
                $settings->updateBooleansToFalse();
                $settings->updateFromArray($_POST);
                $settings->update();

                return true;
            }
        );
    }

    /**
     * Render logging filter view
     */
    public function loggingFilterView(): void {
        if ($_SERVER[Application::REQUEST_METHOD] !== Application::REQUEST_METHOD_POST) {
            $this->jsonClean(
                Response::error('Invalid request'),
                400
            );
        }

        // Get all available logging channels
        $available_channels = LoggingConfiguration::getChannelsWithFileStorage();

        // Get selected logging channels
        $channels = $_POST['channels'] ?? [];

        if (!is_array($channels)) {
            $channels = [];
        }

        // Parse selected date range
        $date_range = DateRange::fromString($_POST['date_range'] ?? '');

        if ($date_range === null) {
            $this->jsonClean(
                Response::error('Invalid date range'),
                400
            );
        }

        // Parse log level if in POST body
        $log_levels = $_POST['log_levels'] ?? []; 

        if (!is_array($log_levels)) {
            $log_levels = []; 
        }

        // Load filtered log entries
        $log_service = new LogService(
            $channels,
            [
                $date_range->getStart(),
                $date_range->getEnd()
            ], 
            $log_levels 
        );

        // Sort log entries
        $log_service->orderBy(Application::ORDER_BY_TIMESTAMP, Application::ORDER_DESC); 

        // Get filtered log entries
        $entries = $log_service->getEntries();

        // Get normalized date range
        $date_range_string = $log_service->getDateRangeAsString();

        // Build logging filter context
        $context = EntryFilterContext::fromFilter(
            $channels, 
            $date_range_string, 
            $available_channels, 
            $log_service->getCount(), 
            $log_service->getStatistics() 
        );

        // Render filtered log entries
        $views = [
            'entries' => $this->renderView(
                'admin/logging/partials/entries',
                [
                    'entries' => $entries
                ]
            )
        ];

        // Logging
        Logger::app()->debug('Api admin logging filtered list view', ['user_id' => Auth::user()->getId()]);

        $this->jsonClean(
            Response::success(
                $context,
                'Logging filter applied',
                $views
            )
        );
    }
}
