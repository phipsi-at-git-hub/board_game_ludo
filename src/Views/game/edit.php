<?php

use App\Core\Csrf;
use App\Constants\Application;
use App\Core\Localization;

/**
 * @var \App\Models\GameModel $game
 * @var \App\Models\UserModel $user
 * @var array $rule_set_presets 
 * @var array $rule_set_original 
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

    <form method="POST" action="/game/update" form-game-rules>

        <input
            type="hidden"
            name="_csrf_token"
            value="<?= Csrf::generate() ?>">
        <input 
            type="hidden" 
            name="game_id" 
            value="<?= $game->getId() ?>"

        <!-- Game -->
        <div class="card">
            <h2><?= Localization::get('game.create.card.game.title') ?></h2>

                <!-- Game Name -->
                <div class="form-group">
                    <label for="username">
                        <?= Localization::get('game.create.card.game.name') ?>
                    </label>

                    <input
                        type="text"
                        name="<?= Application::GAME_NAME ?>" 
                        value="<?= $game->getName() ?>"
                        required>
                </div>

                <!-- Game Ruleset -->
                <div class="form-row">
                    <span><?= Localization::get('game.create.card.game.ruleset') ?></span>

                    <select name="<?= Application::DTO_RULESET ?>" data-ui="switch">
                        <option value="classic" data-state="active" selected>
                            <?= Localization::get('game.ruleset.classic') ?>
                        </option>

                        <option value="advanced" data-state="mid">
                            <?= Localization::get('game.ruleset.advanced') ?>
                        </option>

                        <option value="custom" data-state="default">
                            <?= Localization::get('game.ruleset.custom') ?>
                        </option>
                    </select>
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

                    <select name="<?= Application::ALL_FIGURES_START_AT_HOME ?>" data-ui="switch" <?= (!$game->editAllowedRuleAllFiguresStartAtHome()) ? 'disabled' : '' ?> >
                        <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getAllFiguresStartAtHome()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                        <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getAllFiguresStartAtHome()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                    </select>
                </div>

                <div class="form-row">
                    <span><?= Localization::get('game.rules.start_field_must_be_cleared') ?></span>

                    <select name="<?= Application::START_FIELD_MUST_BE_CLEARED ?>" data-ui="switch">
                        <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getStartFieldMustBeCleared()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                        <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getStartFieldMustBeCleared()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                    </select>
                </div>

                <div class="form-row">
                    <span><?= Localization::get('game.rules.leave_home_attempt') ?></span>

                    <select name="<?= Application::LEAVE_HOME_ATTEMPT ?>" data-ui="switch">
                        <option value="<?= Application::ENUM_FIRST_FIGURE ?>" data-state="default" <?= ($game->getRuleSetModel()->getLeaveHomeAttemptVariant() === Application::ENUM_FIRST_FIGURE) ? 'selected' : '' ?> ><?= Localization::get('game.rules.leave_home_attempt_enum_first_figure') ?></option>
                        <option value="<?= Application::ENUM_ALL_FIGURES ?>" data-state="active" <?= ($game->getRuleSetModel()->getLeaveHomeAttemptVariant() === Application::ENUM_ALL_FIGURES) ? 'selected' : '' ?> ><?= Localization::get('game.rules.leave_home_attempt_enum_all_figures') ?></option>
                    </select>
                </div>

                <div class="form-row">
                    <span><?= Localization::get('game.rules.leave_home_attempts_max') ?></span>

                    <select name="<?= Application::LEAVE_HOME_ATTEMPTS_MAX ?>" data-ui="switch">
                        <option value="1" data-state="default" <?= ($game->getRuleSetModel()->getLeaveHomeAttemptsMax() === 1) ? 'selected' : '' ?> >1</option>
                        <option value="3" data-state="mid" <?= ($game->getRuleSetModel()->getLeaveHomeAttemptsMax() === 3) ? 'selected' : '' ?> >3</option>
                        <option value="5" data-state="active" <?= ($game->getRuleSetModel()->getLeaveHomeAttemptsMax() === 5) ? 'selected' : '' ?> >5</option>
                    </select>
                </div>

                <div class="form-row">
                    <span><?= Localization::get('game.rules.force_leaving_home_on_six') ?></span>

                    <select name="<?= Application::FORCE_LEAVING_HOME_ON_SIX ?>" data-ui="switch">
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

                    <select name="<?= Application::EXTRA_ROLL_ON_SIX_LIMIT ?>" data-ui="switch">
                        <option value="0" data-state="default" <?= ($game->getRuleSetModel()->getExtraRollOnSixLimit() === 0) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                        <option value="3" data-state="mid" <?= ($game->getRuleSetModel()->getExtraRollOnSixLimit() > 0 && $game->getRuleSetModel()->getAllowStackOwnFigures() < 255) ? 'selected' : '' ?> ><?= Localization::get('game.create.three') ?></option>
                        <option value="255" data-state="active" <?= ($game->getRuleSetModel()->getExtraRollOnSixLimit() === 255) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                    </select>
                </div>

                <div class="form-row">
                    <span><?= Localization::get('game.rules.force_extra_lap_on_overflow') ?></span>

                    <select name="<?= Application::FORCE_EXTRA_LAP_ON_OVERFLOW ?>" data-ui="switch">
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

                    <select name="<?= Application::ALLOW_STACK_OWN_FIGURES ?>" data-ui="switch">
                        <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getAllowStackOwnFigures()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                        <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getAllowStackOwnFigures()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                    </select>
                </div>

                <div class="form-row">
                    <span><?= Localization::get('game.rules.force_capture_enemy_figures') ?></span>

                    <select name="<?= Application::FORCE_CAPTURE_ENEMY_FIGURES ?>" data-ui="switch">
                        <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getForceCaptureEnemyFigures()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                        <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getForceCaptureEnemyFigures()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                    </select>
                </div>

                <div class="form-row">
                    <span><?= Localization::get('game.rules.strict_goal_order') ?></span>

                    <select name="<?= Application::STRICT_GOAL_ORDER ?>" data-ui="switch">
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

                <select name="<?= Application::IS_PRIVATE ?>" data-ui="switch">
                    <option value="0" data-state="active" <?= (!$game->isPrivate()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="inactive" <?= ($game->isPrivate()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>

            <div class="form-row">
                <span><?= Localization::get('game.options.is_locked') ?></span>

                <select name="<?= Application::IS_LOCKED ?>" data-ui="switch">
                    <option value="0" data-state="active" <?= (!$game->isLocked()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="inactive" <?= ($game->isLocked()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>

            <div class="form-row">
                <span><?= Localization::get('game.rules.allow_bots') ?></span>

                <select name="<?= Application::ALLOW_BOTS ?>" data-ui="switch">
                    <option value="0" data-state="default" <?= (!$game->getRuleSetModel()->getAllowBots()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="active" <?= ($game->getRuleSetModel()->getAllowBots()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>

        </div>

        <div class="nav-actions">
            <button
                type="submit"
                class="btn btn-save">
                <?= Localization::get('game.edit.button_update') ?>
            </button>
        </div>

    </form>

</div>

<script type="application/json" id="ruleset-presets">
    <?= json_encode($rule_set_presets, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>
<script type="application/json" id="ruleset-original">
    <?= json_encode($rule_set_original, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>
