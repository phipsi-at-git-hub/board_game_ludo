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
        <ul class="nav-list">
            <li><a href="/lobby" class="btn-back"><?= Localization::get('application.general.btn.back_to_lobby') ?></a></li>
        </ul>
    </div>

    <table class="game-list">
        <thead>
            <tr>
                <th class="name"><?= Localization::get('game.list.name') ?></th>
                <th class="players"><?= Localization::get('game.list.players') ?></th>
                <th class="type"><?= Localization::get('game.list.type') ?></th>
                <th class="options"><?= Localization::get('game.list.options') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($games as $game): ?>
                <?php 
                    $is_owner = $game->getCreatedByUserId() === $current_user->getId();
                    $is_admin = $current_user->isAdmin();

                    $can_edit = ($is_owner && $game->isWaiting()) || ($is_admin && $game->isWaiting());
                    $can_delete = ($is_owner && $game->isWaiting()) || $is_admin;

                    $can_join = $game->isWaiting() && !$game->isLocked() && !$game->isPrivate();
                ?>
                <?php if (!$game->isPrivate() || $game->getCreatedByUserId() === $current_user->getId() || $game->isRunning()): ?>
                <tr>
                    <td class="name"><?= htmlspecialchars($game->getName()) ?></td>
                    <td class="players"><?= (int) $game->getPlayerCount() ?> / <?= (int) $game->getPlayerMax() ?></td>
                    <td class="type">
                        <?= ($game->getRuleSetModel()->isGameClassic()) ? Localization::get('application.general.icon.game_classic') : Localization::get('application.general.icon.game_differ') ?>
                        <?= ($game->isPrivate() ? Localization::get('application.general.icon.private') : "") ?>
                        <?= ($game->isLocked() ? Localization::get('application.general.icon.locked') : "") ?>
                    </td>
                    <td>
                        <div class="btn-actions">
                            <a href="/game/detail/<?= $game->getId() ?>" title="Details" class="btn action-btn btn-primary"><?= Localization::get('application.general.icon.details') ?></a>

                            <?php if ($can_join): ?>
                                <form method="POST" action="/game/join/<?= $game->getId() ?>">
                                    <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
                                    <button type="submit" class="btn action-btn btn-primary">
                                        <?= Localization::get('application.general.icon.join') ?>
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if ($can_edit || $can_delete): ?>
                                <?php if ($can_edit): ?>
                                    <a href="/game/edit/<?= $game->getId() ?>" class="btn action-btn btn-primary"><?= Localization::get('application.general.icon.edit') ?></a>
                                <?php endif; ?>

                                <?php if ($can_delete): ?>
                                    <form method="post" action="/game/delete" onsubmit="return confirm('<?= Localization::get('game.list.delete_confirm') ?>');">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
                                        <input type="hidden" name="game_id" value="<?= $game->getId() ?>">
                                        <button type="submit" class="btn action-btn btn-danger">
                                            <?= Localization::get('application.general.icon.delete') ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>