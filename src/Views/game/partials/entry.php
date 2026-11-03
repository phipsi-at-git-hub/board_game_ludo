<?php

use App\Constants\Dto\GamePlayerDtoConstants;
use App\Core\Localization;
use App\Models\Game\GameModel;
use App\Models\User\UserModel;
use App\Policies\GamePolicy;

/**
 * Required:
 * @var GameModel $game 
 * @var UserModel $current_user
 * @var string $status_text 
 */

$is_detail_view ??= false;
$is_admin_view ??= false; 

$is_owner = GamePolicy::isOwner($game, $current_user);
$is_admin = $current_user->isAdmin();

$can_edit = GamePolicy::canEdit($game, $current_user);
$can_cancel = GamePolicy::canCancel($game, $current_user); 
$can_delete = GamePolicy::canDelete($game, $current_user);

$can_join = ($is_detail_view || $is_admin_view) ? false : GamePolicy::canJoin($game, $current_user);
$can_leave = ($is_detail_view || $is_admin_view) ? false : GamePolicy::canLeave($game, $current_user);

$player_count = $game->getPlayerCount();
$player_max = $game->getPlayerMax();

if ($game->isWaiting()) {
    $status_class = 'status-waiting';
    $status_text = Localization::get('game.status.waiting');
} elseif ($game->isRunning()) {
    $status_class = 'status-running';
    $status_text = Localization::get('game.status.running');
} elseif ($game->isFinished()) {
    $status_class = 'status-finished';
    $status_text = Localization::get('game.status.finished');
} elseif ($game->isCancelled()) {
    $status_class = 'status-cancelled';
    $status_text = Localization::get('game.status.cancelled');
}

if ($player_count === 0) {
    $players_class = 'player-count-category-' . GamePlayerDtoConstants::PLAYER_COUNT_EMPTY;
} elseif ($player_count === 1) {
    $players_class = 'player-count-category-' . GamePlayerDtoConstants::PLAYER_COUNT_LOW;
} else {
    $players_class = 'player-count-category-' . GamePlayerDtoConstants::PLAYER_COUNT_READY;
}

$ruleset_text = Localization::get('game.ruleset.' . $game->getRuleSetModel()->getPreset()); 

?>

<div class="game-row-header">

    <div
        class="game-row-title"
        data-bind="name">
        <?= htmlspecialchars($game->getName()) ?>
    </div>

    <div>

        <?php if (!$is_admin_view  && $game->isFinished() && $current_user->getId() === $game->getStateModel()->getWinnerUserId()): ?>
            
            <span class="role-badge role-winner">
                <?= Localization::get('game.show.winner') ?>
            </span>
            
        <?php endif; ?>

        <span
            class="status-badge <?= $status_class ?>"
            data-bind="status">
            <?= strtoupper($status_text) ?>
        </span>

    </div>

</div>

<div class="game-row-footer">
    
    <?php // ToDo: Use ViewRender->renderPartialString() ?>
    <?php include VIEWS_PATH . '/game/partials/badges.php'; ?>

    <?php // ToDo: Use ViewRender->renderPartialString() ?>
    <?php include VIEWS_PATH . '/game/partials/actions.php'; ?>

</div>
