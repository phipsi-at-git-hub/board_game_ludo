<?php
use App\Core\Csrf;
use App\Constants\Application;
use App\Core\Localization;
?>

<h1><?= Localization::get('game.list.title') ?></h1>

<a href="/game/create"><?= Localization::get('game.list.create_new_game') ?></a>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Created By</th>
            <th>Player No.</th>
            <th>Options</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($games as $game): ?>
            <tr>
                <td><?= htmlspecialchars($game->getName()) ?></td>
                <td><?= htmlspecialchars($game->getStatus()) ?></td>
                <td><?= htmlspecialchars($game->getCreatedByUserId()) ?></td>
                <td><?= (int) $game->getPlayerCount() ?></td>
                <td>
                    <form method="POST" action="/game/<?= $game->getId() ?>/join">
                        <button type="submit">
                            <?= Localization::get('game.list.join') ?>
                        </button>
                    </form>
                </td>
                <!--
                <td>
                    <a href="/admin/games/edit/<?= $game->getId() ?>">✏️ Edit</a>

                    <form action="/admin/games/<?= $game->getId() ?>" method="POST" style="display:inline">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
                        <button type="submit" onclick="return confirm('Delete Game - <?= htmlspecialchars($game->getName()) ?>?')">
                            🗑️ Löschen
                        </button>
                    </form>
                </td>
                -->
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
