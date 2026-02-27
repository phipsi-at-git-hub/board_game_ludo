<?php 
use App\Core\Csrf; 
use App\Constants\Application;
use App\Core\Localization;

?>

<h1><?= Localization::get('game.edit.title') ?></h1>

<div class="back-link">
    <ul class="nav-list">
        <li><a href="/game/list" class="btn-back"><?= Localization::get('game.show.back_to_list') ?></a></li>
        <li><a href="/lobby" class="btn-back"><?= Localization::get('game.list.back_to_lobby') ?></a></li>
        <li><a href="/menu" class="btn-back"><?= Localization::get('game.lobby.back_to_menu') ?></a></li>
    </ul>
</div>

<form method="POST" action="/game/update">
    <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
    <input type="hidden" name="game_id" value="<?= $game->getId() ?>">

    <fieldset>
        <legend><?= Localization::get('game.create.name') ?></legend>
        <label>
            <?= Localization::get('game.create.game_name') ?>
            <input name="<?= Application::GAME_NAME ?>" value="<?= $game->getName() ?>">
        </label>
    </fieldset>

    <fieldset>
        <legend><?= Localization::get('game.create.game_options') ?></legend>
        <label>
            <?= Localization::get('game.options.is_private') ?>
            <select name="<?= Application::IS_PRIVATE; ?>">
                <option value="0" <?= (!$game->isPrivate()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                <option value="1" <?= ($game->isPrivate()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
            </select>
        </label>
        <br><br>
        <label>
            <?= Localization::get('game.options.is_locked') ?>
            <select name="<?= Application::IS_LOCKED; ?>">
                <option value="0" <?= (!$game->isLocked()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                <option value="1" <?= ($game->isLocked()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
            </select>
        </label>
    </fieldset>

    <fieldset>
        <legend><?= Localization::get('game.create.game_rules') ?></legend>
        <label>
            <?= Localization::get('game.rules.allow_bots') ?>
            <select name="<?= Application::ALLOW_BOTS; ?>">
                <option value="0" <?= (!$game->getRuleSetModel()->getAllowBots()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                <option value="1" <?= ($game->getRuleSetModel()->getAllowBots()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
            </select>
        </label>
        <br><br>
        <label>
            <?= Localization::get('game.rules.roll_on_limit') ?>
            <select name="<?= Application::EXTRA_ROLL_LIMIT; ?>">
                <option value="0" <?= ($game->getRuleSetModel()->getExtraRollLimit() === 0) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                <option value="3" <?= ($game->getRuleSetModel()->getExtraRollLimit() > 0 && $game->getRuleSetModel()->getAllowStackOwnFigures() < 255) ? 'selected' : '' ?> ><?= $game->getRuleSetModel()->getExtraRollLimit() ?></option>
                <option value="255" <?= ($game->getRuleSetModel()->getExtraRollLimit() === 255) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
            </select>
        </label>
        <br><br>
        <label>
            <?= Localization::get('game.rules.force_extra_lap_on_overflow') ?>
            <select name="<?php echo Application::FORCE_EXTRA_LAP_ON_OVERFLOW; ?>">
                <option value="0" <?= (!$game->getRuleSetModel()->getForceExtraLapOnOverflow()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                <option value="1" <?= ($game->getRuleSetModel()->getForceExtraLapOnOverflow()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
            </select>
        </label>
        <br><br>
        <label>
            <?= Localization::get('game.rules.allow_stack') ?>
            <select name="<?= Application::ALLOW_STACK_OWN_FIGURES; ?>">
                <option value="0" <?= (!$game->getRuleSetModel()->getAllowStackOwnFigures()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                <option value="1" <?= ($game->getRuleSetModel()->getAllowStackOwnFigures()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
            </select>
        </label>
        <br><br>
        <label>
            <?= Localization::get('game.rules.strict_goal_order') ?>
            <select name="<?= Application::STRICT_GOAL_ORDER; ?>">
                <option value="0" <?= (!$game->getRuleSetModel()->getStrictGoalOrder()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                <option value="1" <?= ($game->getRuleSetModel()->getStrictGoalOrder()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
            </select>
        </label>
        <br><br>
        <label>
            <?= Localization::get('game.rules.start_field_must_be_cleared') ?>
            <select name="<?= Application::START_FIELD_MUST_BE_CLEARED; ?>">
                <option value="0" <?= (!$game->getRuleSetModel()->getStartFieldMustBeCleared()) ? 'selected' : '' ?> ><?= Localization::get('application.general.no') ?></option>
                <option value="1" <?= ($game->getRuleSetModel()->getStartFieldMustBeCleared()) ? 'selected' : '' ?> ><?= Localization::get('application.general.yes') ?></option>
            </select>
        </label>
    </fieldset>

    <br>
    <button type="submit"><?= Localization::get('game.edit.button_update') ?></button>
</form>
