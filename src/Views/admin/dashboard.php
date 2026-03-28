<?php use App\Core\Localization; ?>

<div class="panel">
    <h1>Admin Dashboard 🛠️</h1>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li><a href="/lobby" class="btn-back"><?= Localization::get('game.list.back_to_lobby') ?></a></li>
        </ul>
    </div>

    <div class="dashboard-grid">

        <!-- Users -->
        <div class="card dashboard-card">
            <h2>👤 Users</h2>

            <div class="stats-main">
                <div class="stat-big"><?= $stats['users_total'] ?></div>
                <div class="stat-label">Total Users</div>
            </div>

            <div class="stats-sub">
                <div>
                    <span class="stat-value"><?= $stats['admins_total'] ?></span>
                    <span class="stat-text">Admins</span>
                </div>
            </div>

            <div class="nav-actions">
                <a href="/admin/users" class="btn btn-primary">Manage Users →</a>
            </div>
        </div>

        <!-- Games -->
        <div class="card dashboard-card">
            <h2>🎲 Games</h2>

            <div class="stats-main">
                <div class="stat-big"><?= $stats['games_total'] ?></div>
                <div class="stat-label">Total Games</div>
            </div>

            <div class="stats-sub">
                <div>
                    <span class="stat-value"><?= $stats['games_waiting'] ?></span>
                    <span class="stat-text">Waiting</span>
                </div>
                <div>
                    <span class="stat-value"><?= $stats['games_active'] ?></span>
                    <span class="stat-text">Active</span>
                </div>
                <div>
                    <span class="stat-value"><?= $stats['games_finished'] ?></span>
                    <span class="stat-text">Finished</span>
                </div>
            </div>

            <div class="nav-actions">
                <a href="/admin/games/list" class="btn btn-primary">Manage Games →</a>
            </div>
        </div>

    </div>

</div>
