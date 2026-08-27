<?php

use App\Constants\Application;
use App\Core\Csrf;
use App\Core\Localization;
use App\Models\UserModel;
use App\Policies\GamePolicy;

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

?>

<div class="panel">

    <h1>
        <?= Localization::get('admin.users.detail.title') ?>
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

    <div style="margin-bottom: 16px;">

        <?php include VIEWS_PATH . '/admin/user/partials/item.php'; ?>

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
                <?= htmlspecialchars($user->getLanguage()) ?>
            </span>

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
                <?= Localization::get('admin.users.detail.card.games.information.game_perspective') ?>
            </span>

            <span class="status-badge status-default">
                <?= htmlspecialchars($user->getGamePerspective()) ?>
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

            <form
                data-id="user-games-filter-form"
                method="post"
                action="/api/admin/user/games/filter"

                data-response="json"
                data-bind-targets="
                    user-games-filter-games,
                    user-games-entry-count,
                ">

                <input
                    type="hidden"
                    name="_csrf_token"
                    value="<?= Csrf::generate() ?>">

                <div class="nested-card-header">

                    <h3>
                        <?= Localization::get(
                            'admin.users.detail.card.games.list.filter.title'
                        ) ?>
                    </h3>

                    <span
                        class="status-badge status-default"
                        data-id="user-games-entry-count"
                        data-bind-sources="user-games-filter-form"
                        data-bind-1-type="text"
                        data-bind-1-dto-key="games_count">

                        <?= count($games) ?>

                    </span>

                </div>

                <div class="entry-filter-content">

                    <!-- Status -->

                    <div class="form-row">

                        <span>
                            <?= Localization::get(
                                'admin.users.detail.card.games.list.filter.status'
                            ) ?>
                        </span>

                        <select
                            name="status"
                            data-ui="badge-select">

                            <option value="all">
                                <?= ucfirst(
                                    Localization::get(
                                        'application.general.label.all'
                                    )
                                ) ?>
                            </option>

                            <option value="<?= htmlspecialchars(Application::STATUS_WAITING) ?>">
                                <?= Localization::get('game.status.waiting') ?>
                            </option>

                            <option value="<?= htmlspecialchars(Application::STATUS_RUNNING) ?>">
                                <?= Localization::get('game.status.running') ?>
                            </option>

                            <option value="<?= htmlspecialchars(Application::STATUS_FINISHED) ?>">
                                <?= Localization::get('game.status.finished') ?>
                            </option>

                            <option value="<?= htmlspecialchars(Application::STATUS_CANCELLED) ?>">
                                <?= Localization::get('game.status.cancelled') ?>
                            </option>

                        </select>

                    </div>

                    <!-- User Relation -->

                    <div class="form-row">

                        <span>
                            <?= Localization::get(
                                'admin.users.detail.card.games.list.filter.user_relation'
                            ) ?>
                        </span>

                        <select
                            name="user_relation"
                            data-ui="badge-select">

                            <option value="all">
                                <?= ucfirst(
                                    Localization::get(
                                        'application.general.label.all'
                                    )
                                ) ?>
                            </option>

                            <option value="created">
                                <?= Localization::get(
                                    'admin.users.detail.card.games.list.filter.user_relation.created'
                                ) ?>
                            </option>

                            <option value="participated">
                                <?= Localization::get(
                                    'admin.users.detail.card.games.list.filter.user_relation.participated'
                                ) ?>
                            </option>

                            <option value="won">
                                <?= Localization::get(
                                    'admin.users.detail.card.games.list.filter.user_relation.won'
                                ) ?>
                            </option>

                        </select>

                    </div>

                    <!-- Date Range -->

                    <div class="form-row">

                        <span>
                            <?= Localization::get(
                                'admin.users.detail.card.games.list.filter.date_range'
                            ) ?>
                        </span>

                        <input
                            type="text"
                            name="date_range"
                            data-ui="date-range"
                            data-ui-localization="en-us"
                            data-ui-with-time="true"
                            value="<?= htmlspecialchars($date_range ?? '') ?>">

                    </div>

                    <div class="form-row">

                        <span></span>

                        <button
                            type="submit"
                            class="btn btn-actions btn-date-range-apply">

                            <?= Localization::get(
                                'application.general.btn.apply_filter'
                            ) ?>

                        </button>

                    </div>

                </div>

            </form>

        </div>

        <!-- Games -->

        <div
            data-id="user-games-filter-games"
            data-bind-sources="user-games-filter-form"
            data-bind-1-view-key="games"
            data-bind-1-type="view">

            <div class="game-list-cards">

                <?php if (empty($games)): ?>

                    <p>
                        <?= Localization::get(
                            'admin.users.detail.card.games.no_games'
                        ) ?>
                    </p>

                <?php else: ?>

                    <?php foreach ($games as $game):

                        $is_owner = GamePolicy::isOwner($game, $current_user);
                        $is_admin = $current_user->isAdmin();

                        $can_edit = GamePolicy::canEdit($game, $current_user);
                        $can_cancel = GamePolicy::canCancel($game, $current_user);
                        $can_delete = GamePolicy::canDelete($game, $current_user);

                        $can_join = ($is_detail_view)
                            ? false
                            : GamePolicy::canJoin($game, $current_user);

                        $can_leave = ($is_detail_view)
                            ? false
                            : GamePolicy::canLeave($game, $current_user);

                        $player_count = $game->getPlayerCount();
                        $player_max = $game->getPlayerMax();

                        if ($game->isWaiting()) {
                            $status_class = 'status-waiting';
                            $status_text = Localization::get('game.status.waiting');
                        } elseif ($game->isRunning()) {
                            $status_class = 'status-running';
                            $status_text = Localization::get('game.status.running');
                        } elseif ($game->isFinished()) {
                            $status_class = 'status-finished';
                            $status_text = Localization::get('game.status.finished');
                        } elseif ($game->isCancelled()) {
                            $status_class = 'status-cancelled';
                            $status_text = Localization::get('game.status.cancelled');
                        }

                        if ($player_count === 0) {
                            $players_class =
                                'player-count-category-'
                                . Application::DTO_PLAYER_COUNT_EMPTY;
                        } elseif ($player_count === 1) {
                            $players_class =
                                'player-count-category-'
                                . Application::DTO_PLAYER_COUNT_LOW;
                        } else {
                            $players_class =
                                'player-count-category-'
                                . Application::DTO_PLAYER_COUNT_READY;
                        }

                        $ruleset_text = Localization::get(
                            'game.ruleset.'
                            . $game->getRuleSetModel()->getPreset()
                        );

                    ?>

                        <div
                            class="card game-row"
                            onclick="window.location='/admin/game/detail/<?= $game->getId() ?>'">

                            <?php include VIEWS_PATH . '/game/partials/header.php'; ?>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>