<?php

use App\Core\Localization;
use App\Core\Renderer\ViewRenderer;

/**
 * @var array $users
 * 
 * @var ViewRenderer $viewRenderer
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
    <?php 
    $viewRenderer->renderPartial(
        'admin/user', 
        'collapsible_filter'
    ); 
    ?>

    <div
        data-id="users-filter"
        data-bind-sources="users-filter-form"
        data-bind-1-view-key="entries"
        data-bind-1-type="view">

        <!-- include games entries partials -->
        <?php 
        $viewRenderer->renderPartial(
            'admin/user', 
            'entries'
        ); 
        ?>

    </div>

</div>
