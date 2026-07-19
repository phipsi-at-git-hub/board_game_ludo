<?php

use App\Core\Localization;

/**
 * Required:
 * @var object $game
 * @var object $current_user
 * @var string $status_class
 * @var string $status_text
 * @var string $players_class
 * @var int $player_count
 * @var int $player_max
 * @var string $ruleset_text
 * @var bool $is_owner
 */
?>

<div class="game-row-badges">

    <!--<span
        class="status-badge <?= $players_class ?>"
        data-bind="player_count"
        data-class-bind="player_count_category"> -->
    <span 
        class="status-badge <?= $players_class ?>" 
        data-id="game-<?= $game->getId() ?>-player-count" 
        data-bind-sources="game-<?= $game->getId() ?>-join, game-<?= $game->getId() ?>-leave" 
        data-bind-1-dto-key="player_count_label" 
        data-bind-1-type="text" 
        data-bind-2-dto-key="player_count_category" 
        data-bind-2-type="class" 
        data-bind-2-classes-fixed="status-badge" >
        <?= $player_count ?>/<?= $player_max ?>
    </span>

    <span
        class="status-badge status-active"
        data-bind="ruleset">
        <?= strtoupper($ruleset_text) ?>
    </span>

    <span
        class="status-badge <?= $game->isPrivate() ? 'status-warning' : 'status-ok' ?>"
        data-bind="is_private_label">
        <?= strtoupper($game->isPrivate() ? Localization::get('application.general.label.private') : Localization::get('application.general.label.public')) ?>
    </span>

    <span
        class="status-badge <?= $game->isLocked() ? 'status-fail' : 'status-ok' ?>"
        data-bind="is_locked_label">
        <?= strtoupper($game->isLocked() ? Localization::get('application.general.label.locked') : Localization::get('application.general.label.open')) ?>
    </span>

    <?php if ($is_owner): ?>

        <span
            class="role-badge role-admin"
            data-bind="is_owner_label">
            <?= strtoupper(Localization::get('application.general.label.owner')) ?>
        </span>

    <?php endif; ?>

</div>
