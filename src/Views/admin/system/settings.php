<?php

use App\Constants\Application;
use App\Core\Csrf;
use App\Core\Localization;
use App\Core\SystemSettings;
use App\Models\SystemSettingsModel;

/**
 * @var SystemSettingsModel $system_settings
 * @var array $maintenance_messages 
 * @var array $notice_messages 
 */
?>

<div class="panel">

    <div id="form-response_b" class="ajax-response"></div>

    <h1><?= Localization::get('admin.system.settings.overview.title') ?></h1>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li>
                <a href="/lobby" class="btn-back">
                    <?= Localization::get('application.general.btn.back_to_lobby') ?>
                </a>
            </li>
            <li>
                <a href="/admin" class="btn-back">
                    <?= Localization::get('application.general.btn.back_to_dashboard') ?>
                </a>
            </li>
        </ul>
    </div>

    <!-- Overview -->
    <div class="card">
        <h2><?= Localization::get('admin.system.settings.card.overview.title') ?></h2>

        <div class="stats-main">
            <div class="stat-big">
                <?= SystemSettings::isSystemEnabled() ? strtoupper(Localization::get('application.general.online')) : strtoupper(Localization::get('application.general.offline')) ?>
            </div>
            <div class="stat-label">
                <?= Localization::get('admin.system_settings.card.current_state') ?>
            </div>
        </div>

        <div class="stats-sub">
            <div>
                <span class="stat-value">
                    <span class="status-badge status-<?= $system_settings->getRegistrationEnabled() && $system_settings->getLoginEnabled() ? Application::GENERAL_OK : Application::GENERAL_FAIL ?>">
                        <?= $system_settings->getRegistrationEnabled() && $system_settings->getLoginEnabled() ? strtoupper(Application::GENERAL_ON) : strtoupper(Application::GENERAL_OFF) ?>
                    </span>
                </span>
                <span class="stat-text"><?= Localization::get('admin.system_settings.card.authentication') ?></span>
            </div>

            <div>
                <span class="stat-value">
                    <span class="status-badge status-<?= $system_settings->getGameCreationEnabled() && $system_settings->getGamePlayEnabled() ? Application::GENERAL_OK : Application::GENERAL_FAIL ?>">
                        <?= $system_settings->getGameCreationEnabled() && $system_settings->getGamePlayEnabled() ? strtoupper(Application::GENERAL_ON) : strtoupper(Application::GENERAL_OFF) ?>
                    </span>
                </span>
                <span class="stat-text"><?= Localization::get('admin.system_settings.card.games') ?></span>
            </div>

            <div>
                <span class="stat-value">
                    <span class="status-badge status-<?= !$system_settings->getMaintenanceModeEnabled() ? Application::GENERAL_OK : Application::GENERAL_WARNING ?>">
                        <?= !$system_settings->getMaintenanceModeEnabled() ? strtoupper(Application::GENERAL_ON) : strtoupper(Application::GENERAL_OFF) ?>
                    </span>
                </span>
                <span class="stat-text"><?= Localization::get('admin.system_settings.card.maintenance') ?></span>
            </div>
        </div>
    </div>

    <form 
        id="system-settings-form" 
        method="post" 
        action="/admin/system/settings/update" 
        data-ajax-event="change" 
        data-ajax-target="#form-response">

        <input
            type="hidden" 
            name="_csrf_token" 
            value="<?= Csrf::generate() ?>"

        <!-- Settings -->
        <div class="dashboard-grid">

            <!-- User -->
            <div class="card dashboard-card">
                <h2><?= Localization::get('admin.system_settings.card.user.title') ?></h2>

                <div class="form-row">
                    <span><?= Localization::get('admin.system_settings.card.user.registration_enabled') ?></span>

                    <label class="switch">
                        <input 
                            type="checkbox" 
                            name="registration_enabled" 
                            value="1" 
                            <?= $system_settings->getRegistrationEnabled() ? 'checked' : '' ?>
                            data-auto-save="change">
                        <span class="slider">
                            <span class="slider-indicator"></span>
                        </span>
                    </label>
                </div>

                <div class="form-row">
                    <span><?= Localization::get('admin.system_settings.card.user.login_enabled') ?></span>

                    <label class="switch">
                        <input 
                            type="checkbox" 
                            name="login_enabled" 
                            value="1" 
                            <?= $system_settings->getLoginEnabled() ? 'checked' : '' ?>
                            data-auto-save="change">
                        <span class="slider">
                            <span class="slider-indicator"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Game -->
            <div class="card dashboard-card">
                <h2><?= Localization::get('admin.system_settings.card.game.title') ?></h2>

                <div class="form-row">
                    <span><?= Localization::get('admin.system_settings.card.game.creation_enabled') ?></span>

                    <label class="switch">
                        <input 
                            type="checkbox" 
                            name="game_creation_enabled" 
                            value="1"
                            <?= $system_settings->getGameCreationEnabled() ? 'checked' : '' ?>
                            data-auto-save="change">
                        <span class="slider">
                            <span class="slider-indicator"></span>
                        </span>
                    </label>
                </div>

                <div class="form-row">
                    <span><?= Localization::get('admin.system_settings.card.game.play_enabled') ?></span>

                    <label class="switch">
                        <input 
                            type="checkbox" 
                            name="game_play_enabled" 
                            value="1" 
                            <?= $system_settings->getGamePlayEnabled() ? 'checked' : '' ?>
                            data-auto-save="change">
                        <span class="slider">
                            <span class="slider-indicator"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- System -->
            <div class="card dashboard-card">
                <h2><?= Localization::get('admin.system_settings.card.maintenance.title') ?></h2>

                <div class="form-row">
                    <span><?= Localization::get('admin.system_settings.card.maintenance.system_enabled') ?></span>

                    <label class="switch">
                        <input 
                            type="checkbox" 
                            name="system_enabled" 
                            value="1" 
                            <?= $system_settings->getSystemEnabled() ? 'checked' : '' ?>
                            data-auto-save="change">
                        <span class="slider">
                            <span class="slider-indicator"></span>
                        </span>
                    </label>
                </div>

                <div class="setting-group" data-toggle-group>

                    <div class="form-row">
                        <span><?= Localization::get('admin.system_settings.card.maintenance.maintenance_mode_enabled') ?></span>

                        <label class="switch">
                            <input 
                                type="checkbox"
                                name="maintenance_mode_enabled" 
                                value="1" 
                                <?= $system_settings->getMaintenanceModeEnabled() ? 'checked' : '' ?>
                                data-auto-save="change" 
                                data-toggle-switch>
                            <span class="slider">
                                <span class="slider-indicator"></span>
                            </span>
                        </label>
                    </div>

                    <div class="collapse <?= $system_settings->getMaintenanceModeEnabled() ? 'active' : '' ?>" data-toggle-target>
                        <!-- Maintenance Messages -->
                        <div class="nested-card">
                            <h3><?= Localization::get('admin.system_settings.card.maintenance.maintenance_mode.message') ?></h3>

                            <div class="selection-layout">

                                <div class="selection-list">

                                    <input
                                        type="hidden" 
                                        name="maintenance_message" 
                                        value="<?= htmlspecialchars($system_settings->getMaintenanceMessageString()) ?>">

                                    <?php foreach ($maintenance_messages as $message): ?>
                                        <button
                                            class="message-item <?= $system_settings->getMaintenanceMessageString() === $message ? 'active' : '' ?>"
                                            type="button" 
                                            data-message-value="<?= htmlspecialchars($message) ?>"
                                            data-auto-save="click">

                                            <?= Localization::get($message) ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>

                                <div class="selection-preview">
                                    <h4><?= Localization::get('admin.system_settings.card.maintenance.preview') ?></h4>

                                    <p>
                                        <?= Localization::get($system_settings->getMaintenanceMessageString()) ?>
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <!--<div class="setting-row section-separator"></div>-->

                <div class="setting-group" data-toggle-group>

                    <div class="form-row">
                        <span><?= Localization::get('admin.system_settings.card.maintenance.system_notice_enabled') ?></span>

                        <label class="switch">
                            <input 
                                type="checkbox" 
                                name="system_notice_enabled" 
                                value="1" 
                                <?= $system_settings->getSystemNoticeEnabled() ? 'checked' : '' ?>
                                data-auto-save="change" 
                                data-toggle-switch>
                            <span class="slider">
                                <span class="slider-indicator"></span>
                            </span>
                        </label>
                    </div>

                    <div class="collapse <?= $system_settings->getSystemNoticeEnabled() ? 'active' : '' ?>" data-toggle-target>

                        <!-- Notice Messages -->
                        <div class="nested-card">
                            <h3><?= Localization::get('admin.system_settings.card.maintenance.system_notice.message') ?></h3>

                            <div class="selection-layout">

                                <div class="selection-list">

                                    <input
                                        type="hidden" 
                                        name="system_notice_message" 
                                        value="<?= htmlspecialchars($system_settings->getSystemNoticeMessageString()) ?>">

                                    <?php foreach ($notice_messages as $message): ?>
                                        <button
                                            class="message-item <?= $system_settings->getSystemNoticeMessageString() === $message ? 'active' : '' ?>"
                                            type="button" 
                                            data-message-value="<?= htmlspecialchars($message) ?>" 
                                            data-auto-save="click">

                                            <?= Localization::get($message) ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>

                                <div class="selection-preview">
                                    <h4><?= Localization::get('admin.system_settings.card.maintenance.preview') ?></h4>

                                    <p>
                                        <?= Localization::get($system_settings->getSystemNoticeMessageString()) ?>
                                    </p>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>
