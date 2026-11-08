<?php 

use App\Core\Localization;
use App\Core\Renderer\ViewRenderer;
use App\Models\Game\GameModel;
use App\Models\User\UserModel;
use App\Policies\GamePolicy;

/**
 * @var UserModel $current_user
 * @var array $games
 * @var GameModel $game
 * 
 * @var ViewRenderer $viewRenderer
 */

$is_detail_view ??= false;
$is_admin_view ??= false;

?>

<?php if (empty($games)): ?>

    <p>
        <?= Localization::get('game.list.no_entries') ?>
    </p>

<?php else: ?>

    <div class="game-list-cards">

        <?php foreach ($games as $game): ?>

            <?php

            $is_owner = GamePolicy::isOwner($game, $current_user);
            $is_admin = $current_user->isAdmin();

            if ($game->isPrivate() && !$is_owner && !$game->isRunning() && !$is_admin_view && !$is_admin) {
                continue;
            }

            ?>

            <div
                class="card game-row"
                data-id="game-<?= $game->getId() ?>-row" 
                data-bind-sources="game-<?= $game->getId() ?>-delete" 
                data-bind-1-dto-key="deleted" 
                data-bind-1-type="remove" 
                onclick="window.location='<?= ($is_admin_view) ? '/admin' : '' ?>/game/detail/<?= $game->getId() ?>'">
                
                <!-- include game entry -->
                <?php $viewRenderer->renderPartial(
                    'game', 
                    'entry', 
                    [
                        'game' => $game, 
                    ]
                ) ?>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>
