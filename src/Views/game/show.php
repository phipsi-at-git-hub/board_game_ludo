<?php
use App\Constants\Application;
use App\Core\Csrf;
use App\Core\Localization;
use App\Core\SystemSettings;

/**
 * @var Object $game 
 * @var Object $user
 */
?>

<div class="panel">
    <h1><?= Localization::get('game.show.title') ?> 🎮 <?= htmlspecialchars($game->getName()) ?></h1>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li><a href="/lobby" class="btn-back"><?= Localization::get('application.general.btn.back_to_lobby') ?></a></li>
            <li><a href="/game/list" class="btn-back"><?= Localization::get('application.general.btn.back_to_list') ?></a></li>
        </ul>
    </div>

    <!-- Status & Actions -->
    <div class="card game-card">
        <!--<h2><?= Localization::get('game.show.info') ?></h2>-->
        <div class="game-card-header">
            <h2 class="game-tile">🎮 <?= htmlspecialchars($game->getName()) ?></h2>
            <div class="status-badges">
                <span class="status-badge status-<?= strtolower($game->getStatus()) ?>">
                    <?= htmlspecialchars($game->getStatus()) ?>
                </span>
                <span class="status-badge <?= ($game->isPrivate()) ? 'is' : 'is-not' ?>-private-locked">
                    <?= htmlspecialchars(($game->isPrivate()) ? 'Private' : 'Open') ?>
                </span>
                <span class="status-badge <?= ($game->isLocked()) ? 'is' : 'is-not' ?>-private-locked">
                    <?= htmlspecialchars(($game->isLocked()) ? 'Locked' : 'Unlocked') ?>
                </span>
            </div>

            <div class="card-actions">
                    <?php if (!$game->isFinished()): ?>
                        <form method="POST" action="/game/<?= ($game->isParticipant($user)) ? 'leave' : 'join' ?>/<?= $game->getId() ?>">
                            <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
                            <button class="btn btn-<?= ($game->isParticipant($user)) ? 'danger' : 'save' ?>">
                                <?= htmlspecialchars(($game->isParticipant($user)) ? Localization::get('game.show.leave') : Localization::get('game.show.join')) ?>
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if ($user->isAdmin() && !$game->IsTestGame()): ?>
                        <form method="POST" action="/game/create_solo_test" onsubmit="return confirm('<?= Localization::get('game.show.solo_test_creation_confirm') ?>');">
                            <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
                            <input type="hidden" name="game_id" value="<?= $game->getId() ?>">
                            <button type="submit" class="btn btn-secondary create"><?= Localization::get('game.show.test_solo_create') ?></button>
                        </form>
                    <?php endif; ?>

                    <?php if ($game->isCreator($user) && $game->isWaiting()): ?>
                        <form method="POST" action="/game/start">
                            <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
                            <input type="hidden" name="game_id" value="<?= $game->getId() ?>">
                            <button type="submit" class="btn btn-save play"><?= Localization::get('game.show.start') ?></button>
                        </form>
                    <?php endif; ?>

                    <?php if ($game->isRunning() && ($game->isCreator($user) || $user->isAdmin())): ?>
                        <form method="POST" action="/game/pause">
                            <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
                            <input type="hidden" name="game_id" value="<?= $game->getId() ?>">
                            <button type="submit" class="btn btn-secondary"><?= Localization::get('game.show.pause') ?></button>
                        </form>
                    <?php endif; ?>

                    <?php if ($user->isAdmin()): ?>
                        <form method="POST" action="/game/reset">
                            <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
                            <input type="hidden" name="game_id" value="<?= $game->getId() ?>">
                            <button type="submit" class="btn btn-secondary"><?= Localization::get('game.show.reset') ?></button>
                        </form>
                    <?php endif; ?>

                    <?php if ($game->isRunning() && (SystemSettings::isGamePlayEnabled() || $user->isAdmin())): ?>
                        <a href="/game/play/<?= $game->getId() ?>" class="btn btn-save play"><?= Localization::get('game.show.play') ?></a>
                    <?php endif; ?>
            </div>
        </div>

        <!-- Meta-Infos -->
        <div class="card-body meta-grid">
            <div><?= Localization::get('game.show.created_by') ?></div>
            <div><?= htmlspecialchars($game->getCreatedByUserName()) ?></div>

            <div><?= Localization::get('game.show.created_at') ?></div>
            <div><?= htmlspecialchars($game->getCreatedAt()) ?></div>

            <div><?= Localization::get('game.show.players') ?></div>
            <div><?= count($game->getAllPlayers()) ?></div>

            <?php if ($game->isFinished()): ?>
                <div><?= Localization::get('game.show.winner') ?></div>
                <div><?= $game->getWinner()->getUsername() ?></div>
            <?php endif; ?>

            <div><?= Localization::get('game.show.join') ?></div>
            <div><?= ($game->isParticipant($user)) ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></div>
        </div>
    </div>

    <!-- List of Players -->
    <div class="card">
        <h2><?= Localization::get('game.show.players') ?></h2>

        <div class="card players-container">
            <?php if (!empty($game->getAllPlayers())): ?>
                <?php foreach ($game->getAllPlayers() as $player): ?>
                    <div class="player-card">
                        <h3>
                            <?php if ($player->getUserId() === $user->getId()) echo '➡️'; ?>
                            🧑 <?= htmlspecialchars($player->getUsername()) ?>
                            <?php if ($player->getUserId() === $game->getStateModel()->getWinnerUserId()) echo '👑'; ?>
                        </h3>
                        <div class="figure-row">
                            <?php foreach ($player->getAllFigures() as $figure): ?>
                                <div class="figure-badge">
                                    ♟ <?= $figure->getFigureIndex() ?>
                                    <small><?= htmlspecialchars($figure->getArea()) ?> (<?= $figure->getPosition() ?>)</small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p><?= Localization::get('game.show.label_no_players_found') ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Game Rule Set -->
    <div class="card">
        <h2><?= Localization::get('game.show.rules') ?></h2>
        <ul class="rules-list">
            <li><?= Localization::get('game.show.label_rules_bots_allows') ?>: <?= $game->getRuleSetModel()->getAllowBots() ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></li>
            <li><?= Localization::get('game.show.label_rules_all_figures_start_at_home') ?>: <?= $game->getRuleSetModel()->getAllFiguresStartAtHome() ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></li>
            <li><?= Localization::get('game.show.label_rules_start_field_must_be_cleared') ?>: <?= $game->getRuleSetModel()->getStartFieldMustBeCleared() ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></li>
            <li><?= Localization::get('game.show.label_rules_leave_home_attempt') ?>: <?= $game->getRuleSetModel()->getLeaveHomeAttemptVariant() === Application::ENUM_FIRST_FIGURE ? Localization::get('game.show.label_rules_leave_home_attempt_enum_first_figure') : Localization::get('game.show.label_rules_leave_home_attempt_enum_all_figures') ?></li>
            <li><?= Localization::get('game.show.label_rules_leave_home_attempts_max') ?>: <?= $game->getRuleSetModel()->getLeaveHomeAttemptsMax() ?></li>
            <li><?= Localization::get('game.show.label_rules_roll_on_six_limit') ?>: <?php 
                if ($game->getRuleSetModel()->getExtraRollOnSixLimit() === 0) { 
                    echo Localization::get('game.show.label_rules_roll_on_six_limit_no'); 
                } elseif ($game->getRuleSetModel()->getExtraRollOnSixLimit() === 255) { 
                    echo Localization::get('game.show.label_rules_roll_on_six_limit_unlimited'); 
                } else { 
                    echo Localization::get('game.show.label_rules_roll_on_six_limit_limited') . $game->getRuleSetModel()->getExtraRollOnSixLimit();
                } 
            ?></li>
            <li><?= Localization::get('game.show.label_rules_force_leaving_home_on_six') ?>: <?= $game->getRuleSetModel()->getForceLeavingHomeOnSix() ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></li>
            <li><?= Localization::get('game.show.label_rules_force_capture_enemy_figures') ?>: <?= $game->getRuleSetModel()->getForceCaptureEnemyFigures() ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></li>
            <li><?= Localization::get('game.show.label_rules_force_extra_lap_on_overflow') ?>: <?= $game->getRuleSetModel()->getForceExtraLapOnOverflow() ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></li>
            <li><?= Localization::get('game.show.label_rules_stack_own_figures') ?>: <?= $game->getRuleSetModel()->getAllowStackOwnFigures() ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></li>
            <li><?= Localization::get('game.show.label_rules_strict_goal_order') ?>: <?= $game->getRuleSetModel()->getStrictGoalOrder() ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></li>
        </ul>
    </div>
</div>