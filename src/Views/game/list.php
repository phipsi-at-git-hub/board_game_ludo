<?php

use App\Core\Localization; 

/**
 * @var array $games
 * @var Object $current_user
 */

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

    <!-- include all games partials -->
    <?php include VIEWS_PATH . '/game/partials/entries.php' ?>

</div>
