<?php

use App\Core\Localization;

/**
 * @var Object $game
 * @var array $history
 */
?>

<div class="card">

    <h2>
        <?= Localization::get('admin.games.history.title') ?>
    </h2>

    <div class="game-history">

        <?php if (empty($history)): ?>

            <div class="nested-card">
                <?= Localization::get('admin.games.history.empty') ?>
            </div>

        <?php else: ?>

            <?php foreach ($history as $history_entry): ?>

                <?php
                    $state = $history_entry->getState();

                    $current_player = $state['current_player'] ?? null;
                ?>

                <details class="nested-card game-history-entry">

                    <summary class="game-history-entry-summary">

                        <span class="game-history-entry-index">
                            #<?= $history_entry->getStateIndex() ?>
                        </span>

                        <span class="game-history-entry-timestamp">
                            <?= htmlspecialchars($history_entry->getCreatedAt()) ?>
                        </span>

                        <?php if ($current_player !== null): ?>

                            <span class="game-history-entry-player">
                                <?= htmlspecialchars((string) $current_player) ?>
                            </span>

                        <?php endif; ?>

                    </summary>

                    <div class="game-history-entry-content">

                        <pre><?=
                            htmlspecialchars(
                                json_encode(
                                    $state,
                                    JSON_PRETTY_PRINT
                                    | JSON_UNESCAPED_UNICODE
                                    | JSON_UNESCAPED_SLASHES
                                )
                            )
                        ?></pre>

                    </div>

                </details>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>