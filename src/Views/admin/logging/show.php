<?php

use App\Constants\Application;
use App\Core\Localization;

/**
 * @var Object $entry
 */

$timestamp = strtotime($entry->getTimestamp());

?>

<div class="panel">

    <h1><?= Localization::get('admin.logging.show.title') ?></h1>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li>
                <a href="/admin/logging/list" class="btn-back">
                    <?= Localization::get('application.general.btn.back_to_list') ?>
                </a>
            </li>
        </ul>
    </div>

    <!-- Log Header -->
    <div class="card">

        <div class="card-header">

            <h2>
                <?= htmlspecialchars($entry->getMessage()) ?>
            </h2>

            <div class="entry-row-header-badges">

                <span class="status-badge status-default">
                    <?= strtoupper(htmlspecialchars($entry->getChannel())) ?>
                </span>

                <span class="status-badge level-<?= htmlspecialchars($entry->getLevel()) ?>">
                    <?= strtoupper(htmlspecialchars($entry->getLevel())) ?>
                </span>

            </div>

        </div>

        <div class="form-row">
            <span><?= Localization::get('admin.logging.show.card.overview.date') ?></span>
            <span>
                <?= htmlspecialchars(date(Application::FILE_DATE_FORMAT, $timestamp)) ?>
            </span>
        </div>

        <div class="form-row">
            <span><?= Localization::get('admin.logging.show.card.overview.time') ?></span>
            <span>
                <?= htmlspecialchars(date(Application::FILE_TIME_FORMAT, $timestamp)) ?>
            </span>
        </div>

        <div class="form-row">
            <span><?= Localization::get('admin.logging.show.card.overview.channel') ?></span>
            <span>
                <?= htmlspecialchars($entry->getChannel()) ?>
            </span>
        </div>

        <div class="form-row">
            <span><?= Localization::get('admin.logging.show.card.overview.level') ?></span>
            <span>
                <?= htmlspecialchars($entry->getLevel()) ?>
            </span>
        </div>

    </div>

    <!-- Log Meta / Context -->
    <div class="card">

        <h2>
            <?= Localization::get('admin.logging.show.card.meta.title') ?>
        </h2>

        <div class="nested-card">

            <h3>
                <?= Localization::get('admin.logging.show.card.meta.nested.request.title') ?>
            </h3>

            <div class="form-row">
                <span><?= Localization::get('admin.logging.show.card.meta.nested.request.user_id') ?></span>
                <span><?= htmlspecialchars($entry->getUserId() ?? '-') ?></span>
            </div>

            <div class="form-row">
                <span><?= Localization::get('admin.logging.show.card.meta.nested.request.session_id') ?></span>
                <span><?= htmlspecialchars($entry->getSessionId() ?? '-') ?></span>
            </div>

            <div class="form-row">
                <span><?= Localization::get('admin.logging.show.card.meta.nested.request.ip') ?></span>
                <span><?= htmlspecialchars($entry->getClientIp() ?? '-') ?></span>
            </div>

            <div class="form-row">
                <span><?= Localization::get('admin.logging.show.card.meta.nested.request.request_id') ?></span>
                <span><?= htmlspecialchars($entry->getRequestId() ?? '-') ?></span>
            </div>

            <div class="form-row">
                <span><?= Localization::get('admin.logging.show.card.meta.nested.request.request_method') ?></span>
                <span><?= htmlspecialchars($entry->getRequestMethod() ?? '-') ?></span>
            </div>

            <div class="form-row">
                <span><?= Localization::get('admin.logging.show.card.meta.nested.request.request_uri') ?></span>
                <span class="case-sensitive">
                    <?= htmlspecialchars($entry->getRequestUri() ?? '-') ?>
                </span>
            </div>

            <div class="form-row">
                <span><?= Localization::get('admin.logging.show.card.meta.nested.request.runtime') ?></span>
                <span><?= htmlspecialchars($entry->getRuntime() ?? '-') ?></span>
            </div>

        </div>

        <div class="nested-card">

            <h3>
                <?= Localization::get('admin.logging.show.card.meta.nested.caller.title') ?>
            </h3>

            <div class="form-row">
                <span><?= Localization::get('admin.logging.show.card.meta.nested.caller.class') ?></span>
                <span class="case-sensitive">
                    <?= htmlspecialchars($entry->getClass() ?? '-') ?>
                </span>
            </div>

            <div class="form-row">
                <span><?= Localization::get('admin.logging.show.card.meta.nested.caller.method') ?></span>
                <span class="case-sensitive">
                    <?= htmlspecialchars($entry->getMethod() ?? '-') ?>
                </span>
            </div>

            <div class="form-row">
                <span><?= Localization::get('admin.logging.show.card.meta.nested.caller.file') ?></span>
                <span class="case-sensitive">
                    <?= htmlspecialchars($entry->getFile() ?? '-') ?>
                </span>
            </div>

            <div class="form-row">
                <span><?= Localization::get('admin.logging.show.card.meta.nested.caller.line') ?></span>
                <span>
                    <?= htmlspecialchars(
                        $entry->getLine() !== null
                            ? (string)$entry->getLine()
                            : '-'
                    ) ?>
                </span>
            </div>

        </div>

        <?php if (!empty($entry->getContext())): ?>

            <div class="nested-card">

                <h3>
                    <?= Localization::get('admin.logging.show.card.meta.nested.context.title') ?>
                </h3>

                <pre class="log-context"><?= htmlspecialchars(
                    json_encode(
                        $entry->getContext(),
                        JSON_PRETTY_PRINT
                        | JSON_UNESCAPED_UNICODE
                        | JSON_UNESCAPED_SLASHES
                    )
                ) ?></pre>

            </div>

        <?php endif; ?>

    </div>

    <!-- Log Entry -->
    <div class="card">

        <h2>
            <?= Localization::get('admin.logging.show.card.entry.title') ?>
        </h2>

        <div class="nested-card">

            <h3>
                <?= Localization::get('admin.logging.show.card.entry.nested.message.title') ?>
            </h3>

            <div class="log-message">
                <?= nl2br(htmlspecialchars($entry->getMessage())) ?>
            </div>

        </div>

    </div>

    <!-- Original Log Entry -->
    <div class="card">

        <div class="card-header">

            <h2>
                <?= Localization::get('admin.logging.show.card.original.title') ?>
            </h2>

        </div>

        <div id="original-log-entry" class="nested-card entry-raw">

            <pre class="log-entry-original"><?= htmlspecialchars($entry->toPrettyString()) ?></pre>

        </div>

    </div>

</div>
