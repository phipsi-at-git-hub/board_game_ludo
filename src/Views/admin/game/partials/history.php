<?php

use App\Core\Localization;

/**
 * @var Object $game
 * @var array $history
 */
?>

<div class="card">

    <h2>
        <?= Localization::get('admin.game.card.history.title') ?>
    </h2>

    <?php if (empty($history)): ?>

        <div class="nested-card history-empty">
            <?= Localization::get('admin.logging.list.card.empty') ?>
        </div>

    <?php else: ?>

        <div class="collapsible-list">

            <?php foreach ($history as $history_entry): ?>

                <?php
                $state = $history_entry->getState();

                $state_index = $history_entry->getStateIndex();
                $created_at = $history_entry->getCreatedAt();

                $current_player_username = $state['current_player_username'] ?? null;

                $game_status = $state['game_status'] ?? null;

                $player_count = isset($state['players']) && is_array($state['players']) ? count($state['players']) : 0;
                ?>

                <div class="nested-card collapsible-item">

                    <button
                        type="button"
                        class="collapsible-header"
                        aria-expanded="false">

                        <span class="collapsible-header-index">
                            #<?= (int) $state_index ?>
                        </span>

                        <span class="collapsible-header-timestamp">
                            <?= htmlspecialchars($created_at) ?>
                        </span>

                        <span class="collapsible-header-player">
                            <?= htmlspecialchars($current_player_username ?? '—') ?>
                        </span>

                        <?php if ($game_status !== null): ?>
                            <span class="status-badge status-<?= strtolower(htmlspecialchars($game_status)) ?>">
                                <?= htmlspecialchars($game_status) ?>
                            </span>
                        <?php endif; ?>

                        <span
                            class="collapsible-header-icon"
                            aria-hidden="true">
                            ›
                        </span>

                    </button>

                    <div class="collapsible-content">

                        <div class="collapsible-content-inner">

                            <div class="history-summary">

                                <div class="form-row">
                                    <span>
                                        <?= Localization::get('admin.game.card.history.entry.state') ?>
                                    </span>

                                    <span>
                                        #<?= (int) $state_index ?>
                                    </span>
                                </div>

                                <div class="form-row">
                                    <span>
                                        <?= Localization::get('admin.game.card.history.entry.timestamp') ?>
                                    </span>

                                    <span>
                                        <?= htmlspecialchars($created_at) ?>
                                    </span>
                                </div>

                                <div class="form-row">
                                    <span>
                                        <?= Localization::get('admin.game.card.history.entry.current_player') ?>
                                    </span>

                                    <span>
                                        <?= htmlspecialchars($current_player_username ?? '—') ?>
                                    </span>
                                </div>

                                <div class="form-row">
                                    <span>
                                        <?= Localization::get('admin.game.card.history.entry.players') ?>
                                    </span>

                                    <span>
                                        <?= (int) $player_count ?>
                                    </span>
                                </div>

                            </div>

                            <pre class="history-state"><?=
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

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>
