<?php

use App\Core\Localization;

/**
 * Required:
 * @var object $current_user
 * @var object $game
 */

$players = $game->getAllPlayers();
$display_order = match (count($players)) {
    1 => [0, null],
    2 => [0, 1],
    3 => [0, 1, null, 2],
    default => [0, 1, 3, 2]
};

?>

<div class="player-grid">

    <?php if (!empty($players)): ?>

        <?php foreach ($display_order as $index): ?>

            <?php if ($index === null): ?>

                <div class="player-grid placeholder"></div>

                <?php continue; ?>

            <?php endif; ?>

            <?php 
            if (!isset($players[$index])) { 
                continue;
            }
            ?>

            <?php $player = $players[$index]; ?>

            <div class="nested-card">

                <div class="card-header">

                    <h3>
                        <?= htmlspecialchars($player->getUsername()) ?>
                    </h3>

                    <div class="game-row-badges">

                        <?php if ($player->getUserId() === $current_user->getId()): ?>

                            <span class="role-badge role-me">
                                <?= Localization::get('application.general.label.me') ?>
                            </span>

                        <?php endif; ?>

                        <?php if ($player->getUserId() === $game->getCreatedByUserId()): ?>

                            <span class="role-badge role-admin">
                                <?= Localization::get('application.general.label.owner') ?>
                            </span>

                        <?php endif; ?>

                        <?php if ($game->isFinished() && $player->getUserId() === $game->getStateModel()->getWinnerUserId()): ?>

                            <span class="role-badge role-winner">
                                <?= Localization::get('game.show.winner') ?>
                            </span>

                        <?php endif; ?>

                    </div>

                </div>

                <?php foreach ($player->getAllFigures() as $figure): ?>

                    <div class="form-row">

                        <span>
                            <?= Localization::get('game.show.figure') ?>
                            <?= $figure->getFigureIndex() ?>
                        </span>

                        <span>
                            <?= htmlspecialchars($figure->getArea()) ?>
                            (<?= $figure->getPosition() ?>)
                        </span>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <p>
            <?= Localization::get('game.show.label_no_players_found') ?>
        </p>

    <?php endif; ?>

</div>
