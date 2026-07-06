<?php

use App\Core\Csrf;
use App\Core\Localization;

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

            $is_owner = $game->getCreatedByUserId() === $current_user->getId();
            $is_admin = $current_user->isAdmin();

            $can_edit =
                ($is_owner && $game->isWaiting()) ||
                ($is_admin && $game->isWaiting());

            $can_delete =
                ($is_owner && $game->isWaiting()) ||
                $is_admin;

            $can_join =
                $game->isWaiting() &&
                !$game->isLocked() &&
                !$game->isPrivate();

            if (
                $game->isPrivate() &&
                !$is_owner &&
                !$game->isRunning()
            ) {
                continue;
            }

            $player_count = (int)$game->getPlayerCount();
            $player_max = (int)$game->getPlayerMax();

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
                $players_class = 'status-fail';
            } elseif ($player_count === 1) {
                $players_class = 'status-warning';
            } else {
                $players_class = 'status-ok';
            }

            $ruleset_text =
                $game->getRuleSetModel()->isGameClassic()
                    ? Localization::get('game.ruleset.classic')
                    : Localization::get('game.ruleset.custom');

            ?>

            <div
                class="card game-row"
                onclick="window.location='/game/detail/<?= $game->getId() ?>'">

                <div class="game-row-header">

                    <div class="game-row-title">
                        <?= htmlspecialchars($game->getName()) ?>
                    </div>

                    <span class="status-badge <?= $status_class ?>">
                        <?= strtoupper($status_text) ?>
                    </span>

                </div>

                <div class="game-row-footer">

                    <div class="game-row-badges">

                        <span class="status-badge <?= $players_class ?>">
                            <?= $player_count ?>/<?= $player_max ?>
                        </span>

                        <span class="status-badge status-active">
                            <?= strtoupper($ruleset_text) ?>
                        </span>

                        <span class="status-badge <?= $game->isPrivate() ? 'status-warning' : 'status-ok' ?>">
                            <?= strtoupper(
                                $game->isPrivate()
                                    ? Localization::get('application.general.label.private')
                                    : Localization::get('application.general.label.public')
                            ) ?>
                        </span>

                        <span class="status-badge <?= $game->isLocked() ? 'status-fail' : 'status-ok' ?>">
                            <?= strtoupper(
                                $game->isLocked()
                                    ? Localization::get('application.general.label.locked')
                                    : Localization::get('application.general.label.open')
                            ) ?>
                        </span>

                        <?php if ($is_owner): ?>
                            <span class="role-badge role-admin">
                                <?= strtoupper(Localization::get('application.general.label.owner')) ?>
                            </span>
                        <?php endif; ?>

                    </div>

                    <div
                        class="btn-actions"
                        onclick="event.stopPropagation();">

                        <?php if ($can_join): ?>
                            <form
                                method="POST"
                                action="/api/game/join/<?= $game->getId() ?>">

                                <input
                                    type="hidden"
                                    name="_csrf_token"
                                    value="<?= Csrf::generate() ?>">

                                <button
                                    type="submit"
                                    class="btn btn-save">

                                    <?= Localization::get('application.general.label.join') ?>

                                </button>

                            </form>
                        <?php endif; ?>

                        <?php if ($can_edit): ?>
                            <a
                                href="/game/edit/<?= $game->getId() ?>"
                                class="btn btn-secondary">

                                <?= Localization::get('application.general.label.edit') ?>

                            </a>
                        <?php endif; ?>

                        <?php if ($can_delete): ?>
                            <form
                                method="POST"
                                action="/game/delete"
                                onsubmit="return confirm('<?= Localization::get('game.list.delete_confirm') ?>');">

                                <input
                                    type="hidden"
                                    name="_method"
                                    value="DELETE">

                                <input
                                    type="hidden"
                                    name="_csrf_token"
                                    value="<?= Csrf::generate() ?>">

                                <input
                                    type="hidden"
                                    name="game_id"
                                    value="<?= $game->getId() ?>">

                                <button
                                    type="submit"
                                    class="btn btn-danger">

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