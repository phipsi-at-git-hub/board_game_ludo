<?php

use App\Core\Csrf;
use App\Constants\Application;
use App\Core\Localization;

/**
 * @var \App\Models\GameRuleSetModel $rule_set
 * @var array $rule_set_presets 
 */
?>

<div class="panel">

    <h1><?= Localization::get('game.create.title') ?></h1>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li>
                <a href="/lobby" class="btn-back">
                    <?= Localization::get('application.general.btn.back_to_lobby') ?>
                </a>
            </li>
        </ul>
    </div>

    <form method="POST" action="/game/store" form-game-rules>

        <input
            type="hidden"
            name="_csrf_token"
            value="<?= Csrf::generate() ?>">

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

            <h2><?= Localization::get('game.create.card.rules.title') ?></h2>

            <!-- Starting Rules -->
            <div class="nested-card">

                <h3><?= Localization::get('game.create.card.rules.group.starting.title') ?></h3>

                <div class="form-row">
                    <span><?= Localization::get('game.rules.all_figures_start_at_home') ?></span>

                    <select name="<?= Application::ALL_FIGURES_START_AT_HOME ?>" data-ui="switch">
                        <option value="0" data-state="default" <?= !$rule_set->getAllFiguresStartAtHome() ? 'selected' : '' ?>>
                            <?= Localization::get('application.general.no') ?>
                        </option>
                        <option value="1" data-state="active" <?= $rule_set->getAllFiguresStartAtHome() ? 'selected' : '' ?>>
                            <?= Localization::get('application.general.yes') ?>
                        </option>
                    </select>
                </div>

                <div class="form-row">
                    <span><?= Localization::get('game.rules.start_field_must_be_cleared') ?></span>

                    <select name="<?= Application::START_FIELD_MUST_BE_CLEARED ?>" data-ui="switch">
                        <option value="0" data-state="default" <?= !$rule_set->getStartFieldMustBeCleared() ? 'selected' : '' ?>>
                            <?= Localization::get('application.general.no') ?>
                        </option>
                        <option value="1" data-state="active" <?= $rule_set->getStartFieldMustBeCleared() ? 'selected' : '' ?>>
                            <?= Localization::get('application.general.yes') ?>
                        </option>
                    </select>
                </div>

                <div class="form-row">
                    <span><?= Localization::get('game.rules.leave_home_attempt') ?></span>

                    <select name="<?= Application::LEAVE_HOME_ATTEMPT ?>" data-ui="switch">
                        <option value="<?= Application::ENUM_FIRST_FIGURE ?>"
                                data-state="default"
                                <?= $rule_set->getLeaveHomeAttemptVariant() === Application::ENUM_FIRST_FIGURE ? 'selected' : '' ?>>
                            <?= Localization::get('game.rules.leave_home_attempt_enum_first_figure') ?>
                        </option>

                        <option value="<?= Application::ENUM_ALL_FIGURES ?>"
                                data-state="active"
                                <?= $rule_set->getLeaveHomeAttemptVariant() === Application::ENUM_ALL_FIGURES ? 'selected' : '' ?>>
                            <?= Localization::get('game.rules.leave_home_attempt_enum_all_figures') ?>
                        </option>
                    </select>
                </div>

                <div class="form-row">
                    <span><?= Localization::get('game.rules.leave_home_attempts_max') ?></span>

                    <select name="<?= Application::LEAVE_HOME_ATTEMPTS_MAX ?>" data-ui="switch">
                        <option value="1" data-state="default" <?= $rule_set->getLeaveHomeAttemptsMax() === 1 ? 'selected' : '' ?>>1</option>
                        <option value="3" data-state="mid" <?= $rule_set->getLeaveHomeAttemptsMax() === 3 ? 'selected' : '' ?>>3</option>
                        <option value="5" data-state="active" <?= $rule_set->getLeaveHomeAttemptsMax() === 5 ? 'selected' : '' ?>>5</option>
                    </select>
                </div>

                <div class="form-row">
                    <span><?= Localization::get('game.rules.force_leaving_home_on_six') ?></span>

                    <select name="<?= Application::FORCE_LEAVING_HOME_ON_SIX ?>" data-ui="switch">
                        <option value="0" data-state="default" <?= !$rule_set->getForceLeavingHomeOnSix() ? 'selected' : '' ?>>
                            <?= Localization::get('application.general.no') ?>
                        </option>
                        <option value="1" data-state="active" <?= $rule_set->getForceLeavingHomeOnSix() ? 'selected' : '' ?>>
                            <?= Localization::get('application.general.yes') ?>
                        </option>
                    </select>
                </div>

            </div>

            <!-- Movement & Turn Rules -->
            <div class="nested-card">

                <h3><?= Localization::get('game.create.card.rules.group.movement.title') ?></h3>

                <div class="form-row">
                    <span><?= Localization::get('game.rules.roll_on_six_limit') ?></span>

                    <select name="<?= Application::EXTRA_ROLL_ON_SIX_LIMIT ?>" data-ui="switch">
                        <option value="0" data-state="default" <?= $rule_set->getExtraRollOnSixLimit() === 0 ? 'selected' : '' ?>>
                            <?= Localization::get('application.general.no') ?>
                        </option>
                        <option value="3" data-state="mid" <?= $rule_set->getExtraRollOnSixLimit() > 0 && $rule_set->getExtraRollOnSixLimit() < 255 ? 'selected' : '' ?>>
                            <?= Localization::get('game.create.three') ?>
                        </option>
                        <option value="255" data-state="active" <?= $rule_set->getExtraRollOnSixLimit() === 255 ? 'selected' : '' ?>>
                            <?= Localization::get('application.general.yes') ?>
                        </option>
                    </select>
                </div>

                <div class="form-row">
                    <span><?= Localization::get('game.rules.force_extra_lap_on_overflow') ?></span>

                    <select name="<?= Application::FORCE_EXTRA_LAP_ON_OVERFLOW ?>" data-ui="switch">
                        <option value="0" data-state="default" <?= !$rule_set->getForceExtraLapOnOverflow() ? 'selected' : '' ?>>
                            <?= Localization::get('application.general.no') ?>
                        </option>
                        <option value="1" data-state="active" <?= $rule_set->getForceExtraLapOnOverflow() ? 'selected' : '' ?>>
                            <?= Localization::get('application.general.yes') ?>
                        </option>
                    </select>
                </div>

            </div>

            <!-- Interaction & Goal Rules -->
            <div class="nested-card">

                <h3><?= Localization::get('game.create.card.rules.group.interactions.title') ?></h3>

                <div class="form-row">
                    <span><?= Localization::get('game.rules.allow_stack_own_figures') ?></span>

                    <select name="<?= Application::ALLOW_STACK_OWN_FIGURES ?>" data-ui="switch">
                        <option value="0" data-state="default" <?= !$rule_set->getAllowStackOwnFigures() ? 'selected' : '' ?>>
                            <?= Localization::get('application.general.no') ?>
                        </option>
                        <option value="1" data-state="active" <?= $rule_set->getAllowStackOwnFigures() ? 'selected' : '' ?>>
                            <?= Localization::get('application.general.yes') ?>
                        </option>
                    </select>
                </div>

                <div class="form-row">
                    <span><?= Localization::get('game.rules.force_capture_enemy_figures') ?></span>

                    <select name="<?= Application::FORCE_CAPTURE_ENEMY_FIGURES ?>" data-ui="switch">
                        <option value="0" data-state="default" <?= !$rule_set->getForceCaptureEnemyFigures() ? 'selected' : '' ?>>
                            <?= Localization::get('application.general.no') ?>
                        </option>
                        <option value="1" data-state="active" <?= $rule_set->getForceCaptureEnemyFigures() ? 'selected' : '' ?>>
                            <?= Localization::get('application.general.yes') ?>
                        </option>
                    </select>
                </div>

                <div class="form-row">
                    <span><?= Localization::get('game.rules.strict_goal_order') ?></span>

                    <select name="<?= Application::STRICT_GOAL_ORDER ?>" data-ui="switch">
                        <option value="0" data-state="default" <?= !$rule_set->getStrictGoalOrder() ? 'selected' : '' ?>>
                            <?= Localization::get('application.general.no') ?>
                        </option>
                        <option value="1" data-state="active" <?= $rule_set->getStrictGoalOrder() ? 'selected' : '' ?>>
                            <?= Localization::get('application.general.yes') ?>
                        </option>
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
                    <option value="0" data-state="active" selected><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="inactive"><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>

            <!--
            <div class="form-row">
                <span><?= Localization::get('game.options.is_locked') ?></span>

                <select name="<?= Application::IS_LOCKED ?>" data-ui="switch">
                    <option value="0" data-state="active" selected><?= Localization::get('application.general.no') ?></option>
                    <option value="1" data-state="inactive"><?= Localization::get('application.general.yes') ?></option>
                </select>
            </div>
            -->

            <div class="form-row">
                <span><?= Localization::get('game.rules.allow_bots') ?></span>

                <select name="<?= Application::ALLOW_BOTS ?>" data-ui="switch">
                    <option value="0" data-state="default" <?= !$rule_set->getAllowBots() ? 'selected' : '' ?>>
                        <?= Localization::get('application.general.no') ?>
                    </option>
                    <option value="1" data-state="active" <?= $rule_set->getAllowBots() ? 'selected' : '' ?>>
                        <?= Localization::get('application.general.yes') ?>
                    </option>
                </select>
            </div>

        </div>

        <div class="nav-actions">
            <button
                type="submit"
                class="btn btn-save">
                <?= Localization::get('game.create.button_create') ?>
            </button>
        </div>

    </form>

</div>

<script type="application/json" id="ruleset-presets">
    <?= json_encode($rule_set_presets, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>
