<?php
// src/Controllers/Api/ApiAdminController.php

namespace App\Controllers\Api;

use App\Constants\Application;
use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Date\DateRange;
use App\Core\Dto\Logging\EntryFilterContext;
use App\Core\Dto\System\SettingsContext;
use App\Core\Http\Response;
use App\Core\Localization;
use App\Core\Logging\Logger;
use App\Core\Logging\LoggingConfiguration;
use App\Models\SystemSettingsModel;
use App\Services\LogService;
use DateTimeImmutable;

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
        int $entries_count 
    ): array {
        return EntryFilterContext::fromFilter(
            $channels, 
            $date_range, 
            $available_channels, 
            $entries_count 
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

        // Load filtered log entries
        $log_service = new LogService(
            $channels,
            [
                $date_range->getStart(),
                $date_range->getEnd()
            ]
        );

        // Get filtered log entries
        $entries = $log_service->getEntries();

        // Get normalized date range
        $date_range_string = $log_service->getDateRangeAsString();

        // Build logging filter context
        $context = EntryFilterContext::fromFilter(
            $channels,
            $date_range_string,
            $available_channels,
            $log_service->getCount()
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
