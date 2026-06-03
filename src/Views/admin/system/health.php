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
            <li><a href="/lobby" class="btn-back"><?= Localization::get('application.general.btn.back_to_lobby') ?></a></li>
            <li><a href="/admin" class="btn-back"><?= Localization::get('application.general.btn.back_to_dashboard') ?></a></li>
        </ul>
    </div>

    <div class="card system-health-card">

        <!-- HEADER -->
        <div class="card-header">
            <h2>System Health</h2>

            <div class="status-badges">
                <span class="status-badge status-<?= strtolower($overall) ?>"><?= strtoupper($overall) ?></span>
            </div>
        </div>

        <div class="card-body meta-grid">

            <!-- DATABASE -->
            <div class="section-title"><?= Localization::get('admin.system_health.database') ?></div>
            <div class="section-value">
                <span class="status-badge status-<?= strtolower($database['status']) ?>"><?= strtoupper($database['status']) ?></span>
            </div>

            <div><?= Localization::get('admin.system_health.db_time') ?></div>
            <div><?= $database['latency_ms'] ?> ms</div>

            <div><?= Localization::get('admin.system_health.db_connections') ?></div>
            <div><?= $database['connections_ok'] ? 'OK' : 'FAIL' ?></div>


            <!-- ENVIRONMENT -->
            <div class="section-title"><?= Localization::get('admin.system_health.environment') ?></div>
            <div class="section-value">
                <span class="status-badge status-<?= strtolower($environment['status']) ?>">
                    <?= strtoupper($environment['status']) ?>
                </span>
            </div>

            <div>App Env</div>
            <div><?= $environment['app_env'] ?></div>

            <div>Debug</div>
            <div><?= $environment['debug'] ? 'ON' : 'OFF' ?></div>


            <!-- GAME -->
            <div class="section-title"><?= Localization::get('admin.system_health.game') ?></div>
            <div class="section-value">
                <span class="status-badge status-<?= ($game['status_ok']) ? 'ok' : 'fail' ?>">
                    <?= ($game['status_ok']) ? strtoupper('ok') : strtoupper('fail') ?>
                </span>
            </div>

            <div>API</div>
            <div><?= ($game['api']) ? strtoupper('ok') : strtoupper('fail') ?></div>

            <div>Engine</div>
            <div><?= ($game['engine']) ? strtoupper('ok') : strtoupper('fail') ?></div>

        </div>
    </div>
</div>