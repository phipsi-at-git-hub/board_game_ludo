<?php use App\Core\Auth; ?>

<nav>
    <a href="/">Home</a>
    <a href="/lobby">Lobby</a>
    <a href="/account">Account</a>
    <a href="/settings">Settings</a>
    <?php if ($user->isAdmin()): ?>
        <a href="/admin">Admin</a>
    <?php endif; ?>
    <a href="/logout">Logout</a>
</nav>