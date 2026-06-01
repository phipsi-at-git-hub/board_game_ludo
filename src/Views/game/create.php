<?php 
use App\Core\Csrf; 
use App\Constants\Application;
use App\Core\Localization;

/**
 * @var \App\Models\GameRuleSetModel $rule_set
 */
?>

<div class="panel">
    <h1><?= Localization::get('game.create.title') ?></h1>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li><a href="/lobby" class="btn-back"><?= Localization::get('application.general.btn.back_to_lobby') ?></a></li>
        </ul>
    </div>

    <form method="POST" action="/game/store">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">

        <fieldset>
            <legend><?= Localization::get('game.create.name') ?></legend>
            <label>
                <?= Localization::get('game.create.game_name') ?>
                <input type="text" name="<?= Application::GAME_NAME ?>">
            </label>
        </fieldset>

        <fieldset>
            <legend><?= Localization::get('game.create.game_options') ?></legend>
            <label>
                <?= Localization::get('game.options.is_private') ?>
                <select name="<?= Application::IS_PRIVATE; ?>">
                    <option value="0" selected><?= Localization::get('application.general.no') ?></option>
                    <option value="1"><?= Localization::get('application.general.yes') ?></option>
                </select>
            </label>
            <br><br>
            <label>
                <?= Localization::get('game.options.is_locked') ?>
                <select name="<?= Application::IS_LOCKED; ?>">
                    <option value="0" selected><?= Localization::get('application.general.no') ?></option>
                    <option value="1"><?= Localization::get('application.general.yes') ?></option>
                </select>
            </label>
        </fieldset>

        <fieldset>
            <legend><?= Localization::get('game.create.game_rules') ?></legend>

            <label>
                <?= Localization::get('game.rules.allow_bots') ?>
                <select name="<?= Application::ALLOW_BOTS; ?>">
                    <option value="0" <?= (!$rule_set->getAllowBots()) ? 'selected' : '' ?>><?= Localization::get('application.general.no') ?></option>
                    <option value="1" <?= ($rule_set->getAllowBots()) ? 'selected' : '' ?>><?= Localization::get('application.general.yes') ?></option>
                </select>
            </label>
            <br><br>

            <label>
                <?= Localization::get('game.rules.all_figures_start_at_home') ?>
                <select name="<?= Application::ALL_FIGURES_START_AT_HOME; ?>">
                    <option value="0" <?= (!$rule_set->getAllFiguresStartAtHome()) ? 'selected' : '' ?>><?= Localization::get('application.general.no') ?></option>
                    <option value="1" <?= ($rule_set->getAllFiguresStartAtHome()) ? 'selected' : '' ?>><?= Localization::get('application.general.yes') ?></option>
                </select>
            </label>
            <br><br>

            <label>
                <?= Localization::get('game.rules.start_field_must_be_cleared') ?>
                <select name="<?= Application::START_FIELD_MUST_BE_CLEARED; ?>">
                    <option value="0" <?= (!$rule_set->getStartFieldMustBeCleared()) ? 'selected' : '' ?>><?= Localization::get('application.general.no') ?></option>
                    <option value="1" <?= ($rule_set->getStartFieldMustBeCleared()) ? 'selected' : '' ?>><?= Localization::get('application.general.yes') ?></option>
                </select>
            </label>
            <br><br>
            <label>
                <?= Localization::get('game.rules.leave_home_attempt') ?>
                <select name="<?php echo Application::LEAVE_HOME_ATTEMPT; ?>">
                    <option value="<?= Application::ENUM_FIRST_FIGURE ?>" <?= ($rule_set->getLeaveHomeAttemptVariant() === Application::ENUM_FIRST_FIGURE) ? 'selected' : '' ?> ><?= Localization::get('game.rules.leave_home_attempt_enum_first_figure') ?></option>
                    <option value="<?= Application::ENUM_ALL_FIGURES ?>" <?= ($rule_set->getLeaveHomeAttemptVariant() === Application::ENUM_ALL_FIGURES) ? 'selected' : '' ?> ><?= Localization::get('game.rules.leave_home_attempt_enum_all_figures') ?></option>
                </select>
            </label>
            <br><br>
            <label>
                <?= Localization::get('game.rules.leave_home_attempts_max') ?>
                <select name="<?php echo Application::LEAVE_HOME_ATTEMPTS_MAX; ?>">
                    <option value="1" <?= ($rule_set->getLeaveHomeAttemptsMax() === 1) ? 'selected' : '' ?> >1</option>
                    <option value="3" <?= ($rule_set->getLeaveHomeAttemptsMax() === 3) ? 'selected' : '' ?> >3</option>
                    <option value="5" <?= ($rule_set->getLeaveHomeAttemptsMax() === 5) ? 'selected' : '' ?> >5</option>
                </select>
            </label>
            <br><br>
            <label>
                <?= Localization::get('game.rules.roll_on_six_limit') ?>
                <select name="<?php echo Application::EXTRA_ROLL_ON_SIX_LIMIT; ?>">
                    <option value="0" <?= ($rule_set->getExtraRollOnSixLimit() === 0) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="3" <?= ($rule_set->getExtraRollOnSixLimit() > 0 && $rule_set->getAllowStackOwnFigures() < 255) ? 'selected' : '' ?> ><?= Localization::get('game.create.three') ?></option>
                    <option value="255" <?= ($rule_set->getExtraRollOnSixLimit() === 255) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </label>
            <br><br>
            <label>
                <?= Localization::get('game.rules.force_leaving_home_on_six') ?>
                <select name="<?php echo Application::FORCE_LEAVING_HOME_ON_SIX; ?>">
                    <option value="0" <?= (!$rule_set->getForceLeavingHomeOnSix()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" <?= ($rule_set->getForceLeavingHomeOnSix()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </label>
            <br><br>
            <label>
                <?= Localization::get('game.rules.force_capture_enemy_figures') ?>
                <select name="<?php echo Application::FORCE_CAPTURE_ENEMY_FIGURES; ?>">
                    <option value="0" <?= (!$rule_set->getForceCaptureEnemyFigures()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" <?= ($rule_set->getForceCaptureEnemyFigures()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </label>
            <br><br>
            <label>
                <?= Localization::get('game.rules.force_extra_lap_on_overflow') ?>
                <select name="<?php echo Application::FORCE_EXTRA_LAP_ON_OVERFLOW; ?>">
                    <option value="0" <?= (!$rule_set->getForceExtraLapOnOverflow()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" <?= ($rule_set->getForceExtraLapOnOverflow()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </label>
            <br><br>
            <label>
                <?= Localization::get('game.rules.allow_stack_own_figures') ?>
                <select name="<?php echo Application::ALLOW_STACK_OWN_FIGURES; ?>">
                    <option value="0" <?= (!$rule_set->getAllowStackOwnFigures()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" <?= ($rule_set->getAllowStackOwnFigures()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </label>
            <br><br>
            <label>
                <?= Localization::get('game.rules.strict_goal_order') ?>
                <select name="<?php echo Application::STRICT_GOAL_ORDER; ?>">
                    <option value="0" <?= (!$rule_set->getStrictGoalOrder()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" <?= ($rule_set->getStrictGoalOrder()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </label>
        </fieldset>

        <div class="nav-actions">
            <button type="submit" class="btn btn-save"><?= Localization::get('game.create.button_create') ?></button>
        </div>
    </form>
</div>