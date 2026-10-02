<?php

use App\Constants\Application;
use App\Core\Localization;
use App\Core\Security\Csrf;
use App\Models\User\UserModel;

/**
 * Required:
 * @var UserModel $user 
 * @var array $games 
 */

$is_detail_view ??= false;
$is_admin_view ??= false; 

$api_url = ($is_admin_view) ? '/api/admin/game/filter' : '/api/game/filter'; 

?>

<div class="nested-card collapsible-item">

    <form
        data-id="user-games-filter-form"
        method="post"
        action="<?= $api_url ?>"

        data-response="json"
        data-bind-targets="
            user-games-filter-games,
            user-games-entry-count,
        ">

        <input
            type="hidden"
            name="user_id"
            value="<?= $user->getId() ?>">

        <input
            type="hidden"
            name="_csrf_token"
            value="<?= Csrf::generate() ?>">

        <div class="nested-card-header collapsible-header">

            <h3>
                <?= Localization::get('admin.users.detail.card.games.list.filter.title') ?>
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

        <div class="entry-filter-content collapsible-content">

            <div class="collapsible-content-inner">

                <!-- Status -->

                <div class="form-row">

                    <span>
                        <?= Localization::get('admin.users.detail.card.games.list.filter.status') ?>
                    </span>

                    <select
                        name="status[]"
                        multiple 
                        data-ui="badge-multiselect" 
                        data-min-selection="1" 
                        data-label-plural="<?= strtoupper(Localization::get('application.general.selected')) ?>" >

                        <option value="<?= htmlspecialchars(Application::STATUS_WAITING) ?>" selected >
                            <?= Localization::get('game.status.waiting') ?>
                        </option>

                        <option value="<?= htmlspecialchars(Application::STATUS_RUNNING) ?>" selected >
                            <?= Localization::get('game.status.running') ?>
                        </option>

                        <option value="<?= htmlspecialchars(Application::STATUS_FINISHED) ?>" selected >
                            <?= Localization::get('game.status.finished') ?>
                        </option>

                        <option value="<?= htmlspecialchars(Application::STATUS_CANCELLED) ?>" selected >
                            <?= Localization::get('game.status.cancelled') ?>
                        </option>

                    </select>

                </div>

                <!-- User Relation -->

                <div class="form-row">

                    <span>
                        <?= Localization::get('admin.users.detail.card.games.list.filter.user_relation') ?>
                    </span>

                    <select
                        name="user_relation[]"
                        multiple 
                        data-ui="badge-multiselect" 
                        data-min-selection="1" 
                        data-label-plural="<?= strtoupper(Localization::get('application.general.selected')) ?>" > 

                        <option value="created" selected >
                            <?= Localization::get('admin.users.detail.card.games.list.filter.user_relation.created') ?>
                        </option>

                        <option value="participated" selected >
                            <?= Localization::get('admin.users.detail.card.games.list.filter.user_relation.participated') ?>
                        </option>

                        <option value="won" selected >
                            <?= Localization::get('admin.users.detail.card.games.list.filter.user_relation.won') ?>
                        </option>

                    </select>

                </div>

                <!-- Date Range -->

                <div class="form-row">

                    <span>
                        <?= Localization::get('admin.users.detail.card.games.list.filter.date_range') ?>
                    </span>

                    <input
                        type="text"
                        name="date_range"
                        data-ui="date-range"
                        data-ui-localization="<?= Application::EN_US ?>"
                        data-ui-with-time="true" 
                        data-ui-with-reset="true" 
                        value="<?= htmlspecialchars($date_range ?? '') ?>">

                </div>

                <div class="form-row">

                    <span></span>

                    <button
                        type="submit"
                        class="btn btn-actions btn-date-range-apply">

                        <?= Localization::get('application.general.btn.apply_filter') ?>

                    </button>

                </div>

            </div>

        </div>

    </form>

</div>
