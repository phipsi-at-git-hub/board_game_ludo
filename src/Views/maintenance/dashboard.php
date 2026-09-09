<?php
use App\Core\Localization;

/**
 * @var SystemService $system_settings
 */
?>

<div class="panel">

    <h1><?= Localization::get('maintenance.dashboard.title') ?></h1>

    <?php if (!$system_settings::isSystemEnabled()): ?>
        <div class="alert alert-warning">
            <?= Localization::get('maintenance.dashboard.system_disabled') ?>
        </div>
    <?php endif; ?>

    <div class="dashboard-grid">

        <!-- System Status -->
        <div class="card dashboard-card">

            <h2><?= Localization::get('maintenance.dashboard.system_status') ?></h2>

            <div class="stats-main">
                <div class="stat-big">
                    <?= $system_settings::isSystemEnabled() ? 'ONLINE' : 'OFFLINE' ?>
                </div>

                <div class="stat-label">
                    <?= Localization::get('maintenance.dashboard.current_state') ?>
                </div>
            </div>

            <div class="stats-sub">

                <!-- Authentication -->
                <div>
                    <span class="stat-value">
                        <?= $system_settings::isLoginEnabled() ? 'ON' : 'OFF' ?>
                    </span>

                    <span class="stat-text">
                        <?= Localization::get('maintenance.dashboard.login_enabled') ?>
                    </span>
                </div>

                <div>
                    <span class="stat-value">
                        <?= $system_settings::isRegistrationEnabled() ? 'ON' : 'OFF' ?>
                    </span>

                    <span class="stat-text">
                        <?= Localization::get('maintenance.dashboard.registration_enabled') ?>
                    </span>
                </div>

            </div>

            <div class="stats-sub">

                <!-- Games -->
                <div>
                    <span class="stat-value">
                        <?= $system_settings::isGameCreationEnabled() ? 'ON' : 'OFF' ?>
                    </span>

                    <span class="stat-text">
                        <?= Localization::get('maintenance.dashboard.game_creation_enabled') ?>
                    </span>
                </div>

                <div>
                    <span class="stat-value">
                        <?= $system_settings::isGamePlayEnabled() ? 'ON' : 'OFF' ?>
                    </span>

                    <span class="stat-text">
                        <?= Localization::get('maintenance.dashboard.game_play_enabled') ?>
                    </span>
                </div>

            </div>

            <div class="stats-sub">

                <!-- Maintenance -->
                <div>
                    <span class="stat-value">
                        <?= $system_settings::isSystemEnabled() ? 'ON' : 'OFF' ?>
                    </span>

                    <span class="stat-text">
                        <?= Localization::get('maintenance.dashboard.system_enabled') ?>
                    </span>
                </div>

                <div>
                    <span class="stat-value">
                        <?= $system_settings::isMaintenanceModeEnabled() ? 'ON' : 'OFF' ?>
                    </span>

                    <span class="stat-text">
                        <?= Localization::get('maintenance.dashboard.maintenance_mode_enabled') ?>
                    </span>
                </div>

            </div>

            <!--
            <div class="nav-actions">
                <a href="/maintenance/admin/system" class="btn btn-primary">
                    <?= Localization::get('maintenance.dashboard.manage_settings') ?>
                </a>
            </div>
            -->

        </div>

        <!-- Administrator -->
        <div class="card dashboard-card">

            <h2><?= Localization::get('maintenance.dashboard.system_settings_entry') ?></h2>

            <div class="stats-main">
                <div class="stat-big">
                    <?= htmlspecialchars($system_settings::wasUpdatedBy()->getUsername()) ?>
                </div>

                <div class="stat-label">
                    <?= Localization::get('maintenance.dashboard.last_settings_update_by') ?>
                </div>
            </div>

            <div class="stats-sub">

                <div>
                    <span class="stat-value">
                        <?= htmlspecialchars($system_settings::wasUpdatedBy()->getRole()) ?>
                    </span>

                    <span class="stat-text">
                        <?= Localization::get('maintenance.dashboard.role') ?>
                    </span>
                </div>

                <div>
                    <span class="stat-value">
                        <?= htmlspecialchars($system_settings::wasUpdatedAt()) ?>
                    </span>

                    <span class="stat-text">
                        <?= Localization::get('maintenance.dashboard.last_settings_update_at') ?>
                    </span>
                </div>

            </div>

        </div>

    </div>

</div>