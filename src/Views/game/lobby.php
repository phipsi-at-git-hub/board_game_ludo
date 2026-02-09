<?php use App\Core\Csrf; ?>

<h1>🎮 Game Lobby</h1>

<p>Welcome, <?= htmlspecialchars(\App\Core\Auth::user()->getUsername()) ?>!</p>

<ul>
    <li><a href="/game/create">➕ Create new Game</a></li>
    <li><a href="/game/list">📜 Show open Games</a></li>
    <li><a href="/menu">Back to Main Menu</a></li>
</ul>
