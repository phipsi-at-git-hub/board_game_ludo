<?php 
// src/Views/admin/user/partials/collapsible_filter.php

use App\Constants\LocalizationConstants;
use App\Constants\User\UserRoleConstants;
use App\Constants\User\UserStatusConstants;
use App\Core\Dto\User\UserFilterContext;
use App\Core\Localization;
use App\Core\Security\Csrf;

/**
 * @var array $users
 */

$is_detail_view ??= false;
$is_admin_view ??= false; 

$filter ??= null;
// set filter presets 
$filterPresetStatusActive = ($filter instanceof UserFilterContext && is_array($filter->statuses) && in_array(UserStatusConstants::ACTIVE, $filter->statuses)) ? 'selected' : ''; 
$filterPresetStatusInactive = ($filter instanceof UserFilterContext && is_array($filter->statuses) && in_array(UserStatusConstants::INACTIVE, $filter->statuses)) ? 'selected' : ''; 
$filterPresetStatusClosed = ($filter instanceof UserFilterContext && is_array($filter->statuses) && in_array(UserStatusConstants::CLOSED, $filter->statuses)) ? 'selected' : ''; 
$filterPresetStatusBlocked = ($filter instanceof UserFilterContext && is_array($filter->statuses) && in_array(UserStatusConstants::BLOCKED, $filter->statuses)) ? 'selected' : ''; 

$filterPresetStatusUser = ($filter instanceof UserFilterContext && is_array($filter->roles) && in_array(UserRoleConstants::USER, $filter->roles)) ? 'selected' : ''; 
$filterPresetStatusModerator = ($filter instanceof UserFilterContext && is_array($filter->roles) && in_array(UserRoleConstants::MODERATOR, $filter->roles)) ? 'selected' : ''; 
$filterPresetStatusGameMaster = ($filter instanceof UserFilterContext && is_array($filter->roles) && in_array(UserRoleConstants::GAME_MASTER, $filter->roles)) ? 'selected' : ''; 
$filterPresetStatusAdmin = ($filter instanceof UserFilterContext && is_array($filter->roles) && in_array(UserRoleConstants::ADMIN, $filter->roles)) ? 'selected' : ''; 

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

                        <option value="<?= htmlspecialchars(UserStatusConstants::ACTIVE) ?>" <?= $filterPresetStatusActive ?> >
                            <?= Localization::get('application.general.active') ?>
                        </option>

                        <option value="<?= htmlspecialchars(UserStatusConstants::INACTIVE) ?>" <?= $filterPresetStatusInactive ?> >
                            <?= Localization::get('application.general.inactive') ?>
                        </option>

                        <option value="<?= htmlspecialchars(UserStatusConstants::CLOSED) ?>" <?= $filterPresetStatusClosed ?> >
                            <?= Localization::get('application.general.closed') ?>
                        </option>

                        <option value="<?= htmlspecialchars(UserStatusConstants::BLOCKED) ?>" <?= $filterPresetStatusBlocked ?> >
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

                        <option value="<?= htmlspecialchars(UserRoleConstants::USER) ?>" <?= $filterPresetStatusUser ?> >
                            <?= Localization::get('application.general.user') ?>
                        </option>

                        <option value="<?= htmlspecialchars(UserRoleConstants::MODERATOR) ?>" <?= $filterPresetStatusModerator ?> >
                            <?= Localization::get('application.general.moderator') ?>
                        </option>

                        <option value="<?= htmlspecialchars(UserRoleConstants::GAME_MASTER) ?>" <?= $filterPresetStatusGameMaster ?> >
                            <?= Localization::get('application.general.game_master') ?>
                        </option>

                        <option value="<?= htmlspecialchars(UserRoleConstants::ADMIN) ?>" <?= $filterPresetStatusAdmin ?> >
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
                        name="date_range_last_login"
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
