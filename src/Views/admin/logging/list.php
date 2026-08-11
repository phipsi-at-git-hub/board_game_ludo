<?php

use App\Constants\Application;
use App\Core\Csrf;
use App\Core\Localization;
use App\Core\Logging\LogEntry;

/**
 * @var array $available_channels
 * @var array $channels 
 * @var DateTimeImmutable $date_start
 * @var DateTimeImmutable $date_end
 * @var String $date_range 
 * @var LogEntry[] $entries
 * @var array $statistics
 */

?>

<div class="panel">

    <h1>
        <?= Localization::get('admin.logging.list.title') ?>
    </h1>

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

    <!-- Log Levels -->
    <div class="card">

        <h2>
            <?= Localization::get('admin.logging.list.card.levels.title') ?>
        </h2>

        <div class="stats-sub">

            <?php foreach ($statistics as $level => $count): ?>

                <?php if (!is_int($count) || in_array($level, ['total', 'highest_level'])): ?>
                    <?php continue; ?>
                <?php endif; ?>

                <div>

                    <span class="stat-value">

                        <?php if ($count > 0): ?>

                            <span class="status-badge level-<?= htmlspecialchars($level) ?>">
                                <?= $count ?>
                            </span>

                        <?php else: ?>

                            <span class="status-badge status-default">
                                <?= $count ?>
                            </span>

                        <?php endif; ?>

                    </span>

                    <span class="stat-text">
                        <?= strtoupper(htmlspecialchars($level)) ?>
                    </span>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

    <!-- Entries -->
    <div class="card">

        <h2>
            <?= Localization::get('admin.logging.list.card.entries.title') ?>
        </h2>

        <!-- Entry Filter -->
        <div class="nested-card">
            <form
                data-id="logging-form" 
                method="post" 
                action="/api/admin/logging/filter" 
                
                data-response="json" 
                data-bind-targets="logging-filter-entries, logging-entry-count" >
            
                <input
                type="hidden"
                name="_csrf_token"
                value="<?= Csrf::generate() ?>">

                <div class="nested-card-header">

                    <h3>
                        <?= Localization::get('admin.logging.list.card.filter.title') ?>
                    </h3>

                    <span 
                        class="status-badge status-default" 
                        data-id="logging-entry-count" 
                        data-bind-sources="logging-form" 
                        data-bind-1-type="text" 
                        data-bind-1-dto-key="entries_count" >
                        <?= count($entries) ?>
                        <?= Localization::get('admin.logging.list.card.entries.count') ?>
                    </span>

                </div>

                <div class="entry-filter-content">

                    <!-- Channel selection -->
                    <!--<div class="entry-filter-group">-->
                    <div class="form-row">

                        <span>
                            <?= Localization::get('admin.logging.list.card.filter.channel') ?>
                        </span>

                        <select
                            name="channels[]" 
                            multiple 
                            data-ui="badge-multiselect" 
                            data-min-selection="1" 
                            data-label-plural="<?= strtoupper(Localization::get('application.general.selected')) ?>" >

                            <?php foreach ($available_channels as $channel): ?>

                                <option 
                                    value="<?= htmlspecialchars($channel) ?>" 
                                    <?= in_array($channel, $channels, true) ? 'selected' : '' ?> >

                                    <?= strtoupper(htmlspecialchars($channel)) ?>

                                </option>
                            <?php endforeach; ?>

                        </select>

                    </div>

                    <!-- Date range selection -->
                    <!--<div class="entry-filter-group">-->
                    <div class="form-row">

                        <span>
                            <?= Localization::get('admin.logging.list.card.filter.date_range') ?>
                        </span>

                        <input
                            type="text" 
                            name="date_range" 
                            data-ui="date-range" 
                            data-ui-localization="en-us" 
                            data-ui-with-time="true" 
                            value="<?= htmlspecialchars($date_range) ?>" > 

                        <!-- Date Range Picker will be inserted here -->

                    </div>

                    <div class="form-row">

                        <span></span>

                        <button type="submit" class="btn btn-actions btn-date-range-apply" >
                            Use Filter
                        </button>

                    </div>

                </div>

            </form>

        </div>

        <div 
            data-id="logging-filter-entries" 
            data-bind-sources="logging-form" 
            data-bind-1-view-key="entries" 
            data-bind-1-type="view" >

            <?php include VIEWS_PATH . '/admin/logging/partials/entries.php'; ?>

        </div>

    </div>

</div>
