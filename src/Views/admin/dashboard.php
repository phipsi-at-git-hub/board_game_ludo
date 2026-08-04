<?php 
use App\Core\Localization;
use App\Health\SystemHealth;
use App\Services\SystemService;

/** 
 * @var array $users_card 
 * @var array $games_card 
 * @var SystemService $system_settings 
 * @var array $logs_card 
 * @var array $stats_card
 */
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
                <div class="stat-big"><?= $users_card['users_total'] ?></div>
                <div class="stat-label"><?= Localization::get('admin.dashboard.users_card.total') ?></div>
            </div>

            <div class="stats-sub">
                <div>
                    <span class="stat-value"><?= $users_card['users_active'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.users_card.active') ?></span>
                </div>
                <div>
                    <span class="stat-value"><?= $users_card['users_inactive'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.users_card.inactive') ?></span>
                </div>
                <div>
                    <span class="stat-value"><?= $users_card['admins_total'] ?></span>
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
                <div class="stat-big"><?= $games_card['games_total'] ?></div>
                <div class="stat-label"><?= Localization::get('admin.dashboard.games_card.total') ?></div>
            </div>

            <div class="stats-sub">
                <div>
                    <span class="stat-value"><?= $games_card['games_waiting'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.games_card.waiting') ?></span>
                </div>
                <div>
                    <span class="stat-value"><?= $games_card['games_active'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.games_card.running') ?></span>
                </div>
                <div>
                    <span class="stat-value"><?= $games_card['games_finished'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.games_card.finished') ?></span>
                </div>
            </div>

            <div class="nav-actions">
                <a href="/admin/games" class="btn btn-primary"><?= Localization::get('admin.dashboard.games_manage') ?></a>
            </div>
        </div>

        <!-- System Status -->
        <div class="card dashboard-card">
            <h2><?= Localization::get('admin.dashboard.system_settings_card.system_status.title') ?></h2>

            <div class="stats-main">
                <div class="stat-big"><?= $system_settings->isSystemEnabled() ? strtoupper(Localization::get('application.general.online')) : strtoupper(Localization::get('application.general.offline')) ?></div>
                <div class="stat-label"><?= Localization::get('admin.dashboard.system_settings_card.current_state') ?></div>
            </div>

            <div class="stats-sub">
                <div>
                    <span class="stat-value"><?= strtoupper(Localization::get('application.general.' . $system_settings->getAuthenticationStatus())) ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.system_settings_card.authentication') ?></span>
                </div>
                <div>
                    <span class="stat-value"><?= strtoupper(Localization::get('application.general.' . $system_settings->getGamesStatus())) ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.system_settings_card.games') ?></span>
                </div>
                <div>
                    <span class="stat-value"><?= strtoupper(Localization::get('application.general.' . $system_settings->getMaintenanceStatus())) ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.system_settings_card.maintenance') ?></span>
                </div>
            </div>

            <div class="nav-actions">
                <a href="/admin/system/settings" class="btn btn-primary"><?= Localization::get('admin.dashboard.system_settings_manage') ?></a>
            </div>
        </div>

        <!-- System Health -->
        <div class="card dashboard-card">
            <h2><?= Localization::get('admin.dashboard.health_card.system_health.title') ?></h2>

            <div class="stats-main">
                <div class="stat-big"><?= strtoupper(Localization::get('application.general.' . SystemHealth::getStatus())) ?></div>
                <div class="stat-label"><?= Localization::get('admin.dashboard.health_card.overall_health') ?></div>
            </div>

            <div class="stats-sub">
                <div>
                    <span class="stat-value"><?= SystemHealth::isEnvironmentHealthy() ? strtoupper(Localization::get('application.general.okay')) : strtoupper(Localization::get('application.general.not_okay')) ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.health_card.environment') ?></span>
                </div>
                <div>
                    <span class="stat-value"><?= SystemHealth::isDatabaseHealthy() ? strtoupper(Localization::get('application.general.okay')) : strtoupper(Localization::get('application.general.not_okay')) ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.health_card.database') ?></span>
                </div>
                <div>
                    <span class="stat-value"><?= SystemHealth::isGameHealthy() ? strtoupper(Localization::get('application.general.okay')) : strtoupper(Localization::get('application.general.not_okay')) ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.health_card.game') ?></span>
                </div>
            </div>

            
            <div class="nav-actions">
                <a href="/admin/system/health" class="btn btn-primary"><?= Localization::get('admin.dashboard.system_health_show') ?></a>
            </div>

        </div>

        <!-- System Logs -->
        <div class="card dashboard-card">
            <h2><?= Localization::get('admin.dashboard.system_logs_card.system_logs.title') ?></h2>

            <div class="stats-main">
                <div class="stat-big"><?= strtoupper($logs_card['highest_level']) ?></div>
                <div class="stat-label"><?= Localization::get('admin.dashboard.system_logs_card.highest_level') ?></div>
            </div>

            <div class="stats-sub">
                <div>
                    <span class="stat-value"><?= $logs_card['emergency'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.system_logs_card.emergency') ?></span>
                </div>
                <div>
                    <span class="stat-value"><?= $logs_card['alert'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.system_logs_card.alert') ?></span>
                </div>
                <div>
                    <span class="stat-value"><?= $logs_card['critical'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.system_logs_card.critical') ?></span>
                </div>
            </div>

            <div class="nav-actions">
                <a href="/admin/logging/list" class="btn btn-primary"><?= Localization::get('admin.dashboard.system_logs_manage') ?></a>
            </div>
        </div>

        <!-- System Statistic -->
        <div class="card dashboard-card">
            <h2><?= Localization::get('admin.dashboard.system_statistics_card.system_statistics.title') ?></h2>

            <div class="stats-main">
                <div class="stat-big"><?= strtoupper($stats_card['main']) ?></div>
                <div class="stat-label"><?= Localization::get('admin.dashboard.system_statistics_card.games_played') ?></div>
            </div>

            <div class="stats-sub">
                <div>
                    <span class="stat-value"><?= $stats_card['main'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.system_statistics_card.games_created') ?></span>
                </div>
                <div>
                    <span class="stat-value"><?= $stats_card['main'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.system_statistics_card.user_logins') ?></span>
                </div>
                <div>
                    <span class="stat-value"><?= $stats_card['main'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.system_statistics_card.user_registrations') ?></span>
                </div>
            </div>

            
            <div class="nav-actions">
                <a href="/admin/system/statistics" class="btn btn-primary"><?= Localization::get('admin.dashboard.system_statistics_show') ?></a>
            </div>

        </div>

    </div>

</div>
