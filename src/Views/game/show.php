<?php

use App\Core\Localization;
?>
<h1><?= Localization::get('game.show.title') ?> <?= htmlspecialchars($game->getName()) ?></h1>

<div class="game-meta">
    <p><strong><?= Localization::get('game.show.status') ?>:</strong> <?= htmlspecialchars($game->getStatus()) ?></p>
    <p><strong><?= Localization::get('game.show.created_at') ?>:</strong> <?= htmlspecialchars($game->getCreatedAt()) ?></p>
    <p><strong><?= Localization::get('game.show.player') ?>:</strong> <?= count($game->players ?? []) ?></p>
</div>

<h2>Spieler</h2>

<?php if (!empty($game->getAllPlayer())): ?>
    <ul class="player-list">
        <?php foreach ($game->getAllPlayer() as $player): ?>
            <li>
                <?= htmlspecialchars($player['username']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Keine Spieler vorhanden.</p>
<?php endif; ?>

<h2>Figuren</h2>

<?php if (!empty($game->getAllFigures())): ?>

    <?php foreach ($game->getAllPlayer() as $player): ?>
        <h3><?= htmlspecialchars($player['username']) ?></h3>
        <ul>
            <?php foreach ($game->getAllFigures() as $figure): ?>
                <?php if ($figure['user_id'] == $player['user_id']): ?>
                    <li>
                        Figur <?= $figure['figure_index'] ?> –
                        Position: <?= $figure['position'] ?> –
                        Bereich: <?= htmlspecialchars($figure['area']) ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>

<?php else: ?>
    <p>Keine Figuren vorhanden.</p>
<?php endif; ?>

<h2>Regeln</h2>

<ul>
    <li>Bots erlaubt: <?= $game->getRuleSetModel()->getAllowBots() ? 'Ja' : 'Nein' ?></li>
    <li>Extra-Wurf bei 6 Limit: <?php if ($game->getRuleSetModel()->getExtraRollLimit() === 0) { echo 'Kein nochmaliges Würfeln nach 6.'; } elseif ($game->getRuleSetModel()->getExtraRollLimit() === 255) { echo 'Nach jeder 6 ein weiteres Würfeln.'; } else { echo "Zusätzliches Würfeln nach einer 6 ist auf ". $game->getRuleSetModel()->getExtraRollLimit() . " Würfe begrenzt.";} ?></li>
    <li>Eigene Figuren stapeln: <?= $game->getRuleSetModel()->getAllowStackOwnFigures() ? 'Ja' : 'Nein' ?></li>
    <li>Strenge Zielfeld-Reihenfolge: <?= $game->getRuleSetModel()->getStrictGoalOrder() ? 'Ja' : 'Nein' ?></li>
    <li>Startfeld muss frei sein: <?= $game->getRuleSetModel()->getStartFieldMustBeCleared() ? 'Ja' : 'Nein' ?></li>
</ul>