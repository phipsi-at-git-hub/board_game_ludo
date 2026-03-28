<?php

use App\Core\Localization;
// /Views/admin/users/edit.php
?>
<div class="panel">
    <h1>Admin - Edit User</h1>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li><a href="/lobby" class="btn-back"><?= Localization::get('game.list.back_to_lobby') ?></a></li>
            <li><a href="/admin" class="btn-back">← Back to Dashboard</a></li>
            <li><a href="/admin/users" class="btn-back">← Back to Admin Users List</a></li>
        </ul>
    </div>

    <div class="card">
        <form action="/admin/users/edit/<?= $user->getId() ?>" method="POST">
            <input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::generate() ?>">

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($user->getUsername()) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user->getEmail()) ?>" required>
            </div>

            <div class="nav-actions">
                <button type="submit" class="btn btn-save">Save</button>
            </div>
        </form>
    </div>

</div>

 
