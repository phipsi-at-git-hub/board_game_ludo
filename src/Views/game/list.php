<?php
use App\Core\Localization;
?>

<h1><?= Localization::get('game.list.title') ?></h1>

<ul>
    <li><a href="/game/create"><?= Localization::get('game.list.create_new_game') ?></a></li>
    <li><a href="/lobby"><?= Localization::get('game.list.back_to_lobby') ?></a></li>
</ul>


<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th><?= Localization::get('game.list.name') ?></th>
            <th><?= Localization::get('game.list.players') ?></th>
            <th><?= Localization::get('game.list.type') ?></th>
            <th><?= Localization::get('game.list.options') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($games as $game): ?>
            <?php 
                $is_owner = $game->getCreatedByUserId() === $current_user->getId();
                $is_admin = $current_user->isAdmin();

                $can_edit = ($is_owner && $game->isWaiting()) || $is_admin;
                $can_delete = ($is_owner && $game->isWaiting()) || $is_admin;

                $can_join = $game->isWaiting() && !$game->isLocked() && !$game->isPrivate();
            ?>
            <tr>
                <td><?= htmlspecialchars($game->getName()) ?></td>
                <td><?= (int) $game->getPlayerCount() ?> / <?= (int) $game->getPlayerMax() ?></td>
                <td>
                    <?= ($game->getRuleSetModel()->isGameClassic()) ? "🎲" : "⚙️" ?>
                    <?= ($game->isPrivate() ? "🔒" : "") ?>
                    <?= ($game->isLocked() ? "⛔" : "") ?>
                </td>
                <td>
                    <div class="action-buttons">
                        <a href="/game/detail/<?= $game->getId() ?>" title="Details" class="action-btn">👁</a>
                        <?php if ($can_join): ?>
                            <form method="POST" action="/game/join/<?= $game->getId() ?>">
                                <button type="submit" class="action-btn">
                                    <?= Localization::get('game.list.join_icon') ?>
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ($can_edit || $can_delete): ?>
                            <div class="action-menu">
                                <button class="menu-toggle action-btn">⋮</button>
                                <div class="menu-content">
                                    <?php if ($can_edit): ?>
                                        <a href="/game/edit/<?= $game->getId() ?>" class="action-btn"><?= Localization::get('game.list.edit_icon') ?></a>
                                    <?php endif; ?>

                                    <?php if ($can_delete): ?>
                                        <form method="post" action="/game/delete" onsubmit="return confirm(<?= Localization::get('game.list.confirm_delete') ?>);">
                                            <input type="hidden" name="id" value="<?= $game->getId() ?>">
                                            <button type="submit" class="action-btn">
                                                <?= Localization::get('game.list.delete_icon') ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
