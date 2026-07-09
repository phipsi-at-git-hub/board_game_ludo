<?php

use App\Constants\Application;
use App\Core\Csrf;
use App\Core\Localization;
use App\Policies\GamePolicy;

/**
 * @var array $games
 * @var Object $current_user
 */
?>

<div class="panel">

    <h1><?= Localization::get('game.list.title') ?></h1>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li>
                <a href="/lobby" class="btn-back">
                    <?= Localization::get('application.general.btn.back_to_lobby') ?>
                </a>
            </li>
        </ul>
    </div>

    <div class="game-list-cards">

        <?php foreach ($games as $game): ?>

            <?php

            $is_owner = GamePolicy::isOwner($game, $current_user);
            $is_admin = $current_user->isAdmin();

            $can_edit = GamePolicy::canEdit($game, $current_user);
            $can_delete = GamePolicy::canDelete($game, $current_user);

            $can_join = GamePolicy::canJoin($game, $current_user);
            $can_leave = GamePolicy::canLeave($game, $current_user);

            if (
                $game->isPrivate()
                && !$is_owner
                && !$game->isRunning()
            ) {
                continue;
            }

            $player_count = $game->getPlayerCount();
            $player_max = $game->getPlayerMax();

            if ($game->isWaiting()) {
                $status_class = 'status-waiting';
                $status_text = Localization::get('game.status.waiting');
            } elseif ($game->isRunning()) {
                $status_class = 'status-running';
                $status_text = Localization::get('game.status.running');
            } else {
                $status_class = 'status-finished';
                $status_text = Localization::get('game.status.finished');
            }

            if ($player_count === 0) {
                $players_class = 'player-count-category-' . Application::DTO_PLAYER_COUNT_EMPTY;
            } elseif ($player_count === 1) {
                $players_class = 'player-count-category-' . Application::DTO_PLAYER_COUNT_LOW;
            } else {
                $players_class = 'player-count-category-' . Application::DTO_PLAYER_COUNT_READY;
            }

            $ruleset_text = $game->getRuleSetModel()->isGameClassic() ? Localization::get('game.ruleset.classic') : Localization::get('game.ruleset.custom');?>

            <div
                class="card game-row"
                data-id="<?= $game->getId() ?>" 
                data-action-behavior="remove" 
                onclick="window.location='/game/detail/<?= $game->getId() ?>'">

                <div class="game-row-header">

                    <div class="game-row-title">
                        <?= htmlspecialchars($game->getName()) ?>
                    </div>

                    <span
                        class="status-badge <?= $status_class ?>"
                        data-bind="status">
                        <?= strtoupper($status_text) ?>
                    </span>

                </div>

                <div class="game-row-footer">

                    <div class="game-row-badges">

                        <span
                            class="status-badge <?= $players_class ?>"
                            data-bind="player_count" 
                            data-class-bind="player_count_category" >
                            <?= $player_count ?>/<?= $player_max ?>
                        </span>

                        <span
                            class="status-badge status-active"
                            data-bind="ruleset" >
                            <?= strtoupper($ruleset_text) ?>
                        </span>

                        <span
                            class="status-badge <?= $game->isPrivate() ? 'status-warning' : 'status-ok' ?>"
                            data-bind="is_private_label" >
                            <?= strtoupper($game->isPrivate() ? Localization::get('application.general.label.private') : Localization::get('application.general.label.public')) ?>
                        </span>

                        <span
                            class="status-badge <?= $game->isLocked() ? 'status-fail' : 'status-ok' ?>"
                            data-bind="is_locked_label" >
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

                    <div
                        class="btn-actions"
                        onclick="event.stopPropagation();">

                        <form
                            method="POST"
                            action="/api/game/join/<?= $game->getId() ?>"
                            data-action-container="join" 
                                data-response="json" 
                            <?= $can_join ? '' : 'hidden' ?> >

                            <input
                                type="hidden"
                                name="_csrf_token"
                                value="<?= Csrf::generate() ?>" >

                            <button
                                type="submit"
                                class="btn btn-save"
                                data-action="submit" >
                                <?= Localization::get('application.general.label.join') ?>
                            </button>

                        </form>

                        <form
                            method="POST"
                            action="/api/game/leave/<?= $game->getId() ?>"
                            data-action-container="leave" 
                                data-response="json" 
                            <?= $can_leave ? '' : 'hidden' ?> >

                            <input
                                type="hidden"
                                name="_csrf_token"
                                value="<?= Csrf::generate() ?>" >

                            <button
                                type="submit"
                                class="btn btn-save"
                                data-action="submit" >
                                <?= Localization::get('application.general.label.leave') ?>
                            </button>

                        </form>

                        <?php if ($can_edit): ?>

                            <a
                                href="/game/edit/<?= $game->getId() ?>"
                                class="btn btn-secondary" >
                                <?= Localization::get('application.general.label.edit') ?>
                            </a>

                        <?php endif; ?>

                        <?php if ($can_delete): ?>

                            <form
                                method="POST"
                                action="/api/game/delete" 
                                data-action="delete" 
                                data-action-target-id="<?= $game->getId() ?>" 
                                data-action-container="delete" 
                                data-response="json" 
                                data-confirm 
                                data-confirm-title="<?= Localization::get('application.modal.messages.game.delete.title') ?>" 
                                data-confirm-message="<?= Localization::get('application.modal.messages.game.delete.confirm') ?>" 
                                >

                                <input
                                    type="hidden"
                                    name="_method"
                                    value="DELETE" >

                                <input
                                    type="hidden"
                                    name="_csrf_token"
                                    value="<?= Csrf::generate() ?>" >

                                <input
                                    type="hidden"
                                    name="game_id"
                                    value="<?= $game->getId() ?>" >

                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                    data-action="submit" >
                                    <?= Localization::get('application.general.label.delete') ?>
                                </button>

                            </form>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>