<?php
// /Views/admin/users/edit.php
?>
<h1>Admin - Edit User</h1>

<form action="/admin/users/edit/<?= $user->getId() ?>" method="POST">
    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::generate() ?>">

    <div>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user->getUsername()) ?>" required>
    </div>

    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user->getEmail()) ?>" required>
    </div>

    <button type="submit">Save</button>
</form>

<p><a href="/admin/users">Back to Admin Users List</a></p>
