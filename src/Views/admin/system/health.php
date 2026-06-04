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

    <!-- System Overview -->
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
                    <?= strtoupper($database['status']) ?>
                </span>
                <span class="stat-text"><?= Localization::get('admin.system.health.card.overall.database') ?></span>
            </div>

            <div>
                <span class="stat-value">
                    <?= strtoupper($environment['status']) ?>
                </span>
                <span class="stat-text"><?= Localization::get('admin.system.health.card.overall.environment') ?></span>
            </div>

            <div>
                <span class="stat-value">
                    <?= $game['status_ok'] ? strtoupper(Application::GENERAL_OK) : strtoupper(Application::GENERAL_FAIL) ?>
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
                <h2>🗄 Database</h2>
            </div>

            <div class="card-body meta-grid">

                <div>Host</div>
                <div><?= htmlspecialchars($database['db_host'] ?? '-') ?></div>

                <div>Name</div>
                <div><?= htmlspecialchars($database['db_name'] ?? '-') ?></div>

                <div>System</div>
                <div><?= htmlspecialchars($database['db_system'] ?? 'MySQL') ?></div>

                <div>Version</div>
                <div><?= htmlspecialchars($database['db_version'] ?? '-') ?></div>

                <div>Latency</div>
                <div>
                    <span class="status-badge status-<?= $database['latency_state'] ?>">
                        <?= htmlspecialchars($database['latency_ms'] ?? '-') ?> ms
                    </span>
                </div>

                <div>Connections</div>
                <div><?= htmlspecialchars($database['threads_connected'] ?? '-') ?></div>

                <?php if (isset($database['db_size'])): ?>
                    <div>Size</div>
                    <div><?= htmlspecialchars($database['db_size']) ?></div>
                <?php endif; ?>

            </div>
        </div>

        <!-- ENVIRONMENT -->
        <div class="card overview-card">
            <div class="card-header">
                <h2>⚙️ Environment</h2>
            </div>

            <div class="card-body meta-grid">

                <div>APP_ENV</div>
                <div><?= htmlspecialchars($environment['app_env'] ?? '-') ?></div>

                <div>Debug</div>
                <div>
                    <span class="status-badge status-<?= ($environment['debug'] ?? false) ? 'warning' : 'ok' ?>">
                        <?= ($environment['debug'] ?? false) ? 'ON' : 'OFF' ?>
                    </span>
                </div>

                <div>PHP</div>
                <div><?= htmlspecialchars($environment['php_version'] ?? '-') ?></div>

                <div>Memory Limit</div>
                <div><?= htmlspecialchars($environment['memory_limit'] ?? '-') ?></div>

                <?php if (isset($environment['disk_free'])): ?>
                    <div>Free Disk</div>
                    <div><?= htmlspecialchars($environment['disk_free']) ?></div>
                <?php endif; ?>

                <?php if (isset($environment['app_size'])): ?>
                    <div>App Size</div>
                    <div><?= htmlspecialchars($environment['app_size']) ?></div>
                <?php endif; ?>

                <?php if (isset($environment['disk_free_space'])): ?>
                    <div>Free Space</div>
                    <div><?= htmlspecialchars($environment['disk_free_space']) ?></div>
                <?php endif; ?>

                <?php if (isset($environment['disk_total_space'])): ?>
                    <div>Total Space</div>
                    <div><?= htmlspecialchars($environment['disk_total_space']) ?></div>
                <?php endif; ?>

                <?php if (isset($environment['disk_free_2_total_space'])): ?>
                    <div>Used Space</div>
                    <div>
                        <span class="status-badge status-<?= $environment['disk_free_2_total_space_state'] ?>">
                            <?= htmlspecialchars($environment['disk_free_2_total_space']) ?>
                        </span>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- GAME -->
        <div class="card overview-card">
            <div class="card-header">
                <h2>🎮 Game</h2>
            </div>

            <div class="card-body meta-grid">

                <div>Engine Health</div>
                <div>
                    <span class="status-badge status-<?= ($game['engine'] ?? false) ? 'ok' : 'fail' ?>">
                        <?= ($game['engine'] ?? false) ? 'OK' : 'FAIL' ?>
                    </span>
                </div>

                <div>API Health</div>
                <div>
                    <span class="status-badge status-<?= ($game['api'] ?? false) ? 'ok' : 'fail' ?>">
                        <?= ($game['api'] ?? false) ? 'OK' : 'FAIL' ?>
                    </span>
                </div>

                <div>API Reachable</div>
                <div>
                    <span class="status-badge status-<?= ($game['reachable'] ?? false) ? 'ok' : 'fail' ?>">
                        <?= ($game['reachable'] ?? false) ? 'OK' : 'FAIL' ?>
                    </span>
                </div>

                <div>Latency</div>
                <div>
                    <span class="status-badge status-<?= $game['latency_state'] ?>">
                        <?= htmlspecialchars($game['latency'] ?? '-') ?> ms
                    </span>
                </div>

                <?php if (isset($game['version'])): ?>
                    <div>Version</div>
                    <div><?= htmlspecialchars($game['version']) ?></div>
                <?php endif; ?>

            </div>
        </div>

    </div>
</div>