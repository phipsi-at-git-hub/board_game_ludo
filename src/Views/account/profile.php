<?php use App\Core\Auth; use App\Core\Csrf; ?>

<h1>Account</h1>

<form method="POST" action="/account/update">
    <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
    <input type="text" name="username" value="<?= htmlspecialchars(Auth::user()->getUsername()) ?>" required><br>
    <input type="email" name="email" value="<?= htmlspecialchars(Auth::user()->getEmail()) ?>" required><br>
    <button type="submit">Update Profile</button>
</form>

<h2>Change Password</h2>
<form method="POST" action="/account/password">
    <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
    <input type="password" name="current_password" placeholder="Current password" required><br>
    <input type="password" name="new_password" placeholder="New password" required><br>
    <input type="password" name="confirm_password" placeholder="Confirm new password" required><br>
    <button type="submit">Change Password</button>
</form>

<h2>Danger Zone</h2>
<form method="POST" action="/account/delete" onsubmit="return confirm('Are you sure? This cannot be undone.')">
    <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
    <button type="submit" style="color:red;">Delete Account</button>
</form>

<p><a href="/lobby">Back to Lobby</a></p>
