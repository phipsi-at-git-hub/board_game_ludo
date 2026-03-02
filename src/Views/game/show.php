<?php

use App\Constants\Application;
use App\Core\Csrf;
use App\Core\Localization;
use App\Policies\GamePolicy;

?>
<h1><?= Localization::get('game.show.title') ?> 🎮 <?= htmlspecialchars($game->getName()) ?></h1>

<div class="back-link">
    <ul class="nav-list">
        <li><a href="/game/list" class="btn-back"><?= Localization::get('game.show.back_to_list') ?></a></li>
        <li><a href="/lobby" class="btn-back"><?= Localization::get('game.list.back_to_lobby') ?></a></li>
        <li><a href="/menu" class="btn-back"><?= Localization::get('game.lobby.back_to_menu') ?></a></li>
    </ul>
</div>

<div class="card game-card">
    <div class="card-header">
        <span class="status-badge status-<?= strtolower($game->getStatus()) ?>">
            <?= htmlspecialchars($game->getStatus()) ?>
        </span>
        <span class="status-badge <?= ($game->isPrivate()) ? 'is' : 'is-not' ?>-private">
            <?= htmlspecialchars(($game->isPrivate()) ? 'Private' : 'Open') ?>
        </span>
        <span class="status-badge <?= ($game->isLocked()) ? 'is' : 'is-not' ?>-locked">
            <?= htmlspecialchars(($game->isLocked()) ? 'Locked' : 'Unlocked') ?>
        </span>

        <?php if ($game->isParticipant($user)) { ?>
        <?php } ?>

        <form method="POST" action="/game/<?= ($game->isParticipant($user)) ? 'leave' : 'join' ?>/<?= $game->getId() ?>">
        <?php if (GamePolicy::canJoin($user, $game, false)) { ?>
            <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
            <button class="joined-btn <?= ($game->isParticipant($user)) ? 'has' : 'has-not' ?>-joined">
                <?= htmlspecialchars(($game->isParticipant($user)) ? Localization::get('game.show.leave') : Localization::get('game.show.join')) ?>
            </button>
        <?php } else { ?>
            <button class="joined-btn can-not-join"><?= Localization::get('game.show.join') ?></button>
        <?php } ?>
        </form>

        <?php if ($user->isAdmin() && !$game->IsTestGame()) { ?>
        <form method="POST" action="/game/solo_test" onsubmit="return confirm('<?= Localization::get('game.show.solo_test_creation_confirm') ?>');">
            <input type="hidden" name="_method" value="POST">
            <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
            <input type="hidden" name="game_id" value="<?= $game->getId() ?>">
            <button type="submit" class="solo-test-btn"><?= Localization::get('game.list.test_solo') ?></button>
        </form>
        <?php } ?>
    </div>

    <div class="card-body meta-grid">
        <div><?= Localization::get('game.show.created_by') ?></div>
        <div><?= htmlspecialchars($game->getCreatedByUserName()) ?></div>
        <div><?= Localization::get('game.show.created_at') ?></div>
        <div><?= htmlspecialchars($game->getCreatedAt()) ?></div>
        <div><?= Localization::get('game.show.players') ?></div>
        <div><?= count($game->getAllPlayer()) ?></div>
        <div><?= Localization::get('game.show.join') ?></div>
        <div><?= ($game->isParticipant($user)) ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></div>
    </div>

</div>

<div class="card">
    <h2><?= Localization::get('game.show.players') ?></h2>

    <?php if (!empty($game->getAllPlayer())): ?>
        <?php foreach ($game->getAllPlayer() as $player): ?>
            <div class="player-card">
                <h3><?php if ($player->getUserId() === $user->getId()) { ?>➡️<?php } ?>🧑 <?= htmlspecialchars($player->getUsername()) ?></h3>
                <div class="figure-row">
                    <?php foreach ($player->getAllFigures() as $figure): ?>
                        <div class="figure-badge">
                            ♟ <?= $figure->getFigureIndex() ?>
                            <small>
                                <?= htmlspecialchars($figure->getArea()) ?>
                                (<?= $figure->getPosition() ?>)
                            </small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p><?= Localization::get('game.show.label_no_players_found') ?></p>
    <?php endif; ?>
</div>

<div class="card">
    <h2><?= Localization::get('game.show.rules') ?></h2>
    <ul class="rules-list">
        <li><?= Localization::get('game.show.label_rules_bots_allows') ?>: <?= $game->getRuleSetModel()->getAllowBots() ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></li>
        <li><?= Localization::get('game.show.label_rules_leave_home_attempt') ?>: <?= $game->getRuleSetModel()->getLeaveHomeAttemptVariant() === Application::ENUM_FIRST_FIGURE ? Localization::get('game.show.label_rules_leave_home_attempt_enum_first_figure') : Localization::get('game.show.label_rules_leave_home_attempt_enum_all_figures') ?></li>
        <li><?= Localization::get('game.show.label_rules_leave_home_attempts_max') ?>: <?= $game->getRuleSetModel()->getLeaveHomeAttemptsMax() ?></li>
        <li><?= Localization::get('game.show.label_rules_roll_on_six_limit') ?>: <?php if ($game->getRuleSetModel()->getExtraRollOnSixLimit() === 0) { echo Localization::get('game.show.label_rules_roll_on_six_limit_no'); } elseif ($game->getRuleSetModel()->getExtraRollOnSixLimit() === 255) { echo Localization::get('game.show.label_rules_roll_on_six_limit_unlimited'); } else { echo Localization::get('game.show.label_rules_roll_on_six_limit_limited'). $game->getRuleSetModel()->getExtraRollOnSixLimit();} ?></li>
        <li><?= Localization::get('game.show.label_rules_force_extra_lap_on_overflow') ?>: <?= $game->getRuleSetModel()->getForceExtraLapOnOverflow() ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></li>
        <li><?= Localization::get('game.show.label_rules_stack_own_figures') ?>: <?= $game->getRuleSetModel()->getAllowStackOwnFigures() ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></li>
        <li><?= Localization::get('game.show.label_rules_strict_goal_order') ?>: <?= $game->getRuleSetModel()->getStrictGoalOrder() ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></li>
        <li><?= Localization::get('game.show.label_rules_start_field_must_be_cleared') ?>: <?= $game->getRuleSetModel()->getStartFieldMustBeCleared() ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></li>
    </ul>
</div>
