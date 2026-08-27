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

        <form
            data-id="logging-level-form" 
            method="post" 
            action="/api/admin/logging/filter" 
            
            data-response="json" 
            data-bind-targets="
                logging-filter-entries, 
                logging-entry-count, 
            " >
            
            <input
                type="hidden"
                name="_csrf_token"
                value="<?= Csrf::generate() ?>">

            <!-- Hidden filter -->

            <!-- Hidden filter channels -->
            <?php $index = 0; ?>
            <?php foreach ($available_channels as $channel): ?>
            
                <input 
                    type="hidden" 
                    name="channels[]" 
                    data-id="applied-filter-channels-<?= htmlspecialchars($channel) ?>" 
                    data-bind-1-type="attribute" 
                    data-bind-1-dto-key="channels.<?= htmlspecialchars($index) ?>" 
                    data-bind-1-dto-key-loose="true" 
                    data-bind-1-attribute="value" 
                    data-bind-sources="logging-filter-form" 
                    value="<?= htmlspecialchars($channel) ?>" >
                
                <?php $index++; ?>

            <?php endforeach; ?>

            <!-- Hidden filter date_range -->
            <input 
                type="hidden" 
                name="date_range" 
                data-id="applied-filter-date_range" 
                data-bind-1-type="attribute" 
                data-bind-1-dto-key="date_range" 
                data-bind-1-attribute="value" 
                data-bind-sources="logging-filter-form" 
                value="<?= htmlspecialchars($date_range) ?>" >

            <div class="stats-sub">

                <?php foreach ($statistics as $level => $count): ?>

                    <?php if (!is_int($count) || in_array($level, ['total', 'highest_level'])): ?>
                        <?php continue; ?>
                    <?php endif; ?>

                    <div>

                        <span class="stat-value" >

                            <?php if ($count > 0): ?>

                                <button class="btn btn-badge level-<?= htmlspecialchars($level) ?>" 
                                    type="submit" 
                                    name="log_levels[]" 
                                    value="<?= htmlspecialchars($level) ?>" 
                                    data-id="logging-level-<?= htmlspecialchars($level) ?>" 
                                    data-bind-sources="logging-filter-form, logging-level-form" 
                                    data-bind-1-type="text" 
                                    data-bind-1-dto-key="statistics.<?= htmlspecialchars($level) ?>_label" 
                                    data-bind-2-type="class" 
                                    data-bind-2-dto-key="statistics.<?= htmlspecialchars($level) ?>_classes" 
                                    data-bind-2-classes-fixed="btn, btn-badge" 
                                    data-bind-3-type="attribute" 
                                    data-bind-3-dto-key="statistics.<?= htmlspecialchars($level) ?>_disabled" 
                                    data-bind-3-attribute="disabled" 
                                    <?= ($count > 0) ? '' : 'disabled' ?> >
                                    <?= $count ?>
                            </button>

                            <?php else: ?>

                                <button class="btn btn-badge status-default" 
                                    type="submit" 
                                    name="log_levels[]" 
                                    value="<?= htmlspecialchars($level) ?>" 
                                    data-id="logging-level-<?= htmlspecialchars($level) ?>" 
                                    data-bind-sources="logging-filter-form, logging-level-form" 
                                    data-bind-1-type="text" 
                                    data-bind-1-dto-key="statistics.<?= htmlspecialchars($level) ?>_label" 
                                    data-bind-2-type="class" 
                                    data-bind-2-dto-key="statistics.<?= htmlspecialchars($level) ?>_classes" 
                                    data-bind-2-classes-fixed="btn, btn-badge" 
                                    data-bind-3-type="attribute" 
                                    data-bind-3-dto-key="statistics.<?= htmlspecialchars($level) ?>_disabled" 
                                    data-bind-3-attribute="disabled" 
                                    <?= ($count > 0) ? '' : 'disabled' ?> >
                                    <?= $count ?>
                                </button>
                                
                            <?php endif; ?>

                        </span>

                        <span class="stat-text">
                            <?= strtoupper(htmlspecialchars($level)) ?>
                        </span>

                    </div>

                <?php endforeach; ?>

            </div>

        </form>

    </div>

    <!-- Entries -->
    <div class="card">

        <h2>
            <?= Localization::get('admin.logging.list.card.entries.title') ?>
        </h2>

        <!-- Entry Filter -->
        <div class="nested-card">
            <form
                data-id="logging-filter-form" 
                method="post" 
                action="/api/admin/logging/filter" 
                
                data-response="json" 
                data-bind-targets="
                    logging-filter-entries, 
                    logging-entry-count, 
                    <?php foreach ($statistics as $level => $count): ?>
                        <?php if (!is_int($count) || in_array($level, ['total', 'highest_level'])): ?>
                            <?php continue; ?>
                        <?php endif; ?>
                        logging-level-<?= htmlspecialchars($level) ?>, 
                    <?php endforeach; ?>

                    <?php foreach ($available_channels as $channel): ?>
                        applied-filter-channels-<?= $channel ?>, 
                    <?php endforeach; ?>
                    applied-filter-date_range, 
                " >
            
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
                        data-bind-sources="logging-filter-form, logging-level-form" 
                        data-bind-1-type="text" 
                        data-bind-1-dto-key="entries_count" >
                        <?= count($entries) ?>
                    </span>

                </div>

                <div class="entry-filter-content">

                    <!-- Channel selection -->
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
                    <div class="form-row">

                        <span>
                            <?= Localization::get('admin.logging.list.card.filter.date_range') ?>
                        </span>

                        <input
                            type="text" 
                            name="date_range" 
                            data-ui="date-range" 
                            data-ui-localization="<?= Application::EN_US ?>" 
                            data-ui-with-time="true" 
                            value="<?= htmlspecialchars($date_range) ?>" > 

                        <!-- Date Range Picker will be inserted here -->

                    </div>

                    <div class="form-row">

                        <span></span>

                        <button 
                            type="submit" 
                            class="btn btn-actions btn-filter-apply" >

                            <?= Localization::get('application.general.btn.apply_filter') ?>
                            
                        </button>

                    </div>

                </div>

            </form>

        </div>

        <div 
            data-id="logging-filter-entries" 
            data-bind-sources="logging-filter-form, logging-level-form" 
            data-bind-1-view-key="entries" 
            data-bind-1-type="view" >

            <?php include VIEWS_PATH . '/admin/logging/partials/entries.php'; ?>

        </div>

    </div>

</div>
