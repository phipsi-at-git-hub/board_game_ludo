<?php
use App\Constants\Application;
use App\Core\Csrf;
use App\Core\Localization;
?>
<h1><?= $game->getName() ?></h1>

<div class="back-link">
    <ul class="nav-list">
        <li><a href="/game/detail/<?= $game->getId() ?>" class="btn-back"><?= Localization::get('game.play.back_to_detail') ?></a></li>
        <li><a href="/game/list" class="btn-back"><?= Localization::get('game.show.back_to_list') ?></a></li>
        <li><a href="/lobby" class="btn-back"><?= Localization::get('game.list.back_to_lobby') ?></a></li>
        <li><a href="/menu" class="btn-back"><?= Localization::get('game.lobby.back_to_menu') ?></a></li>
    </ul>
</div>

<p><?= Localization::get('game.play.status') ?>: <?= $game->getStatus() ?></p>
<p><?= Localization::get('game.play.current_player') ?>: <?= $game->getCurrentPlayer()->getUsername() ?></p>
<?php if($game->isFinished() && $game->getWinner()->getUserId() === $user->getId()): ?>
    <p>YOU WON!</p>
<?php endif; ?>
<?php if($game->isFinished() && $game->getWinner()->getUserId() !== $user->getId()): ?>
    <p>YOU LOST!</p>
<?php endif; ?>

<!-- Roll Dice -->
 <?php if ($game->isPlayersTurn($user) && $game->isRunning()): ?>
    <?php if ($game->getStateModel()->getCurrentDiceRoll() === null): ?>
        <form method="POST" action="/game/roll">
            <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
            <input type="hidden" name="game_id" value="<?= $game->getId() ?>">
            <button type="submit"><?= Localization::get('game.play.roll_dice') ?></button>
        </form>
    <?php else: ?>
        <p><?= Localization::get('game.play.rolled') ?>: <?= $game->getStateModel()->getCurrentDiceRoll() ?></p>
    <?php endif; ?>

    <!-- Possible Moves -->
    <?php if (!empty($moves)): ?>
        <h3><?= Localization::get('game.play.possible_moves') ?></h3>

        <?php foreach ($moves as $move): ?>
            <?php if (!empty($move[Application::DTO_IS_PASS])): ?>
                <form method="POST" action="/game/move">
                    <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
                    <input type="hidden" name="game_id" value="<?= $game->getId() ?>">
                    <input type="hidden" name="<?= Application::DTO_MOVE ?>" value='<?= json_encode($move, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                    <button type="submit"><?= Localization::get('game.play.pass_turn') ?></button>
                </form>
            <?php else: ?>
                <form method="POST" action="/game/move">
                    <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
                    <input type="hidden" name="game_id" value="<?= $game->getId() ?>">
                    <input type="hidden" name="<?= Application::DTO_MOVE ?>" value='<?= json_encode($move, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                    <button type="submit">
                        <?= Localization::get('game.play.figure') ?> <?= $move[Application::DTO_FIGURE_INDEX] ?> :
                        <?= Localization::get('game.play.position') ?> <?= $move[Application::DTO_FROM][Application::DTO_AREA] ?? '–' ?> / <?= $move[Application::DTO_FROM][Application::DTO_POSITION] ?? '–' ?> →
                        <?= Localization::get('game.play.position') ?> <?= $move[Application::DTO_TO][Application::DTO_AREA] ?? '–' ?> / <?= $move[Application::DTO_TO][Application::DTO_POSITION] ?? '–' ?>
                    </button>
                </form>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>

<h3><?= Localization::get('game.play.figures') ?></h3>

<?php foreach ($game->getAllPlayers() as $player): ?>
    <h4><?= $player->getUsername() ?></h4>
    <ul>
        <?php foreach ($player->getAllFigures() as $index => $figure): ?>
            <li>
                <?= Localization::get('game.play.figure') ?> <?= $index ?> -
                <?= Localization::get('game.play.area') ?>: <?= $figure->getArea() ?>,
                <?= Localization::get('game.play.position') ?>: <?= $figure->getPosition() ?? '–' ?><?php if ($figure->getArea() === Application::AREA_FIELD): ?>, <?= Localization::get('game.play.absolute_position') ?>: <?= $game->getAbsoluteFieldPosition($player, $figure) ?? '–' ?><?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>

<pre>
<?= print_r($game->getDebugState($user->getId(), $game->getStateModel()->getCurrentDiceRoll()), true) ?>
</pre>