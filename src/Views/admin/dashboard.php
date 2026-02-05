<h1>Admin Dashboard</h1>

<section>
    <h2>👤 Users</h2>
    <ul>
        <li>Total: <strong><?= $stats['users_total'] ?></strong></li>
        <li>Admins: <strong><?= $stats['admins_total'] ?></strong></li>
        <li><a href="/admin/users">Manage Users →</a></li>
    </ul>
</section>

<hr>

<section>
    <h2>🎲 Spiele</h2>
    <ul>
        <li>Total: <strong><?= $stats['games_total'] ?></strong></li>
        <li>Waiting: <?= $stats['games_waiting'] ?></li>
        <li>Active: <?= $stats['games_active'] ?></li>
        <li>Finished: <?= $stats['games_finished'] ?></li>
        <li><a href="/admin/games/list">Manage Games →</a></li>
    </ul>
</section>

<hr>

<nav>
    <a href="/menu">⬅ Back to main Menu</a>
</nav>
