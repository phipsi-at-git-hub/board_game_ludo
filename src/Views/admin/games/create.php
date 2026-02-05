<?php
use App\Core\Csrf;
?>

<h1>Create new Game</h1>

<form method="POST" action="/admin/games/create">
    <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">

    <div>
        <label>Name oof the Game</label><br>
        <input type="text" name="name" required>
    </div>

    <br>

    <button type="submit">Create Game</button>
    <a href="/admin/games/list">Cancel</a>
</form>
