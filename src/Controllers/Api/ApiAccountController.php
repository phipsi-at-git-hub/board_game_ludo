<?php
// src/Controllers/Api/ApiAccountController.php

namespace App\Controllers\Api;

use App\Constants\Application;
use App\Core\Application\App;
use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Dto\User\UserContext;
use App\Core\Http\Response;
use App\Core\Localization;
use App\Core\Logging\Logger;
use App\Services\UserService;

final class ApiAccountController extends BaseController {
    private UserService $userService;

    public function __construct(App $app) {
        parent::__construct($app);

        $this->userService = $app->resolve(UserService::class);
    }

    /**
     * Account context.
     */
    private function context(): array {
        return UserContext::fromUser(Auth::user());
    }

    /**
     * Update profile information.
     */
    public function updateProfile(): void {
        $user = Auth::user(); 
        $data = [
            'username' => $_POST['username'] ?? $user->getUsername(),
            'email' => $_POST['email'] ?? $user->getEmail(),
        ];

        $success = $this->userService->update($user, $data);
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

        // Logging
        Logger::app()->info('User account profile updated', ['user_id' => $user->getId()]);

        $this->jsonClean(
            Response::success(
                $this->context(),
                Localization::get(
                    'application.response.messages.save.success'
                )
            )
        );
    }

    /**
     * Update account locale.
     *
     * Used by the Binding System for individual settings.
     */
    public function updateLocale(): void {
        $user = Auth::user(); 
        $data = [];

        if (isset($_POST['preferred_language'])) {
            $data['preferred_language'] = $_POST['preferred_language'];
        }

        if (empty($data)) {
            $this->jsonClean(
                Response::error('No settings provided'),
                400
            );
        }

        $success = $this->userService->update($user, $data);
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

        // Logging
        Logger::app()->info('User account locale setting updated',['user_id' => $user->getId()]);

        $this->jsonClean(
            Response::success(
                $this->context(),
                Localization::get(
                    'application.response.messages.save.success'
                )
            )
        );
    }

    /**
     * Update account settings.
     *
     * Used by the Binding System for individual settings.
     */
    public function updateSettings(): void {
        $user = Auth::user(); 
        $data = [];

        if (isset($_POST['preferred_language'])) {
            $data['preferred_language'] = $_POST['preferred_language'];
        }

        if (isset($_POST['preferred_camera_mode'])) {
            $data['preferred_camera_mode'] = $_POST['preferred_camera_mode'];
        }

        if (empty($data)) {
            $this->jsonClean(
                Response::error('No settings provided'),
                400
            );
        }

        $success = $this->userService->update($user, $data);
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

        // Logging
        Logger::app()->info('User account settings updated',['user_id' => $user->getId()]);

        $this->jsonClean(
            Response::success(
                $this->context(),
                Localization::get(
                    'application.response.messages.save.success'
                )
            )
        );
    }

    /**
     * Change current user's password.
     */
    public function changePassword(): void {
        $user = Auth::user(); 
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!$user->verifyPassword($currentPassword)) {
            $this->jsonClean(
                Response::error(
                    Localization::get(
                        'account.password.current.invalid'
                    )
                ),
                400
            );
        }

        if ($newPassword !== $confirmPassword) {
            $this->jsonClean(
                Response::error(
                    Localization::get(
                        'account.password.confirm.mismatch'
                    )
                ),
                400
            );
        }

        if ($newPassword === '') {
            $this->jsonClean(
                Response::error(
                    Localization::get(
                        'account.password.new.required'
                    )
                ),
                400
            );
        }

        $success = $user->updatePassword($newPassword);

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

        // Logging
        Logger::app()->info('User account password changed',['user_id' => $user->getId()]);

        $this->jsonClean(
            Response::success(
                $this->context(),
                Localization::get(
                    'application.response.messages.save.success'
                )
            )
        );
    }

    /**
     * Show current account.
     */
    public function show(): void {
        $this->jsonClean(
            Response::success(
                $this->context()
            )
        );
    }
}
