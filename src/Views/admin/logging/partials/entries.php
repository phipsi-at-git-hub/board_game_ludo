<?php

use App\Constants\Application;
use App\Core\Csrf;
use App\Core\Localization;

/**
 * Required:
 * @var array $entries
 */

?>

<?php if (empty($entries)): ?>

    <p>
        <?= Localization::get('admin.logging.list.card.no_entries') ?>
    </p>

<?php else: ?>

    <div class="entry-list-cards">

        <?php foreach ($entries as $entry): ?>

            <div class="card entry-row <?= ($entry->getId() !== null) ? '' : 'disabled' ?>">

                <?php if ($entry->getId() !== null): ?>

                    <form
                        action="/admin/logging/show"
                        method="post" >

                        <input
                            type="hidden"
                            name="id"
                            value="<?= htmlspecialchars($entry->getId()) ?>" >

                        <input
                            type="hidden"
                            name="channel"
                            value="<?= htmlspecialchars($entry->getChannel()) ?>" >

                        <input
                            type="hidden"
                            name="timestamp"
                            value="<?= htmlspecialchars($entry->getTimestamp()) ?>" >
            
                        <input
                            type="hidden"
                            name="_csrf_token"
                            value="<?= Csrf::generate() ?>">

                        <button
                            type="submit"
                            class="entry-row-button" >

                <?php endif; ?>

                            <div class="entry-row-header">

                                <div class="entry-row-title">

                                    <?= htmlspecialchars($entry->getMessage()) ?>

                                </div>

                                <div class="entry-row-header-badges">

                                    <span class="status-badge status-default">
                                        <?= strtoupper(htmlspecialchars($entry->getChannel())) ?>
                                    </span>

                                    <span class="status-badge level-<?= htmlspecialchars($entry->getLevel()) ?>">
                                        <?= strtoupper(htmlspecialchars($entry->getLevel())) ?>
                                    </span>

                                </div>

                            </div>

                            <div class="entry-row-footer">

                                <div class="entry-row-badges">

                                    <span class="status-badge status-active">
                                        <?= htmlspecialchars(date(Application::FILE_DATE_FORMAT, strtotime($entry->getTimestamp()))) ?>
                                    </span>

                                    <span class="status-badge status-active">
                                        <?= htmlspecialchars(date(Application::FILE_TIME_FORMAT, strtotime($entry->getTimestamp()))) ?>
                                    </span>

                                    <?php if ($entry->getClass()): ?>

                                        <span class="status-badge status-default case-sensitive">
                                            <?= htmlspecialchars($entry->getClass() . '::' . ($entry->getMethod() ?? '-')) ?>
                                        </span>

                                    <?php endif; ?>

                                </div>

                                <?php if ($entry->getClientIp()): ?>

                                    <div class="entry-row-badges">

                                        <span class="status-badge status-default">
                                            <?= htmlspecialchars($entry->getClientIp()) ?>
                                        </span>

                                    </div>

                                <?php endif; ?>

                            </div>

                <?php if ($entry->getId() !== null): ?>

                        </button>

                    </form>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>
