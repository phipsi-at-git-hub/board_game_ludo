<?php 
use App\Core\Csrf; 
use App\Constants\Application;
use App\Core\Localization;

/**
 * @var \App\Models\GameModel $game 
 */
?>

<div class="panel">
    <h1><?= Localization::get('game.edit.title') ?></h1>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li><a href="/lobby" class="btn-back"><?= Localization::get('application.general.btn.back_to_lobby') ?></a></li>
            <li><a href="/game/list" class="btn-back"><?= Localization::get('application.general.btn.back_to_list') ?></a></li>
        </ul>
    </div>

    <form method="POST" action="/game/update">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
        <input type="hidden" name="game_id" value="<?= $game->getId() ?>">

        <fieldset>
            <legend><?= Localization::get('game.create.name') ?></legend>
            <label>
                <?= Localization::get('game.create.game_name') ?>
                <input type="text" name="<?= Application::GAME_NAME ?>" value="<?= $game->getName() ?>">
            </label>
        </fieldset>

        <fieldset>
            <legend><?= Localization::get('game.create.game_options') ?></legend>
            <div class="form-row">
                <?= Localization::get('game.options.is_private') ?>

                <select name="<?= Application::IS_PRIVATE; ?>" data-ui="switch" class="enhanced">
                    <option value="0" data-state="active" <?= (!$game->isPrivate()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="inactive" <?= ($game->isPrivate()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>
            <br>
            <div class="form-row">
                <?= Localization::get('game.options.is_locked') ?>

                <select name="<?= Application::IS_LOCKED; ?>" data-ui="switch" class="enhanced">
                    <option value="0" data-state="active" <?= (!$game->isLocked()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="inactive" <?= ($game->isLocked()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>
        </fieldset>

        <fieldset>
            <legend><?= Localization::get('game.create.game_rules') ?></legend>
            <div class="form-row">
                <?= Localization::get('game.rules.allow_bots') ?>

                <select name="<?= Application::ALLOW_BOTS; ?>" data-ui="switch" class="enhanced">
                    <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getAllowBots()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getAllowBots()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>
            <br>
            <div class="form-row">
                <?= Localization::get('game.rules.all_figures_start_at_home') ?>

                <?php if ($game->editAllowedRuleAllFiguresStartAtHome()): ?>
                <select name="<?= Application::ALL_FIGURES_START_AT_HOME; ?>" data-ui="switch" class="enhanced">
                    <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getAllFiguresStartAtHome()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getAllFiguresStartAtHome()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
                <?php else: ?>
                    <span class="form-edit-not-allowed"><?= ($game->getRuleSetModel()->getAllFiguresStartAtHome()) ? Localization::get('application.general.yes') : Localization::get('application.general.no') ?></span>
                    <input type="hidden" name="<?= Application::ALL_FIGURES_START_AT_HOME; ?>" value="<?= ($game->getRuleSetModel()->getAllFiguresStartAtHome()) ? 1 : 0 ?>">
                <?php endif; ?>
            </div>
            <br>
            <div class="form-row">
                <?= Localization::get('game.rules.start_field_must_be_cleared') ?>

                <select name="<?= Application::START_FIELD_MUST_BE_CLEARED; ?>" data-ui="switch" class="enhanced">
                    <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getStartFieldMustBeCleared()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getStartFieldMustBeCleared()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>
            <br>
            <div class="form-row">
                <?= Localization::get('game.rules.leave_home_attempt') ?>

                <select name="<?php echo Application::LEAVE_HOME_ATTEMPT; ?>" data-ui="switch" class="enhanced">
                    <option value="<?= Application::ENUM_FIRST_FIGURE ?>" data-state="default" <?= ($game->getRuleSetModel()->getLeaveHomeAttemptVariant() === Application::ENUM_FIRST_FIGURE) ? 'selected' : '' ?> ><?= Localization::get('game.rules.leave_home_attempt_enum_first_figure') ?></option>
                    <option value="<?= Application::ENUM_ALL_FIGURES ?>" data-state="active" <?= ($game->getRuleSetModel()->getLeaveHomeAttemptVariant() === Application::ENUM_ALL_FIGURES) ? 'selected' : '' ?> ><?= Localization::get('game.rules.leave_home_attempt_enum_all_figures') ?></option>
                </select>
            </div>
            <br>
            <div class="form-row">
                <?= Localization::get('game.rules.leave_home_attempts_max') ?>

                <select name="<?php echo Application::LEAVE_HOME_ATTEMPTS_MAX; ?>" data-ui="switch" class="enhanced">
                    <option value="1" data-state="default" <?= ($game->getRuleSetModel()->getLeaveHomeAttemptsMax() === 1) ? 'selected' : '' ?> >1</option>
                    <option value="3" data-state="mid" <?= ($game->getRuleSetModel()->getLeaveHomeAttemptsMax() === 3) ? 'selected' : '' ?> >3</option>
                    <option value="5" data-state="active" <?= ($game->getRuleSetModel()->getLeaveHomeAttemptsMax() === 5) ? 'selected' : '' ?> >5</option>
                </select>
            </div>
            <br>
            <div class="form-row">
                <?= Localization::get('game.rules.roll_on_six_limit') ?>

                <select name="<?= Application::EXTRA_ROLL_ON_SIX_LIMIT; ?>" data-ui="switch" class="enhanced">
                    <option value="0" data-state="default" <?= ($game->getRuleSetModel()->getExtraRollOnSixLimit() === 0) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="3" data-state="mid" <?= ($game->getRuleSetModel()->getExtraRollOnSixLimit() > 0 && $game->getRuleSetModel()->getAllowStackOwnFigures() < 255) ? 'selected' : '' ?> ><?= Localization::get('game.create.three') ?></option>
                    <option value="255" data-state="active" <?= ($game->getRuleSetModel()->getExtraRollOnSixLimit() === 255) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>
            <br>
            <div class="form-row">
                <?= Localization::get('game.rules.force_leaving_home_on_six') ?>

                <select name="<?php echo Application::FORCE_LEAVING_HOME_ON_SIX; ?>" data-ui="switch" class="enhanced">
                    <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getForceLeavingHomeOnSix()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getForceLeavingHomeOnSix()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>
            <br>
            <div class="form-row">
                <?= Localization::get('game.rules.force_capture_enemy_figures') ?>

                <select name="<?php echo Application::FORCE_CAPTURE_ENEMY_FIGURES; ?>" data-ui="switch" class="enhanced">
                    <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getForceCaptureEnemyFigures()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getForceCaptureEnemyFigures()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>
            <br>
            <div class="form-row">
                <?= Localization::get('game.rules.force_extra_lap_on_overflow') ?>

                <select name="<?php echo Application::FORCE_EXTRA_LAP_ON_OVERFLOW; ?>" data-ui="switch" class="enhanced">
                    <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getForceExtraLapOnOverflow()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getForceExtraLapOnOverflow()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>
            <br>
            <div class="form-row">
                <?= Localization::get('game.rules.allow_stack_own_figures') ?>

                <select name="<?= Application::ALLOW_STACK_OWN_FIGURES; ?>" data-ui="switch" class="enhanced">
                    <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getAllowStackOwnFigures()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getAllowStackOwnFigures()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>
            <br>
            <div class="form-row">
                <?= Localization::get('game.rules.strict_goal_order') ?>

                <select name="<?= Application::STRICT_GOAL_ORDER; ?>" data-ui="switch" class="enhanced">
                    <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getStrictGoalOrder()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getStrictGoalOrder()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>
        </fieldset>

        <div class="nav-actions">
            <button type="submit" class="btn btn-save"><?= Localization::get('game.edit.button_update') ?></button>
        </div>
    </form>
</div>
