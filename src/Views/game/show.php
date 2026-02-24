<?php

use App\Core\Localization;
?>
<h1><?= Localization::get('game.show.title') ?> <?= htmlspecialchars($game->getName()) ?></h1>

<div class="game-meta">
    <p><strong><?= Localization::get('game.show.status') ?>:</strong> <?= htmlspecialchars($game->getStatus()) ?></p>
    <p><strong><?= Localization::get('game.show.created_at') ?>:</strong> <?= htmlspecialchars($game->getCreatedAt()) ?></p>
    <p><strong><?= Localization::get('game.show.player') ?>:</strong> <?= count($game->players ?? []) ?></p>
</div>

<h2><?= Localization::get('game.show.players') ?></h2>

<?php if (!empty($game->getAllPlayer())): ?>
    <ul class="player-list">
        <?php foreach ($game->getAllPlayer() as $player): ?>
            <li>
                <?= htmlspecialchars($player->getUsername()) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p><?= Localization::get('game.show.label_no_players_found') ?></p>
<?php endif; ?>

<h2><?= Localization::get('game.show.figures') ?></h2>

<?php if (!empty($game->getAllFigures())): ?>

    <?php foreach ($game->getAllPlayer() as $player): ?>
        <h3><?= htmlspecialchars($player->getUsername()) ?></h3>
        <ul>
            <?php foreach ($game->getAllFigures() as $figure): ?>
                <?php if ($figure->getUserId() == $player->getUserId()): ?>
                    <li>
                        <?= Localization::get('game.show.figure') ?> <?= $figure->getFigureIndex() ?> –
                        <?= Localization::get('game.show.position') ?>: <?= $figure->getPosition() ?> –
                        <?= Localization::get('game.show.area') ?>: <?= htmlspecialchars($figure->getArea()) ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>

<?php else: ?>
    <p><?= Localization::get('game.show.label_no_figures_found') ?></p>
<?php endif; ?>

<h2><?= Localization::get('game.show.rules') ?></h2>

<ul>
    <li><?= Localization::get('game.show.label_rules_bots_allows') ?>: <?= $game->getRuleSetModel()->getAllowBots() ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></li>
    <li><?= Localization::get('game.show.label_rules_roll_on_six_limit') ?>: <?php if ($game->getRuleSetModel()->getExtraRollLimit() === 0) { echo Localization::get('game.show.label_rules_roll_on_six_limit_no'); } elseif ($game->getRuleSetModel()->getExtraRollLimit() === 255) { echo Localization::get('game.show.label_rules_roll_on_six_limit_unlimited'); } else { echo Localization::get('game.show.label_rules_roll_on_six_limit_limited'). $game->getRuleSetModel()->getExtraRollLimit();} ?></li>
    <li><?= Localization::get('game.show.label_rules_stack_on_figures') ?>: <?= $game->getRuleSetModel()->getAllowStackOwnFigures() ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></li>
    <li><?= Localization::get('game.show.label_rules_strict_goal_order') ?>: <?= $game->getRuleSetModel()->getStrictGoalOrder() ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></li>
    <li><?= Localization::get('game.show.label_rules_start_field_must_be_cleared') ?>: <?= $game->getRuleSetModel()->getStartFieldMustBeCleared() ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></li>
</ul>