<?php use App\Core\Csrf; ?>

<h1>🎮 Game Lobby</h1>

<p>Welcome, <?= htmlspecialchars(\App\Core\Auth::user()->getUsername()) ?>!</p>

<ul>
    <li><a href="/game/create">➕ Neues Spiel erstellen</a></li>
    <li><a href="/games">📜 Offene Spiele anzeigen</a></li>
    <li><a href="/account">📜 Mein User Profil anzeigen</a></li>
</ul>

<form action="/logout" method="POST">
    <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
    <button type="submit">Logout</button>
</form>
