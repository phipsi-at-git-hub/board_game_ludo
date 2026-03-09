<?php

use App\Constants\Application;
use App\Core\Localization;
?>
<h1><?= $game->getName() ?></h1>

<p><?= Localization::get('game.play.status') ?>: <?= $game->getStatus() ?></p>
<p><?= Localization::get('game.play.current_player') ?>: <?= $game->getCurrentPlayer()->getUsername() ?></p>

<?php if (!$game->getStateModel()->getCurrentDiceRoll()): ?>
    <form method="POST" action="/game/roll/<?= $game->getId() ?>">
        <button type="submit"><?= Localization::get('game.play.roll_dice') ?></button>
    </form>
<?php else: ?>
    <p><?= Localization::get('game.play.rolled') ?>: <?= $game->getStateModel()->getCurrentDiceRoll() ?></p>
<?php endif; ?>

<?php if (!empty($moves)): ?>
    <h3><?= Localization::get('game.play.possible_moves') ?></h3>

    <?php foreach ($moves as $move): ?>
        <form method="POST" action="/game/move/<?= $game->getId() ?>">
            <input type="hidden" name="<?= $move[Application::DTO_MOVE] ?>" value="<?= json_encode($move) ?>">
            <button type="submit"><?= Localization::get('game.play.figure') ?> <?= $move[Application::DTO_FIGURE_INDEX] ?> → <?= Localization::get('game.play.position') ?> <?= $move[Application::DTO_TO] ?></button>
        </form>
    <?php endforeach; ?>

<?php endif; ?>

<h3><?= Localization::get('game.play.figures') ?></h3>

<?php foreach ($game->getAllPlayers() as $player): ?>
    <h4><?= $player->getUsername() ?></h4>
    <ul>
        <?php foreach ($player->getAllFigures() as $index => $figure): ?>
            <li><?= Localization::get('game.play.figure') ?> <?= $index ?> - <?= Localization::get('game.play.position') ?> <?= $figure->getPosition() ?></li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>

<pre>
    <?php print_r($game->getDebugState($user->getId(), $game->getStateModel()->getCurrentDiceRoll())) ?>
</pre>