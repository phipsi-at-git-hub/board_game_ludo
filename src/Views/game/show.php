<?php

use App\Constants\Application;
use App\Core\Localization; 
use App\Policies\GamePolicy;

/**
 * @var Object $game
 * @var Object $current_user 
 */

$can_edit_rules = false;
$can_edit_options = false;

$is_admin_view ??= false; 
$show_metadata ??= false; 
$show_history ??= false; 
$show_start_pause = true; 

$show_players_card = true; 

$is_owner = GamePolicy::isOwner($game, $current_user);
$is_admin = $current_user->isAdmin();
$can_create_test = GamePolicy::canCreateTestGame($game, $current_user); 

$can_start = GamePolicy::canStart($game, $current_user); 
$can_play = GamePolicy::canPlay($game, $current_user); 
$can_pause = GamePolicy::canPause($game, $current_user); 

$can_edit = GamePolicy::canEdit($game, $current_user);
$can_reset = GamePolicy::canReset($game, $current_user); 
$can_cancel = GamePolicy::canCancel($game, $current_user); 
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
} elseif ($game->isFinished()) {
    $status_class = 'status-finished';
    $status_text = Localization::get('game.status.finished');
} elseif ($game->isCancelled()) {
    $status_class = 'status-cancelled';
    $status_text = Localization::get('game.status.cancelled');
}

if ($player_count === 0) {
    $players_class = 'player-count-category-' . Application::DTO_PLAYER_COUNT_EMPTY;
} elseif ($player_count === 1) {
    $players_class = 'player-count-category-' . Application::DTO_PLAYER_COUNT_LOW;
} else {
    $players_class = 'player-count-category-' . Application::DTO_PLAYER_COUNT_READY;
}

$ruleset_text = Localization::get('game.ruleset.' . $game->getRuleSetModel()->getPreset());
$api_delete = false; 

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

    <!-- Admin Partials -->
    <?php 
    if ($is_admin_view && $show_metadata) { 
        include VIEWS_PATH . '/admin/game/partials/metadata.php';   
    } 
    ?>

    <?php 
    if ($is_admin_view && $show_history) { 
        include VIEWS_PATH . '/admin/game/partials/history.php';   
    } 
    ?>

    <!-- Players -->
    <div class="card">

        <h2>
            <?= Localization::get('game.show.players') ?>
        </h2>

        <!--<div data-view-bind="players">-->
        <div 
            data-id="game-<?= $game->getId() ?>-players" 
            data-bind-sources="game-<?= $game->getId() ?>-join, game-<?= $game->getId() ?>-leave" 
            data-bind-1-view-key="players" 
            data-bind-1-type="view" >
        
            <?php include VIEWS_PATH . '/game/partials/players.php' ?>

        </div>

    </div>

    <!-- Game Rules -->
    <div class="card">
        <div class="card-header">
            <h2><?= Localization::get('game.create.card.rules.title') ?></h2>

            <div id="restore-ruleset-group" class="btn-control-group default invisible">
                <button id="restore-ruleset" type="button" class="btn-control"><?= Localization::get('game.create.card.rules.restore') ?></button>
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