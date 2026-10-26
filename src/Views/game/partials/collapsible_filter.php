<?php

use App\Constants\Application;
use App\Constants\LocalizationConstants;
use App\Constants\User\UserRelationConstants;
use App\Core\Dto\Game\GameFilterContext;
use App\Core\Localization;
use App\Core\Security\Csrf;
use App\Models\User\UserModel;

/**
 * Required:
 * @var UserModel $user 
 * @var array $games 
 * @var GameFilterContext $filter 
 */

$is_detail_view ??= false;
$is_admin_view ??= false; 

$api_url = ($is_admin_view) ? '/api/admin/game/filter' : '/api/game/filter'; 

$filter ??= null;
// set filter presets
$filterPresetStatusWaiting = ($filter instanceof GameFilterContext && is_array($filter->statuses) && in_array(Application::STATUS_WAITING, $filter->statuses)) ? 'selected' : ''; 
$filterPresetStatusRunning = ($filter instanceof GameFilterContext && is_array($filter->statuses) && in_array(Application::STATUS_RUNNING, $filter->statuses)) ? 'selected' : ''; 
$filterPresetStatusFinished = ($filter instanceof GameFilterContext && is_array($filter->statuses) && in_array(Application::STATUS_FINISHED, $filter->statuses)) ? 'selected' : ''; 
$filterPresetStatusCancelled = ($filter instanceof GameFilterContext && is_array($filter->statuses) && in_array(Application::STATUS_CANCELLED, $filter->statuses)) ? 'selected' : ''; 

$filterPresetUserRelationCreated = ($filter instanceof GameFilterContext && is_array($filter->user_relations) && in_array(Application::GAMES_CREATED, $filter->user_relations)) ? 'selected' : ''; 
$filterPresetUserRelationParticipated = ($filter instanceof GameFilterContext && is_array($filter->user_relations) && in_array(Application::GAMES_PARTICIPATED, $filter->user_relations)) ? 'selected' : ''; 
$filterPresetUserRelationWon = ($filter instanceof GameFilterContext && is_array($filter->user_relations) && in_array(Application::GAMES_WON, $filter->user_relations)) ? 'selected' : ''; 

?>

<div class="nested-card collapsible-item">

    <form
        data-id="games-filter-form"
        method="post"
        action="<?= $api_url ?>"

        data-response="json"
        data-bind-targets="
            games-filter-games,
            games-entry-count,
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
                data-id="games-entry-count"
                data-bind-sources="games-filter-form"
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

                        <option value="<?= htmlspecialchars(Application::STATUS_WAITING) ?>" <?= $filterPresetStatusWaiting ?> >
                            <?= Localization::get('game.status.waiting') ?>
                        </option>

                        <option value="<?= htmlspecialchars(Application::STATUS_RUNNING) ?>" <?= $filterPresetStatusRunning ?> >
                            <?= Localization::get('game.status.running') ?>
                        </option>

                        <option value="<?= htmlspecialchars(Application::STATUS_FINISHED) ?>" <?= $filterPresetStatusFinished ?> >
                            <?= Localization::get('game.status.finished') ?>
                        </option>

                        <option value="<?= htmlspecialchars(Application::STATUS_CANCELLED) ?>" <?= $filterPresetStatusCancelled ?> >
                            <?= Localization::get('game.status.cancelled') ?>
                        </option>

                    </select>

                </div>

                <!-- User Relation -->

                <?php if (!$is_admin_view): ?>

                <div class="form-row">

                    <span>
                        <?= Localization::get('admin.users.detail.card.games.list.filter.user_relation') ?>
                    </span>

                    <select
                        name="user_relation[]"
                        multiple 
                        data-ui="badge-multiselect" 
                        data-min-selection="<?= ($is_admin_view) ? '1' : '0' ?>" 
                        data-label-plural="<?= strtoupper(Localization::get('application.general.selected')) ?>" > 

                        <option value="<?= htmlspecialchars(UserRelationConstants::CREATED) ?>" <?= $filterPresetUserRelationCreated ?> >
                            <?= Localization::get('admin.users.detail.card.games.list.filter.user_relation.created') ?>
                        </option>

                        <option value="<?= htmlspecialchars(UserRelationConstants::PARTICIPATED) ?>" <?= $filterPresetUserRelationParticipated ?> >
                            <?= Localization::get('admin.users.detail.card.games.list.filter.user_relation.participated') ?>
                        </option>

                        <option value="<?= htmlspecialchars(UserRelationConstants::WON) ?>" <?= $filterPresetUserRelationWon ?> >
                            <?= Localization::get('admin.users.detail.card.games.list.filter.user_relation.won') ?>
                        </option>

                    </select>

                </div>

                <?php endif; ?>

                <!-- Date Range -->

                <div class="form-row">

                    <span>
                        <?= Localization::get('admin.users.detail.card.games.list.filter.date_range') ?>
                    </span>

                    <input
                        type="text"
                        name="date_range"
                        data-ui="date-range"
                        data-ui-localization="<?= LocalizationConstants::EN_US ?>"
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
