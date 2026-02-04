<?php
use App\Core\Csrf;

/** @var array $games */
?>

<div class="container mt-4">
    <h1>Games Administration</h1>

    <ul>
        <ll><a href="/admin/games/create" class="btn btn-success mb-3">Create new game</a></li>
        <li><a href="/admin">Back to Admin Dashboard</a></li>
    </ul>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Created at am</th>
                <th>Options</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($games as $game): ?>
            <tr>
                <td><?= htmlspecialchars($game->getId()) ?></td>
                <td><?= htmlspecialchars($game->getName()) ?></td>
                <td><?= htmlspecialchars($game->getStatus()) ?></td>
                <td><?= htmlspecialchars($game->getCreatedAt()) ?></td>
                <td>
                    <a href="/admin/games/edit/<?= $game->getId() ?>" class="btn btn-sm btn-primary">Edit</a>

                    <form action="/admin/games/<?= $game->getId() ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to finish the game: <?= htmlspecialchars($game->getName()) ?>?');">
                        <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
                        <input type="hidden" name="_method" value="DELETE">
                        <button class="btn btn-sm btn-danger">Finish</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
