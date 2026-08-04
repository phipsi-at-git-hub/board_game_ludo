<?php

use App\Core\Localization;
use App\Core\Logging\LogEntry;

/**
 * @var LogEntry[] $entries
 * @var string $channel
 * @var string $date
 * @var array $statistics
 */

?>

<div class="panel">

    <h1><?= Localization::get('admin.logging.list.title') ?></h1>

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

        <h2><?= Localization::get('admin.logging.list.card.overview.title') ?></h2>

        <div class="stats-sub">

            <div>
                <span class="stat-value">
                    <?= htmlspecialchars($channel) ?>
                </span>
                <span class="stat-text">
                    <?= Localization::get('admin.logging.list.card.overview.channel') ?>
                </span>
            </div>

            <div>
                <span class="stat-value">
                    <?= htmlspecialchars($date) ?>
                </span>
                <span class="stat-text">
                    <?= Localization::get('admin.logging.list.card.overview.date') ?>
                </span>
            </div>

            <div>
                <span class="stat-value">
                    <?= $statistics['total'] ?>
                </span>
                <span class="stat-text">
                    <?= Localization::get('admin.logging.list.card.overview.entries') ?>
                </span>
            </div>

            <div>
                <span class="stat-value">
                    <?= strtoupper($statistics['highest_level'] ?? '-') ?>
                </span>
                <span class="stat-text">
                    <?= Localization::get('admin.logging.list.card.overview.highest_level') ?>
                </span>
            </div>

        </div>

    </div>

    <!-- Statistics -->
    <div class="card">

        <h2><?= Localization::get('admin.logging.list.card.statistics.title') ?></h2>

        <div class="stats-sub">

            <?php foreach ($statistics as $level => $count): ?>

                <?php
                if (in_array($level, ['total', 'highest_level'], true)) {
                    continue;
                }
                ?>

                <div>

                    <span class="stat-value">
                        <?= $count ?>
                    </span>

                    <span class="stat-text">
                        <?= strtoupper($level) ?>
                    </span>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

    <!-- Entries -->
    <?php 
    include VIEWS_PATH . '/admin/logging/partials/entries.php';   
    ?>

</div>