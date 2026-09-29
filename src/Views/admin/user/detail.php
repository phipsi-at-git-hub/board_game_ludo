<?php

use App\Constants\Application;
use App\Core\Security\Csrf;
use App\Core\Localization;
use App\Models\User\UserModel;

/**
 * @var UserModel $user
 * @var UserModel $current_user
 * @var array $games
 * @var array $statistics
 * @var DateTimeImmutable $date_start
 * @var DateTimeImmutable $date_end
 * @var String $date_range
 */

$is_detail_view = true;
$is_admin_view = true; 

?>

<div class="panel">

    <h1>
        <?= Localization::get('admin.users.detail.title') . ' ' . $user->getUsername() ?>
    </h1>

    <div class="nav-actions left">

        <ul class="nav-list horizontal">

            <li>
                <a href="/lobby" class="btn-back">
                    <?= Localization::get('application.general.btn.back_to_lobby') ?>
                </a>
            </li>

            <li>
                <a href="/admin" class="btn-back">
                    <?= Localization::get('application.general.btn.back_to_dashboard') ?>
                </a>
            </li>

            <li>
                <a href="/admin/user/list" class="btn-back">
                    <?= Localization::get('application.general.btn.back_to_list') ?>
                </a>
            </li>

        </ul>

    </div>

    <!-- User -->

    <div class="card entry-detail" >

        <?php include VIEWS_PATH . '/admin/user/partials/entry.php'; ?>

    </div>

    <!-- Account Information -->

    <div class="card">

        <h2>
            <?= Localization::get('admin.users.detail.card.information.title') ?>
        </h2>

        <div class="form-row">

            <span>
                <?= Localization::get('admin.users.detail.card.information.username') ?>
            </span>

            <strong>
                <?= htmlspecialchars($user->getUsername()) ?>
            </strong>

        </div>

        <div class="form-row">

            <span>
                <?= Localization::get('admin.users.detail.card.information.email') ?>
            </span>

            <strong>
                <?= htmlspecialchars($user->getEmail()) ?>
            </strong>

        </div>

        <div class="form-row">

            <span>
                <?= Localization::get('admin.users.detail.card.information.role') ?>
            </span>

            <span class="status-badge status-default">
                <?= htmlspecialchars($user->getRole()) ?>
            </span>

        </div>

        <div class="form-row">

            <span>
                <?= Localization::get('admin.users.detail.card.information.status') ?>
            </span>

            <span class="status-badge status-<?= htmlspecialchars(strtolower($user->getStatus())) ?>">
                <?= htmlspecialchars($user->getStatus()) ?>
            </span>

        </div>

        <div class="form-row">

            <span>
                <?= Localization::get('admin.users.detail.card.information.language') ?>
            </span>

            <span class="status-badge status-default">
                <?= htmlspecialchars($user->getPreferredLanguage()) ?>
            </span>

        </div>

    </div>

    <!-- Account Statistics -->

    <div class="card">

        <h2>
            <?= Localization::get('admin.users.detail.card.statistics.title') ?>
        </h2>

        <div class="form-row">
            
            <span>
                <?= Localization::get('admin.users.detail.card.statistics.logins') ?>
            </span>

            <strong>
                <?= $user->getUserStatistics()->getLogins() ?>
            </strong>

        </div>

        <div class="form-row">
            
            <span>
                <?= Localization::get('admin.users.detail.card.statistics.games_created') ?>
            </span>

            <strong>
                <?= $user->getUserStatistics()->getGamesCreated() ?>
            </strong>

        </div>

        <div class="form-row">
            
            <span>
                <?= Localization::get('admin.users.detail.card.statistics.games_participated') ?>
            </span>

            <strong>
                <?= $user->getUserStatistics()->getGamesParticipated() ?>
            </strong>

        </div>

        <div class="form-row">
            
            <span>
                <?= Localization::get('admin.users.detail.card.statistics.games_won') ?>
            </span>

            <strong>
                <?= $user->getUserStatistics()->getGamesWon() ?>
            </strong>

        </div>

    </div>

    <!-- Account Metadata -->

    <div class="card">

        <h2>
            <?= Localization::get('admin.users.detail.card.metadata.title') ?>
        </h2>

        <div class="form-row">

            <span>
                <?= Localization::get('admin.users.detail.card.metadata.user_id') ?>
            </span>

            <span class="status-badge status-default case-sensitive">
                <?= htmlspecialchars($user->getId()) ?>
            </span>

        </div>

        <div class="form-row">

            <span>
                <?= Localization::get('admin.users.detail.card.metadata.last_login') ?>
            </span>

            <strong>
                <?= htmlspecialchars($user->getLastLogin() ?? '-') ?>
            </strong>

        </div>

        <div class="form-row">

            <span>
                <?= Localization::get('admin.users.detail.card.metadata.created_at') ?>
            </span>

            <strong>
                <?= htmlspecialchars($user->getCreatedAt() ?? '-') ?>
            </strong>

        </div>

        <div class="form-row">

            <span>
                <?= Localization::get('admin.users.detail.card.metadata.updated_at') ?>
            </span>

            <strong>
                <?= htmlspecialchars($user->getUpdatedAt() ?? '-') ?>
            </strong>

        </div>

    </div>

    <!-- Game Information -->

    <div class="card">

        <h2>
            <?= Localization::get('admin.users.detail.card.games.information.title') ?>
        </h2>

        <!-- Game Settings -->

        <div class="form-row">

            <span>
                <?= Localization::get('admin.users.detail.card.games.information.game_camera_mode') ?>
            </span>

            <span class="status-badge status-default">
                <?= htmlspecialchars(Localization::get('game.camera.mode.' . $user->getPreferredCameraMode())) ?>
            </span>

        </div>

    </div>

    <!-- Games -->

    <div class="card">

        <h2>
            <?= Localization::get('admin.users.detail.card.games.list.title') ?>
        </h2>

        <!-- Game Filter -->

        <div class="nested-card">

            <!-- include filter partials -->
            <?php include VIEWS_PATH . '/game/partials/filter.php' ?>

        </div>

        <!-- Games -->

        <div
            data-id="user-games-filter-games"
            data-bind-sources="user-games-filter-form"
            data-bind-1-view-key="entries"
            data-bind-1-type="view">

            <!-- include games entries partials -->
            <?php include VIEWS_PATH . '/game/partials/entries.php' ?>

        </div>

    </div>

</div>
