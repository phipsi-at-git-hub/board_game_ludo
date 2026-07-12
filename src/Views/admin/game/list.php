<?php
use App\Core\Csrf;
use App\Core\Localization;

/**
 * @var array $games
 * @var UserModel $current_user
 */
?>

<div class="panel">
    <h1><?= Localization::get('admin.games.list.title') ?></h1>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li><a href="/lobby" class="btn-back"><?= Localization::get('application.general.btn.back_to_lobby') ?></a></li>
            <li><a href="/admin" class="btn-back"><?= Localization::get('application.general.btn.back_to_dashboard') ?></a></li>
        </ul>
    </div>
    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li><a href="/game/create" class="btn-back"><?= Localization::get('application.general.btn.create_new_game') ?></a></li>
        </ul>
    </div>

    <table class="game-list">
        <thead>
            <tr>
                <th class="name admin"><?= Localization::get('admin.games.list.header.name') ?></th>
                <th class="status admin"><?=  Localization::get('admin.games.list.header.status') ?></th>
                <th class="players admin"><?= Localization::get('admin.games.list.header.players') ?></th>
                <th class="type admin"><?= Localization::get('admin.games.list.header.type') ?></th>
                <th class="options admin"><?= Localization::get('admin.games.list.header.options') ?></th>
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
                    <td class="name admin"><?= htmlspecialchars($game->getName()) ?></td>
                    <td class="status admin">
                        <div class="status-badges">
                            <span class="status-badge status-<?= strtolower($game->getStatus()) ?>">
                                <?= htmlspecialchars($game->getStatus()) ?>
                            </span>
                        </div>
                    </td>
                    <td class="players admin"><?= (int) $game->getPlayerCount() ?> / <?= (int) $game->getPlayerMax() ?></td>
                    <td class="type admin">
                        <?= ($game->getRuleSetModel()->isGameClassic()) ? Localization::get('application.general.icon.game_classic') : Localization::get('application.general.icon.game_differ') ?>
                        <?= ($game->isPrivate() ? Localization::get('application.general.icon.private') : "") ?>
                        <?= ($game->isLocked() ? Localization::get('application.general.icon.locked') : "") ?>
                    </td>
                    <td class="options admin">
                        <div class="btn-actions">
                            <a href="/admin/game/show/<?= $game->getId() ?>" title="Details" class="btn action-btn btn-primary"><?= Localization::get('application.general.icon.details') ?></a>

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
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
