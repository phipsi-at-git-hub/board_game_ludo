<h1>Main Menu</h1>

<ul>
    <li><a href="/game/single">Singleplayer</a></li>
    <li><a href="/lobby">Multiplayer Lobby</a></li>
    <li><a href="/account">Account</a></li>
    <li><a href="/settings">Settings</a></li>
    <?php if ($user->isAdmin()): ?>
        <li><a href="/admin">Admin</a></li>
    <?php endif; ?>
</ul>

<form method="POST" action="/logout">
    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::generate() ?>">
    <button type="submit">Logout</button>
</form>