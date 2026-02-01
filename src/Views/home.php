<?php use App\Core\Auth; ?>

<h1>Welcome <?= htmlspecialchars(Auth::user()->getUsername() ?? 'Guest') ?></h1>

<?php if (Auth::check()): ?>
    <p><a href="/logout">Logout</a></p>
<?php else: ?>
    <p><a href="/login">Login</a> | <a href="/register">Register</a></p>
<?php endif; ?>
