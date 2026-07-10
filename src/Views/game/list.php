<?php

use App\Constants\Application;
use App\Core\Localization;
use App\Policies\GamePolicy;

/**
 * @var array $games
 * @var Object $current_user
 */
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

    <div class="game-list-cards">

        <?php foreach ($games as $game): ?>

            <?php

            $is_owner = GamePolicy::isOwner($game, $current_user);
            $is_admin = $current_user->isAdmin();

            $can_edit = GamePolicy::canEdit($game, $current_user);
            $can_delete = GamePolicy::canDelete($game, $current_user);

            $can_join = GamePolicy::canJoin($game, $current_user);
            $can_leave = GamePolicy::canLeave($game, $current_user);

            if ($game->isPrivate() && !$is_owner && !$game->isRunning()) {
                continue;
            }

            $player_count = $game->getPlayerCount();
            $player_max = $game->getPlayerMax();

            if ($game->isWaiting()) {
                $status_class = 'status-waiting';
                $status_text = Localization::get('game.status.waiting');
            } elseif ($game->isRunning()) {
                $status_class = 'status-running';
                $status_text = Localization::get('game.status.running');
            } else {
                $status_class = 'status-finished';
                $status_text = Localization::get('game.status.finished');
            }

            if ($player_count === 0) {
                $players_class = 'player-count-category-' . Application::DTO_PLAYER_COUNT_EMPTY;
            } elseif ($player_count === 1) {
                $players_class = 'player-count-category-' . Application::DTO_PLAYER_COUNT_LOW;
            } else {
                $players_class = 'player-count-category-' . Application::DTO_PLAYER_COUNT_READY;
            }

            $ruleset_text = $game->getRuleSetModel()->isGameClassic() ? Localization::get('game.ruleset.classic') : Localization::get('game.ruleset.custom');?>

            <div
                class="card game-row"
                data-id="<?= $game->getId() ?>" 
                data-action-behavior="remove" 
                onclick="window.location='/game/detail/<?= $game->getId() ?>'">
                
                <?php include VIEWS_PATH . '/game/partials/header.php' ?>

            </div>

        <?php endforeach; ?>

    </div>

</div>