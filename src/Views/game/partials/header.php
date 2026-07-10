<?php

/**
 * Required:
 * @var object $game 
 * @var string $status_text 
 */
?>

<div class="game-row-header">

    <div
        class="game-row-title"
        data-bind="name">
        <?= htmlspecialchars($game->getName()) ?>
    </div>

    <span
        class="status-badge <?= $status_class ?>"
        data-bind="status">
        <?= strtoupper($status_text) ?>
    </span>

</div>

<div class="game-row-footer">

    <?php include VIEWS_PATH . '/game/partials/badges.php'; ?>

    <?php include VIEWS_PATH . '/game/partials/actions.php'; ?>

</div>
