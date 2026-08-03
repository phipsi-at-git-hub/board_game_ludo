<?php
// src/Controllers/Api/ApiAdminController.php

namespace App\Controllers\Api;

use App\Constants\Application;
use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Dto\System\SettingsContext;
use App\Core\Http\Response;
use App\Core\Localization;
use App\Core\Logging\Logger;
use App\Models\SystemSettingsModel;

final class ApiAdminController extends BaseController {
    /*
    public function __construct() {
        // ToDo: add SystemService in Models to provide main methods if needed
    }
    */

    /**
     * Helper - Current system settings
     */
    private function settings(): SystemSettingsModel {
        return SystemSettingsModel::findSystemSettings();
    }

    /**
     * Helper - DTO context
     */
    private function context(): array {
        return SettingsContext::fromSystem(
            $this->settings()
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
                $this->context(),
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
        // Logging
        Logger::app()->info('Admin system settings updated', ['user_id' => Auth::user()->getId()]);

        $this->updateSettings(
            function (SystemSettingsModel $settings): bool {
                $settings->updateBooleansToFalse();
                $settings->updateFromArray($_POST);
                $settings->update();

                return true;
            }
        );
    }
}
