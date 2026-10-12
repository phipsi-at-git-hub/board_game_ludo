<?php

use App\Core\Localization;

/**
 * @var array $users
 */
?>

<div class="panel">

    <h1>
        <?= Localization::get('admin.users.list.title') ?>
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
        </ul>
    </div>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li>
                <a href="/admin/user/create" class="btn-back">
                    <?= Localization::get('application.general.btn.create_new_user') ?>
                </a>
            </li>
        </ul>
    </div>

    <!-- include filter partials -->
    <?php include VIEWS_PATH . '/admin/user/partials/collapsible_filter.php' ?>

    <div
        data-id="users-filter"
        data-bind-sources="users-filter-form"
        data-bind-1-view-key="entries"
        data-bind-1-type="view">

        <!-- include games entries partials -->
        <?php include VIEWS_PATH . '/admin/user/partials/entries.php' ?>

    </div>

</div>
