<?php 
// src/Views/admin/user/partials/collapsible_filter.php

use App\Constants\Application;
use App\Core\Localization;
use App\Core\Security\Csrf;

/**
 * @var array $users
 */

$is_detail_view ??= false;
$is_admin_view ??= false; 

$filter ??= null;
// set filter presets
/*
$filterPresetStatusWaiting = ($filter instanceof GameFilterContext && is_array($filter->statuses) && in_array(Application::STATUS_WAITING, $filter->statuses)) ? 'selected' : ''; 
$filterPresetStatusRunning = ($filter instanceof GameFilterContext && is_array($filter->statuses) && in_array(Application::STATUS_RUNNING, $filter->statuses)) ? 'selected' : ''; 
$filterPresetStatusFinished = ($filter instanceof GameFilterContext && is_array($filter->statuses) && in_array(Application::STATUS_FINISHED, $filter->statuses)) ? 'selected' : ''; 
$filterPresetStatusCancelled = ($filter instanceof GameFilterContext && is_array($filter->statuses) && in_array(Application::STATUS_CANCELLED, $filter->statuses)) ? 'selected' : ''; 

$filterPresetUserRelationCreated = ($filter instanceof GameFilterContext && is_array($filter->user_relations) && in_array(Application::GAMES_CREATED, $filter->user_relations)) ? 'selected' : ''; 
$filterPresetUserRelationParticipated = ($filter instanceof GameFilterContext && is_array($filter->user_relations) && in_array(Application::GAMES_PARTICIPATED, $filter->user_relations)) ? 'selected' : ''; 
$filterPresetUserRelationWon = ($filter instanceof GameFilterContext && is_array($filter->user_relations) && in_array(Application::GAMES_WON, $filter->user_relations)) ? 'selected' : ''; 
*/

$filterPresetStatusActive = 'selected'; 
$filterPresetStatusInactive = 'selected'; 
$filterPresetStatusClosed = 'selected'; 
$filterPresetStatusBlocked = 'selected'; 

$filterPresetStatusUser = 'selected'; 
$filterPresetStatusModerator = 'selected'; 
$filterPresetStatusGameMaster = 'selected'; 
$filterPresetStatusAdmin = 'selected'; 

?>

<div class="nested-card collapsible-item">

    <form
        data-id="users-filter-form"
        method="post"
        action="/api/admin/user/filter"

        data-response="json"
        data-bind-targets="
            users-filter,
            users-entry-count,
        ">

        <input
            type="hidden"
            name="_csrf_token"
            value="<?= Csrf::generate() ?>">

        <div class="nested-card-header collapsible-header">

            <h3>
                <?= Localization::get('admin.users.list.filter.title') ?>
            </h3>

            <span
                class="status-badge status-default"
                data-id="users-entry-count"
                data-bind-sources="users-filter-form"
                data-bind-1-type="text"
                data-bind-1-dto-key="users_count">

                <?= count($users) ?>

            </span>

        </div>

        <div class="entry-filter-content collapsible-content">

            <div class="collapsible-content-inner">

                <!-- Status -->

                <div class="form-row">

                    <span>
                        <?= Localization::get('admin.users.list.filter.status') ?>
                    </span>

                    <select
                        name="status[]"
                        multiple 
                        data-ui="badge-multiselect" 
                        data-min-selection="1" 
                        data-label-plural="<?= strtoupper(Localization::get('application.general.selected')) ?>" >

                        <option value="<?= htmlspecialchars(Application::ACTIVE) ?>" <?= $filterPresetStatusActive ?> >
                            <?= Localization::get('application.general.active') ?>
                        </option>

                        <option value="<?= htmlspecialchars(Application::INACTIVE) ?>" <?= $filterPresetStatusInactive ?> >
                            <?= Localization::get('application.general.inactive') ?>
                        </option>

                        <option value="<?= htmlspecialchars(Application::CLOSED) ?>" <?= $filterPresetStatusClosed ?> >
                            <?= Localization::get('application.general.closed') ?>
                        </option>

                        <option value="<?= htmlspecialchars(Application::BLOCKED) ?>" <?= $filterPresetStatusBlocked ?> >
                            <?= Localization::get('application.general.blocked') ?>
                        </option>

                    </select>

                </div>

                <!-- User Relation -->

                <?php if (!$is_admin_view): ?>

                <div class="form-row">

                    <span>
                        <?= Localization::get('admin.users.list.filter.role') ?>
                    </span>

                    <select
                        name="role[]"
                        multiple 
                        data-ui="badge-multiselect" 
                        data-min-selection="1" 
                        data-label-plural="<?= strtoupper(Localization::get('application.general.selected')) ?>" > 

                        <option value="created" <?= $filterPresetStatusUser ?> >
                            <?= Localization::get('application.general.user') ?>
                        </option>

                        <option value="participated" <?= $filterPresetStatusModerator ?> >
                            <?= Localization::get('application.general.moderator') ?>
                        </option>

                        <option value="won" <?= $filterPresetStatusGameMaster ?> >
                            <?= Localization::get('application.general.game_master') ?>
                        </option>

                        <option value="won" <?= $filterPresetStatusAdmin ?> >
                            <?= Localization::get('application.general.admin') ?>
                        </option>

                    </select>

                </div>

                <?php endif; ?>

                <!-- Date Range -->

                <div class="form-row">

                    <span>
                        <?= Localization::get('admin.users.list.filter.date_range') ?>
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
