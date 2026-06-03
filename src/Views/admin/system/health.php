<?php
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

    <!-- Overall -->
    <div class="card">
        <div class="card-header">
            <h2>🩺 System Health</h2>

            <div class="status-badges">
                <span class="status-badge status-<?= strtolower($overall) ?>">
                    <?= strtoupper($overall) ?>
                </span>
            </div>
        </div>
    </div>

    <div class="health-grid">

        <!-- DATABASE -->
        <div class="card">
            <div class="card-header">
                <h2>🗄 Database</h2>

                <div class="status-badges">
                    <span class="status-badge status-<?= strtolower($database['status']) ?>">
                        <?= strtoupper($database['status']) ?>
                    </span>
                </div>
            </div>

            <div class="card-body meta-grid">
                <div>Database</div>
                <div><?= htmlspecialchars($database['db_name']) ?></div>

                <div>Latency</div>
                <div><?= $database['latency_ms'] ?> ms</div>

                <div>Connection</div>
                <div>
                    <span class="status-badge status-<?= $database['connections_ok'] ? 'ok' : 'fail' ?>">
                        <?= $database['connections_ok'] ? 'OK' : 'FAIL' ?>
                    </span>
                </div>

                <div>Version</div>
                <div><?= htmlspecialchars($database['db_version']) ?></div>
            </div>
        </div>

        <!-- ENVIRONMENT -->
        <div class="card">
            <div class="card-header">
                <h2>⚙️ Environment</h2>

                <div class="status-badges">
                    <span class="status-badge status-<?= strtolower($environment['status']) ?>">
                        <?= strtoupper($environment['status']) ?>
                    </span>
                </div>
            </div>

            <div class="card-body meta-grid">
                <div>APP_ENV</div>
                <div><?= htmlspecialchars($environment['app_env']) ?></div>

                <div>Debug</div>
                <div>
                    <span class="status-badge status-<?= $environment['debug'] ? 'warning' : 'ok' ?>">
                        <?= $environment['debug'] ? 'ON' : 'OFF' ?>
                    </span>
                </div>

                <div>PHP</div>
                <div><?= htmlspecialchars($environment['php_version']) ?></div>

                <div>Memory Limit</div>
                <div><?= htmlspecialchars($environment['memory_limit']) ?></div>
            </div>
        </div>

        <!-- GAME -->
        <div class="card">
            <div class="card-header">
                <h2>🎮 Game</h2>

                <div class="status-badges">
                    <span class="status-badge status-<?= ($game['status_ok']) ? 'ok' : 'fail' ?>">
                        <?= ($game['status_ok']) ? 'ok' : 'fail' ?>
                    </span>
                </div>
            </div>

            <div class="card-body">

                <div class="meta-grid">
                    <div>API Health</div>
                    <div>
                        <span class="status-badge status-<?= $game['api'] ? 'ok' : 'fail' ?>">
                            <?= $game['api'] ? 'OK' : 'FAIL' ?>
                        </span>
                    </div>

                    <div>API Latency</div>
                    <div><?= $game['latency'] ?> ms</div>

                    <div>Engine</div>
                    <div>
                        <span class="status-badge status-<?= $game['engine'] ? 'ok' : 'fail' ?>">
                            <?= $game['engine'] ? 'OK' : 'FAIL' ?>
                        </span>
                    </div>
                </div>

                <hr>

                <h3>API Resources</h3>

                <div class="meta-grid">

                    <?php foreach ($game['resources'] as $resource => $status): ?>
                        <div><?= htmlspecialchars($resource) ?></div>

                        <div>
                            <span class="status-badge status-<?= $status ? 'ok' : 'fail' ?>">
                                <?= $status ? 'OK' : 'FAIL' ?>
                            </span>
                        </div>
                    <?php endforeach; ?>

                </div>

            </div>
        </div>

    </div>
</div>