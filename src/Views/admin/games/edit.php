<?php
use App\Core\Csrf;

/** @var \App\Models\GameModel $game */
?>

<div class="container mt-4">
    <h1>Edit game</h1>

    <form action="/admin/games/edit/<?= $game->getId() ?>" method="POST">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($game->getName()) ?>" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="waiting" <?= $game->getStatus() === 'waiting' ? 'selected' : '' ?>>Waiting</option>
                <option value="active" <?= $game->getStatus() === 'active' ? 'selected' : '' ?>>Activ</option>
                <option value="finished" <?= $game->getStatus() === 'finished' ? 'selected' : '' ?>>Finish</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
        <a href="/admin/games/list" class="btn btn-secondary">Cancel</a>
    </form>
</div>
