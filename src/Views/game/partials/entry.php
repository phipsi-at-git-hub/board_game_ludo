<?php

use App\Core\Localization;
use App\Core\Renderer\ViewRenderer;
use App\Models\Game\GameModel;
use App\Models\User\UserModel;
use App\Policies\GamePolicy;

/**
 * Required:
 * @var GameModel $game 
 * @var UserModel $current_user
 * @var string $status_text 
 * 
 * @var ViewRenderer $viewRenderer
 */

$is_detail_view ??= false;
$is_admin_view ??= false; 

$is_admin = $current_user->isAdmin();

$can_edit = GamePolicy::canEdit($game, $current_user);
$can_cancel = GamePolicy::canCancel($game, $current_user); 
$can_delete = GamePolicy::canDelete($game, $current_user);

$can_join = ($is_detail_view || $is_admin_view) ? false : GamePolicy::canJoin($game, $current_user);
$can_leave = ($is_detail_view || $is_admin_view) ? false : GamePolicy::canLeave($game, $current_user);

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
    
    <?php 
    $viewRenderer->renderPartial(
        'game', 
        'badges'
    ); 
    ?>

    <?php 
    $viewRenderer->renderPartial(
        'game', 
        'actions'
    ); 
    ?>

</div>
