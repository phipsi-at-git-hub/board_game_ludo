<?php

use App\Core\Localization;
use App\Models\GameModel;
use App\Models\UserModel;

/**
 * Required:
 * @var GameModel $game 
 * @var UserModel $current_user
 * @var string $status_text 
 */
?>

<div class="game-row-header">

    <div
        class="game-row-title"
        data-bind="name">
        <?= htmlspecialchars($game->getName()) ?>
    </div>

    <div>

        <?php if ($game->isFinished() && $current_user->getId() === $game->getStateModel()->getWinnerUserId()): ?>
            
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

    <?php include VIEWS_PATH . '/game/partials/badges.php'; ?>

    <?php include VIEWS_PATH . '/game/partials/actions.php'; ?>

</div>
