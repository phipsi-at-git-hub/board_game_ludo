<?php
// admin/users.php
use App\Core\Csrf;
?>

<h1>Admin - Users List</h1>

<ul>
    <li><a href="/admin">Back to Admin Dashboard</a></li>
</ul>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Options</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user->getUsername()) ?></td>
                    <td><?= htmlspecialchars($user->getEmail()) ?></td>
                    <td><?= htmlspecialchars($user->getRole()) ?></td>
                    <td>
                        <!-- Edit Button -->
                        <a href="/admin/users/edit/<?= $user->getId() ?>" title="Edit">✏️</a>

                        <!-- Delete Button -->
                        <form action="/admin/users/<?= $user->getId() ?>" method="POST" style="display:inline">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
                            <button type="submit" title="Delete" onclick="return confirm('Are you sure you want to delete the user: <?= htmlspecialchars($user->getUsername()) ?>?')">🗑️</button>
                        </form>
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
