<?php 
use App\Core\Localization; 
use App\Core\SystemSettings; 

/** @var array $stats */
?>

<div class="panel">
    <h1><?= Localization::get('admin.dashboard.title') ?></h1>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li><a href="/lobby" class="btn-back"><?= Localization::get('application.general.btn.back_to_lobby') ?></a></li>
        </ul>
    </div>

    <div class="dashboard-grid">

        <!-- Users -->
        <div class="card dashboard-card">
            <h2><?= Localization::get('admin.dashboard.users_card.title') ?></h2>

            <div class="stats-main">
                <div class="stat-big"><?= $stats['users_total'] ?></div>
                <div class="stat-label"><?= Localization::get('admin.dashboard.users_card.total') ?></div>
            </div>

            <div class="stats-sub">
                <div>
                    <span class="stat-value"><?= $stats['users_active'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.users_card.active') ?></span>
                </div>
                <div>
                    <span class="stat-value"><?= $stats['users_inactive'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.users_card.inactive') ?></span>
                </div>
                <div>
                    <span class="stat-value"><?= $stats['admins_total'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.users_card.admin') ?></span>
                </div>
            </div>

            <div class="nav-actions">
                <a href="/admin/users" class="btn btn-primary"><?= Localization::get('admin.dashboard.users_manage') ?></a>
            </div>
        </div>

        <!-- Games -->
        <div class="card dashboard-card">
            <h2><?= Localization::get('admin.dashboard.games_card.title') ?></h2>

            <div class="stats-main">
                <div class="stat-big"><?= $stats['games_total'] ?></div>
                <div class="stat-label"><?= Localization::get('admin.dashboard.games_card.total') ?></div>
            </div>

            <div class="stats-sub">
                <div>
                    <span class="stat-value"><?= $stats['games_waiting'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.games_card.waiting') ?></span>
                </div>
                <div>
                    <span class="stat-value"><?= $stats['games_active'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.games_card.running') ?></span>
                </div>
                <div>
                    <span class="stat-value"><?= $stats['games_finished'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.games_card.finished') ?></span>
                </div>
            </div>

            <div class="nav-actions">
                <a href="/admin/games/list" class="btn btn-primary"><?= Localization::get('admin.dashboard.games_manage') ?></a>
            </div>
        </div>

        <!-- System Status -->
        <div class="card dashboard-card">

            <h2><?= Localization::get('admin.dashboard.system_status.title') ?></h2>

            <div class="stats-main">
                <div class="stat-big">
                    <?= SystemSettings::isSystemEnabled() ? strtoupper(Localization::get('application.general.online')) : strtoupper(Localization::get('application.general.offline')) ?>
                </div>

                <div class="stat-label">
                    <?= Localization::get('admin.dashboard.current_state') ?>
                </div>
            </div>

            <div class="stats-sub">

                <!-- Authentication -->
                <div>
                    <span class="stat-value">
                        <?= SystemSettings::isLoginEnabled() ? strtoupper(Localization::get('application.general.on')) : strtoupper(Localization::get('application.general.off')) ?>
                    </span>

                    <span class="stat-text">
                        <?= Localization::get('admin.dashboard.login_enabled') ?>
                    </span>
                </div>

                <div>
                    <span class="stat-value">
                        <?= SystemSettings::isRegistrationEnabled() ? strtoupper(Localization::get('application.general.on')) : strtoupper(Localization::get('application.general.off')) ?>
                    </span>

                    <span class="stat-text">
                        <?= Localization::get('admin.dashboard.registration_enabled') ?>
                    </span>
                </div>

            </div>

            <div class="stats-sub">

                <!-- Games -->
                <div>
                    <span class="stat-value">
                        <?= SystemSettings::isGameCreationEnabled() ? strtoupper(Localization::get('application.general.on')) : strtoupper(Localization::get('application.general.off')) ?>
                    </span>

                    <span class="stat-text">
                        <?= Localization::get('admin.dashboard.game_creation_enabled') ?>
                    </span>
                </div>

                <div>
                    <span class="stat-value">
                        <?= SystemSettings::isGamePlayEnabled() ? strtoupper(Localization::get('application.general.on')) : strtoupper(Localization::get('application.general.off')) ?>
                    </span>

                    <span class="stat-text">
                        <?= Localization::get('admin.dashboard.game_play_enabled') ?>
                    </span>
                </div>

            </div>

            <div class="stats-sub">

                <!-- Maintenance -->
                <!--
                <div>
                    <span class="stat-value">
                        <?= SystemSettings::isSystemEnabled() ? strtoupper(Localization::get('application.general.on')) : strtoupper(Localization::get('application.general.off')) ?>
                    </span>

                    <span class="stat-text">
                        <?= Localization::get('admin.dashboard.system_enabled') ?>
                    </span>
                </div>
                -->

                <div>
                    <span class="stat-value">
                        <?= SystemSettings::isSystemNoticeEnabled() ? strtoupper(Localization::get('application.general.on')) : strtoupper(Localization::get('application.general.off')) ?>
                    </span>

                    <span class="stat-text">
                        <?= Localization::get('admin.dashboard.system_notice_enabled') ?>
                    </span>
                </div>

                <div>
                    <span class="stat-value">
                        <?= SystemSettings::isMaintenanceModeEnabled() ? strtoupper(Localization::get('application.general.on')) : strtoupper(Localization::get('application.general.off')) ?>
                    </span>

                    <span class="stat-text">
                        <?= Localization::get('admin.dashboard.maintenance_mode_enabled') ?>
                    </span>
                </div>

            </div>

            <div class="stats-sub">

                <!-- Maintenance Messages -->

                <div>
                    <span class="stat-value">
                        <?= SystemSettings::showSystemNoticeMessage() ?? '-' ?>
                    </span>

                    <span class="stat-text">
                        <?= Localization::get('admin.dashboard.system_notice_message') ?>
                    </span>
                </div>

                <div>
                    <span class="stat-value">
                        <?= SystemSettings::showMaintenanceMessage() ?? '-' ?>
                    </span>

                    <span class="stat-text">
                        <?= Localization::get('admin.dashboard.maintenance_mode_message') ?>
                    </span>
                </div>

            </div>

            <div class="nav-actions">
                <a href="/admin/system_settings" class="btn btn-primary">
                    <?= Localization::get('admin.dashboard.system_settings_manage') ?>
                </a>
            </div>

        </div>

    </div>

</div>
