<?php
use App\Core\Csrf;
use App\Core\Localization;
?>

<div class="panel">
    <h1>Admin - Users 👤</h1>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li><a href="/lobby" class="btn-back"><?= Localization::get('game.list.back_to_lobby') ?></a></li>
            <li><a href="/admin" class="btn-back">← Back to Dashboard</a></li>
        </ul>
    </div>

    <table class="table users-list">
        <thead>
            <tr>
                <th class="username">Username</th>
                <th class="email">Email</th>
                <th class="role">Role</th>
                <th class="options">Options</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="username"><?= htmlspecialchars($user->getUsername()) ?></td>
                        <td class="email"><?= htmlspecialchars($user->getEmail()) ?></td>
                        <td class="role">
                            <span class="role-badge role-<?= strtolower($user->getRole()) ?>">
                                <?= htmlspecialchars($user->getRole()) ?>
                            </span>
                        </td>
                        <td class="options">
                            <div class="btn-actions">
                                <a href="/admin/users/edit/<?= $user->getId() ?>" class="btn action-btn btn-primary" title="Edit">✏️</a>

                                <form action="/admin/users/<?= $user->getId() ?>" method="POST">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
                                    <button type="submit" class="btn action-btn btn-danger"
                                        onclick="return confirm('Delete user: <?= htmlspecialchars($user->getUsername()) ?>?')">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>