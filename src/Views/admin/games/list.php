<?php
use App\Core\Csrf;
?>

<h1>Admin · Games</h1>

<a href="/admin/games/create">➕ Create new Game</a>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Created at</th>
            <th>Options</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($games as $game): ?>
            <tr>
                <td><?= htmlspecialchars($game->getName()) ?></td>
                <td><?= htmlspecialchars($game->getStatus()) ?></td>
                <td><?= htmlspecialchars($game->getCreatedAt()) ?></td>
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
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
