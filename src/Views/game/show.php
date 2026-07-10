<?php

use App\Constants\Application;
use App\Core\Localization;
use App\Core\SystemSettings;
use App\Policies\GamePolicy;

/**
 * @var Object $game
 * @var Object $current_user 
 */


$is_owner = GamePolicy::isOwner($game, $current_user);
$is_admin = $current_user->isAdmin();

$can_edit = GamePolicy::canEdit($game, $current_user);
$can_delete = GamePolicy::canDelete($game, $current_user);

$can_join = GamePolicy::canJoin($game, $current_user);
$can_leave = GamePolicy::canLeave($game, $current_user);

$player_count = $game->getPlayerCount();
$player_max = $game->getPlayerMax();

if ($game->isWaiting()) {
    $status_class = 'status-waiting';
    $status_text = Localization::get('game.status.waiting');
} elseif ($game->isRunning()) {
    $status_class = 'status-running';
    $status_text = Localization::get('game.status.running');
} else {
    $status_class = 'status-finished';
    $status_text = Localization::get('game.status.finished');
}

if ($player_count === 0) {
    $players_class = 'player-count-category-' . Application::DTO_PLAYER_COUNT_EMPTY;
} elseif ($player_count === 1) {
    $players_class = 'player-count-category-' . Application::DTO_PLAYER_COUNT_LOW;
} else {
    $players_class = 'player-count-category-' . Application::DTO_PLAYER_COUNT_READY;
}

$ruleset_text = $game->getRuleSetModel()->isGameClassic() ? Localization::get('game.ruleset.classic') : Localization::get('game.ruleset.custom');

$can_start = $game->isWaiting() && $game->isCreator($current_user);
$can_pause = $game->isRunning() && ($game->isCreator($current_user) || $is_admin);
$can_play = $game->isRunning() && (SystemSettings::isGamePlayEnabled() || $is_admin);
$can_reset = $is_admin;

$can_edit_rules = false;
$can_edit_options = false;

?>

<div class="panel">

    <h1><?= Localization::get('game.show.title') ?></h1>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li>
                <a href="/lobby" class="btn-back">
                    <?= Localization::get('application.general.btn.back_to_lobby') ?>
                </a>
            </li>

            <li>
                <a href="/game/list" class="btn-back">
                    <?= Localization::get('application.general.btn.back_to_list') ?>
                </a>
            </li>
        </ul>
    </div>

    <!-- Game Header -->
    <div
        class="card game-detail"
        data-id="<?= $game->getId() ?>">

        <?php include __DIR__ . '/partials/header.php'; ?>

    </div>

    <!-- Game Information -->
    <div class="card">

        <h2>
            <?= Localization::get('game.show.info') ?>
        </h2>

        <div class="form-row">
            <span><?= Localization::get('game.show.created_by') ?></span>
            <span><?= htmlspecialchars($game->getCreatedByUserName()) ?></span>
        </div>

        <div class="form-row">
            <span><?= Localization::get('game.show.created_at') ?></span>
            <span><?= htmlspecialchars($game->getCreatedAt()) ?></span>
        </div>

        <div class="form-row">
            <span><?= Localization::get('game.show.players') ?></span>
            <span><?= $player_count ?>/<?= $player_max ?></span>
        </div>

        <div class="form-row">
            <span><?= Localization::get('game.show.join') ?></span>
            <span>
                <?= $game->isParticipant($current_user)
                    ? Localization::get('application.general.yes')
                    : Localization::get('application.general.no') ?>
            </span>
        </div>

        <?php if ($game->isFinished() && $game->getWinner()): ?>

            <div class="form-row">
                <span><?= Localization::get('game.show.winner') ?></span>
                <span><?= htmlspecialchars($game->getWinner()->getUsername()) ?></span>
            </div>

        <?php endif; ?>

    </div>

    <!-- Players -->
    <div class="card">

        <h2>
            <?= Localization::get('game.show.players') ?>
        </h2>

        <?php include VIEWS_PATH . '/game/partials/players.php' ?>

    </div>

    <!-- Game Rules -->
    <div class="card">
        <div class="card-header">
            <h2><?= Localization::get('game.create.card.rules.title') ?></h2>

            <div id="restore-ruleset-group" class="btn-badge-group default invisible">
                <button id="restore-ruleset" type="button" class="btn-badge"><?= Localization::get('game.create.card.rules.restore') ?></button>
            </div>
        </div>

        <!-- Starting Rules -->
        <div class="nested-card">

            <h3><?= Localization::get('game.create.card.rules.group.starting.title') ?></h3>

            <div class="form-row">
                <span><?= Localization::get('game.rules.all_figures_start_at_home') ?></span>

                <select name="<?= Application::ALL_FIGURES_START_AT_HOME ?>" data-ui="switch" <?= $can_edit_rules ? '' : 'disabled' ?> >
                    <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getAllFiguresStartAtHome()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getAllFiguresStartAtHome()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>

            <div class="form-row">
                <span><?= Localization::get('game.rules.start_field_must_be_cleared') ?></span>

                <select name="<?= Application::START_FIELD_MUST_BE_CLEARED ?>" data-ui="switch" <?= $can_edit_rules ? '' : 'disabled' ?> >
                    <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getStartFieldMustBeCleared()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getStartFieldMustBeCleared()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>

            <div class="form-row">
                <span><?= Localization::get('game.rules.leave_home_attempt') ?></span>

                <select name="<?= Application::LEAVE_HOME_ATTEMPT ?>" data-ui="switch" <?= $can_edit_rules ? '' : 'disabled' ?> >
                    <option value="<?= Application::ENUM_FIRST_FIGURE ?>" data-state="default" <?= ($game->getRuleSetModel()->getLeaveHomeAttemptVariant() === Application::ENUM_FIRST_FIGURE) ? 'selected' : '' ?> ><?= Localization::get('game.rules.leave_home_attempt_enum_first_figure') ?></option>
                    <option value="<?= Application::ENUM_ALL_FIGURES ?>" data-state="active" <?= ($game->getRuleSetModel()->getLeaveHomeAttemptVariant() === Application::ENUM_ALL_FIGURES) ? 'selected' : '' ?> ><?= Localization::get('game.rules.leave_home_attempt_enum_all_figures') ?></option>
                </select>
            </div>

            <div class="form-row">
                <span><?= Localization::get('game.rules.leave_home_attempts_max') ?></span>

                <select name="<?= Application::LEAVE_HOME_ATTEMPTS_MAX ?>" data-ui="switch" <?= $can_edit_rules ? '' : 'disabled' ?> >
                    <option value="1" data-state="default" <?= ($game->getRuleSetModel()->getLeaveHomeAttemptsMax() === 1) ? 'selected' : '' ?> >1</option>
                    <option value="3" data-state="mid" <?= ($game->getRuleSetModel()->getLeaveHomeAttemptsMax() === 3) ? 'selected' : '' ?> >3</option>
                    <option value="5" data-state="active" <?= ($game->getRuleSetModel()->getLeaveHomeAttemptsMax() === 5) ? 'selected' : '' ?> >5</option>
                </select>
            </div>

            <div class="form-row">
                <span><?= Localization::get('game.rules.force_leaving_home_on_six') ?></span>

                <select name="<?= Application::FORCE_LEAVING_HOME_ON_SIX ?>" data-ui="switch" <?= $can_edit_rules ? '' : 'disabled' ?> >
                    <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getForceLeavingHomeOnSix()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getForceLeavingHomeOnSix()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>

        </div>

        <!-- Movement & Turn Rules -->
        <div class="nested-card">

            <h3><?= Localization::get('game.create.card.rules.group.movement.title') ?></h3>

            <div class="form-row">
                <span><?= Localization::get('game.rules.roll_on_six_limit') ?></span>

                <select name="<?= Application::EXTRA_ROLL_ON_SIX_LIMIT ?>" data-ui="switch" <?= $can_edit_rules ? '' : 'disabled' ?> >
                    <option value="0" data-state="default" <?= ($game->getRuleSetModel()->getExtraRollOnSixLimit() === 0) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="3" data-state="mid" <?= ($game->getRuleSetModel()->getExtraRollOnSixLimit() > 0 && $game->getRuleSetModel()->getAllowStackOwnFigures() < 255) ? 'selected' : '' ?> ><?= Localization::get('game.create.three') ?></option>
                    <option value="255" data-state="active" <?= ($game->getRuleSetModel()->getExtraRollOnSixLimit() === 255) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>

            <div class="form-row">
                <span><?= Localization::get('game.rules.force_extra_lap_on_overflow') ?></span>

                <select name="<?= Application::FORCE_EXTRA_LAP_ON_OVERFLOW ?>" data-ui="switch" <?= $can_edit_rules ? '' : 'disabled' ?> >
                    <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getForceExtraLapOnOverflow()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getForceExtraLapOnOverflow()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>

        </div>

        <!-- Interaction & Goal Rules -->
        <div class="nested-card">

            <h3><?= Localization::get('game.create.card.rules.group.interactions.title') ?></h3>

            <div class="form-row">
                <span><?= Localization::get('game.rules.allow_stack_own_figures') ?></span>

                <select name="<?= Application::ALLOW_STACK_OWN_FIGURES ?>" data-ui="switch" <?= $can_edit_rules ? '' : 'disabled' ?> >
                    <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getAllowStackOwnFigures()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getAllowStackOwnFigures()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>

            <div class="form-row">
                <span><?= Localization::get('game.rules.force_capture_enemy_figures') ?></span>

                <select name="<?= Application::FORCE_CAPTURE_ENEMY_FIGURES ?>" data-ui="switch" <?= $can_edit_rules ? '' : 'disabled' ?> >
                    <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getForceCaptureEnemyFigures()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getForceCaptureEnemyFigures()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>

            <div class="form-row">
                <span><?= Localization::get('game.rules.strict_goal_order') ?></span>

                <select name="<?= Application::STRICT_GOAL_ORDER ?>" data-ui="switch" <?= $can_edit_rules ? '' : 'disabled' ?> >
                    <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getStrictGoalOrder()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getStrictGoalOrder()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>

        </div>

    </div>

    <!-- Game Options -->
    <div class="card">

        <h2><?= Localization::get('game.create.card.options.title') ?></h2>

        <div class="form-row">
            <span><?= Localization::get('game.options.is_private') ?></span>

            <select name="<?= Application::IS_PRIVATE ?>" data-ui="switch" <?= $can_edit_options ? '' : 'disabled' ?> >
                <option value="0" data-state="active" <?= (!$game->isPrivate()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                <option value="1" data-state="inactive" <?= ($game->isPrivate()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
            </select>
        </div>

        <div class="form-row">
            <span><?= Localization::get('game.options.is_locked') ?></span>

            <select name="<?= Application::IS_LOCKED ?>" data-ui="switch" <?= $can_edit_options ? '' : 'disabled' ?> >
                <option value="0" data-state="active" <?= (!$game->isLocked()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                <option value="1" data-state="inactive" <?= ($game->isLocked()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
            </select>
        </div>

        <div class="form-row">
            <span><?= Localization::get('game.rules.allow_bots') ?></span>

            <select name="<?= Application::ALLOW_BOTS ?>" data-ui="switch" <?= $can_edit_options ? '' : 'disabled' ?> >
                <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getAllowBots()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getAllowBots()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
            </select>
        </div>

    </div>

</div>