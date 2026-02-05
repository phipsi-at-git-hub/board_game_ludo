<?php
use App\Core\Csrf;
?>

<h1>Edit Game</h1>

<form method="POST" action="/admin/games/edit/<?= $game->getId() ?>">
    <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">

    <div>
        <label>Name of the Game</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($game->getName()) ?>" required>
    </div>

    <br>

    <div>
        <label>Status</label><br>
        <select name="status">
            <?php foreach (['waiting', 'active', 'finished'] as $status): ?>
                <option value="<?= $status ?>" <?= $game->getStatus() === $status ? 'selected' : '' ?>> <?= ucfirst($status) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <br>

    <button type="submit">Save</button>
    <a href="/admin/games/list">Back</a>
</form>
