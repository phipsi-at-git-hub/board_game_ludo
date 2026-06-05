<?php
use App\Core\Localization;
use App\Models\SystemSettingsModel;

/**
 * @var SystemSettingsModel $system_settings
 * @var array $maintenance_messages 
 * @var array $notice_messages 
 */
?>

<div class="panel">
    <h1><?= Localization::get('admin.system.settings.title') ?></h1>

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
        <h2>⚙️ System Overview</h2>

        <div class="stats-sub">
            <div>
                <span class="stat-value">
                    <span class="status-badge status-<?= $system_settings->getRegistrationEnabled() && $system_settings->getLoginEnabled() ? 'ok' : 'fail' ?>">
                        <?= $system_settings->getRegistrationEnabled() && $system_settings->getLoginEnabled() ? 'OK' : 'LIMITED' ?>
                    </span>
                </span>
                <span class="stat-text">User</span>
            </div>

            <div>
                <span class="stat-value">
                    <span class="status-badge status-<?= $system_settings->getGameCreationEnabled() && $system_settings->getGamePlayEnabled() ? 'ok' : 'fail' ?>">
                        <?= $system_settings->getGameCreationEnabled() && $system_settings->getGamePlayEnabled() ? 'OK' : 'LIMITED' ?>
                    </span>
                </span>
                <span class="stat-text">Game</span>
            </div>

            <div>
                <span class="stat-value">
                    <span class="status-badge status-<?= !$system_settings->getMaintenanceModeEnabled() ? 'ok' : 'warning' ?>">
                        <?= !$system_settings->getMaintenanceModeEnabled() ? 'OK' : 'MAINT' ?>
                    </span>
                </span>
                <span class="stat-text">System</span>
            </div>
        </div>
    </div>

    <!-- Settings -->
    <div class="dashboard-grid">

        <!-- User -->
        <div class="card dashboard-card">
            <h2>👤 User</h2>

            <div class="setting-row">
                <span>Registration</span>

                <label class="switch">
                    <input type="checkbox"
                           <?= $system_settings->getRegistrationEnabled() ? 'checked' : '' ?>
                           data-setting="registration_enabled">
                    <span class="slider"></span>
                </label>
            </div>

            <div class="setting-row">
                <span>Login</span>

                <label class="switch">
                    <input type="checkbox"
                           <?= $system_settings->getLoginEnabled() ? 'checked' : '' ?>
                           data-setting="login_enabled">
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <!-- Game -->
        <div class="card dashboard-card">
            <h2>🎮 Game</h2>

            <div class="setting-row">
                <span>Game Creation</span>

                <label class="switch">
                    <input type="checkbox"
                           <?= $system_settings->getGameCreationEnabled() ? 'checked' : '' ?>
                           data-setting="game_creation_enabled">
                    <span class="slider"></span>
                </label>
            </div>

            <div class="setting-row">
                <span>Game Play</span>

                <label class="switch">
                    <input type="checkbox"
                           <?= $system_settings->getGamePlayEnabled() ? 'checked' : '' ?>
                           data-setting="game_play_enabled">
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <!-- System -->
        <div class="card dashboard-card">
            <h2>🛠 System</h2>

            <div class="setting-group" data-toggle-group>

                <div class="setting-row">
                    <span>Maintenance Mode</span>

                    <label class="switch">
                        <input type="checkbox"
                            <?= $system_settings->getMaintenanceModeEnabled() ? 'checked' : '' ?>
                            data-setting="maintenance_mode_enabled" data-toggle-switch>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="collapse <?= $system_settings->getMaintenanceModeEnabled() ? 'active' : '' ?>" data-toggle-target>
                    <!-- Maintenance Messages -->
                    <div class="nested-card">
                        <h3>🛠 Maintenance Messages</h3>

                        <div class="selection-layout">

                            <div class="selection-list">
                                <?php foreach ($maintenance_messages as $message): ?>
                                    <button
                                        class="message-item <?= $system_settings->getMaintenanceMessageString() === $message ? 'active' : '' ?>"
                                        type="button">

                                        <?= Localization::get($message) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>

                            <div class="selection-preview">
                                <h4>Preview</h4>

                                <p>
                                    <?= Localization::get($system_settings->getMaintenanceMessageString()) ?>
                                </p>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <div class="setting-row section-separator"></div>

            <div class="setting-group" data-toggle-group>

                <div class="setting-row">
                    <span>System Notice</span>

                    <label class="switch">
                        <input type="checkbox"
                            <?= $system_settings->getSystemNoticeEnabled() ? 'checked' : '' ?>
                            data-setting="system_notice_enabled" data-toggle-switch="">
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="collapse <?= $system_settings->getSystemNoticeEnabled() ? 'active' : '' ?>" data-toggle-target>

                    <!-- Notice Messages -->
                    <div class="nested-card">
                        <h3>📢 System Notices Message</h3>

                        <div class="selection-layout">

                            <div class="selection-list">
                                <?php foreach ($notice_messages as $message): ?>
                                    <button
                                        class="message-item <?= $system_settings->getSystemNoticeMessageString() === $message ? 'active' : '' ?>"
                                        type="button">

                                        <?= Localization::get($message) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>

                            <div class="selection-preview">
                                <h4>Preview</h4>

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

</div>
