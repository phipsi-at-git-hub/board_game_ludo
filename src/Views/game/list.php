<?php

use App\Core\Localization; 
use App\Core\Renderer\ViewRenderer;

/**
 * @var array $games
 * @var Object $current_user
 * 
 * @var ViewRenderer $viewRenderer
 */

$user = $current_user;

$is_admin_view ??= false; 

?>

<div class="panel">

    <h1><?= Localization::get('game.list.title') ?></h1>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li>
                <a href="/lobby" class="btn-back">
                    <?= Localization::get('application.general.btn.back_to_lobby') ?>
                </a>
            </li>
        </ul>
    </div>

    <!-- include filter partials -->
    <?php 
    $viewRenderer->renderPartial(
        'game', 
        'collapsible_filter', 
        [
            'user' => $user
        ]
    ); 
    ?>

    <div
        data-id="games-filter-games"
        data-bind-sources="games-filter-form"
        data-bind-1-view-key="entries"
        data-bind-1-type="view">

        <!-- include games entries partials -->
        <?php $viewRenderer->renderPartial(
            'game', 
            'entries', 
            [
                'is_admin_view' => $is_admin_view, 
                'games' => $games
            ]
        ) ?>

    </div>

</div>
