<?php

use App\Constants\Application;
use App\Core\Localization;

/**
 * @var string $overall
 * @var array $database
 * @var array $environment
 * @var array $game
 */
?>

<div class="panel">
    <h1><?= Localization::get('admin.system.health.overview.title') ?></h1>

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
    <div class="card dashboard-card">
        <h2> <?= Localization::get('admin.system.health.card.overview.title') ?></h2>

        <div class="stats-main">
            <div class="stat-big">
                <?= strtoupper($overall) ?>
            </div>
            <div class="stat-label">
                <?= Localization::get('admin.system.health.card.overall.health') ?>
            </div>
        </div>

        <div class="stats-sub">

            <div>
                <span class="stat-value">
                    <span class="status-badge status-<?= $database['status'] ?>">
                        <?= strtoupper($database['status']) ?>
                    </span>
                </span>
                <span class="stat-text"><?= Localization::get('admin.system.health.card.overall.database') ?></span>
            </div>

            <div>
                <span class="stat-value">
                    <span class="status-badge status-<?= $environment['status'] ?>">
                        <?= strtoupper($environment['status']) ?>
                    </span>
                </span>
                <span class="stat-text"><?= Localization::get('admin.system.health.card.overall.environment') ?></span>
            </div>

            <div>
                <span class="stat-value">
                    <span class="status-badge status-<?= ($game['status_ok']) ? Application::GENERAL_OK : Application::GENERAL_FAIL ?>">
                        <?= $game['status_ok'] ? strtoupper(Application::GENERAL_OK) : strtoupper(Application::GENERAL_FAIL) ?>
                    </span>
                </span>
                <span class="stat-text"><?= Localization::get('admin.system.health.card.overall.game') ?></span>
            </div>

        </div>
    </div>

    <!-- Detail Cards -->
    <div class="overview-grid">

        <!-- DATABASE -->
        <div class="card overview-card">
            <div class="card-header">
                <h2><?= Localization::get('admin.system.health.card.database.title') ?></h2>
            </div>

            <div class="card-body meta-grid">

                <div><?= Localization::get('admin.system.health.card.db.host.label') ?></div>
                <div><?= htmlspecialchars($database['db_host'] ?? '-') ?></div>

                <div><?= Localization::get('admin.system.health.card.db.name.label') ?></div>
                <div><?= htmlspecialchars($database['db_name'] ?? '-') ?></div>

                <div><?= Localization::get('admin.system.health.card.db.system.label') ?></div>
                <div><?= htmlspecialchars($database['db_system'] ?? 'MySQL') ?></div>

                <div><?= Localization::get('admin.system.health.card.db.version.label') ?></div>
                <div><?= htmlspecialchars($database['db_version'] ?? '-') ?></div>

                <div><?= Localization::get('admin.system.health.card.db.latency.label') ?></div>
                <div>
                    <span class="status-badge status-<?= $database['latency_state'] ?>">
                        <?= htmlspecialchars($database['latency_ms'] ?? '-') ?> ms
                    </span>
                </div>

                <div><?= Localization::get('admin.system.health.card.db.connections.label') ?></div>
                <div><?= htmlspecialchars($database['threads_connected'] ?? '-') ?></div>

                <div><?= Localization::get('admin.system.health.card.db.size.label') ?></div>
                <div><?= htmlspecialchars($database['db_size']) ?></div>

            </div>
        </div>

        <!-- ENVIRONMENT -->
        <div class="card overview-card">
            <div class="card-header">
                <h2><?= Localization::get('admin.system.health.card.environment.title') ?></h2>
            </div>

            <div class="card-body meta-grid">

                <div><?= Localization::get('admin.system.health.card.environment.app.label') ?></div>
                <div>
                    <span class="status-badge status-<?= ($environment['app_env'] === Application::GENERAL_PROD) ? Application::GENERAL_OK : Application::GENERAL_FAIL ?>">
                        <?= strtoupper(htmlspecialchars($environment['app_env'] ?? '-')) ?>
                    </span>
                </div>

                <div><?= Localization::get('admin.system.health.card.environment.debug.label') ?></div>
                <div>
                    <span class="status-badge status-<?= ($environment['debug'] ?? false) ? Application::GENERAL_WARNING : Application::GENERAL_OK ?>">
                        <?= ($environment['debug'] ?? false) ? strtoupper(Application::GENERAL_ON) : strtoupper(Application::GENERAL_OFF) ?>
                    </span>
                </div>

                <div><?= Localization::get('admin.system.health.card.environment.php.label') ?></div>
                <div><?= htmlspecialchars($environment['php_version'] ?? '-') ?></div>

                <div><?= Localization::get('admin.system.health.card.environment.memory_limit.label') ?></div>
                <div><?= htmlspecialchars($environment['memory_limit'] ?? '-') ?></div>

                <div><?= Localization::get('admin.system.health.card.environment.space_free.label') ?></div>
                <div><?= htmlspecialchars($environment['disk_free_space']) ?></div>

                <div><?= Localization::get('admin.system.health.card.environment.space_total.label') ?></div>
                <div><?= htmlspecialchars($environment['disk_total_space']) ?></div>

                <div><?= Localization::get('admin.system.health.card.environment.space_used.label') ?></div>
                <div>
                    <span class="status-badge status-<?= $environment['disk_free_2_total_space_state'] ?>">
                        <?= htmlspecialchars($environment['disk_free_2_total_space']) ?>
                    </span>
                </div>

            </div>
        </div>

        <!-- GAME -->
        <div class="card overview-card">
            <div class="card-header">
                <h2><?= Localization::get('admin.system.health.card.game.title') ?></h2>
            </div>

            <div class="card-body meta-grid">

                <div><?= Localization::get('admin.system.health.card.game.engine_health.label') ?></div>
                <div>
                    <span class="status-badge status-<?= ($game['engine'] ?? false) ? Application::GENERAL_OK : Application::GENERAL_FAIL ?>">
                        <?= ($game['engine'] ?? false) ? strtoupper(Application::GENERAL_OK) : strtoupper(Application::GENERAL_FAIL) ?>
                    </span>
                </div>

                <div><?= Localization::get('admin.system.health.card.game.api_health.label') ?></div>
                <div>
                    <span class="status-badge status-<?= ($game['api'] ?? false) ? Application::GENERAL_OK : Application::GENERAL_FAIL ?>">
                        <?= ($game['api'] ?? false) ? strtoupper(Application::GENERAL_OK) : strtoupper(Application::GENERAL_FAIL) ?>
                    </span>
                </div>

                <div><?= Localization::get('admin.system.health.card.game.api_reachable.label') ?></div>
                <div>
                    <span class="status-badge status-<?= ($game['reachable'] ?? false) ? Application::GENERAL_OK : Application::GENERAL_FAIL ?>">
                        <?= ($game['reachable'] ?? false) ? strtoupper(Application::GENERAL_OK) : strtoupper(Application::GENERAL_FAIL) ?>
                    </span>
                </div>

                <div><?= Localization::get('admin.system.health.card.game.latency.label') ?></div>
                <div>
                    <span class="status-badge status-<?= $game['latency_state'] ?>">
                        <?= htmlspecialchars($game['latency'] ?? '-') ?> ms
                    </span>
                </div>

                <?php if (isset($game['version'])): ?>
                    <div><?= Localization::get('admin.system.health.card.game.version.label') ?></div>
                    <div><?= htmlspecialchars($game['version']) ?></div>
                <?php endif; ?>

            </div>
        </div>

    </div>
</div>