<?php 
use App\Core\Localization; 
?>

<div class="panel">
    <h1><?= Localization::get('admin.dashboard.title') ?></h1>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li><a href="/lobby" class="btn-back"><?= Localization::get('game.list.back_to_lobby') ?></a></li>
        </ul>
    </div>

    <div class="dashboard-grid">

        <!-- Users -->
        <div class="card dashboard-card">
            <h2><?= Localization::get('admin.dashboard.users_card.title') ?></h2>

            <div class="stats-main">
                <div class="stat-big"><?= $stats['users_total'] ?></div>
                <div class="stat-label"><?= Localization::get('admin.dashboard.users_card.total') ?></div>
            </div>

            <div class="stats-sub">
                <div>
                    <span class="stat-value"><?= $stats['admins_total'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.users_card.admin') ?></span>
                </div>
            </div>

            <div class="nav-actions">
                <a href="/admin/users" class="btn btn-primary"><?= Localization::get('admin.dashboard.manage') ?></a>
            </div>
        </div>

        <!-- Games -->
        <div class="card dashboard-card">
            <h2><?= Localization::get('admin.dashboard.games_card.title') ?></h2>

            <div class="stats-main">
                <div class="stat-big"><?= $stats['games_total'] ?></div>
                <div class="stat-label"><?= Localization::get('admin.dashboard.games_card.total') ?></div>
            </div>

            <div class="stats-sub">
                <div>
                    <span class="stat-value"><?= $stats['games_waiting'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.games_card.waiting') ?></span>
                </div>
                <div>
                    <span class="stat-value"><?= $stats['games_active'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.games_card.active') ?></span>
                </div>
                <div>
                    <span class="stat-value"><?= $stats['games_finished'] ?></span>
                    <span class="stat-text"><?= Localization::get('admin.dashboard.games_card.finished') ?></span>
                </div>
            </div>

            <div class="nav-actions">
                <a href="/admin/games/list" class="btn btn-primary"><?= Localization::get('admin.dashboard.games_manage') ?></a>
            </div>
        </div>

    </div>

</div>
