<h1>🎮 Game Lobby</h1>

<p>Welcome, <?= htmlspecialchars(\App\Core\Auth::user()->getUsername()) ?>!</p>

<ul>
    <li><a href="/game/create">➕ Neues Spiel erstellen</a></li>
    <li><a href="/games">📜 Offene Spiele anzeigen</a></li>
</ul>

<p><a href="/logout">Logout</a></p>
