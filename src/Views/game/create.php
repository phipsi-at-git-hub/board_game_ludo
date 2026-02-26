<?php 
use App\Core\Csrf; 
use App\Constants\Application;
use App\Core\Localization;

?>

<h1><?= Localization::get('game.create.title') ?></h1>

<div class="back-link">
    <ul class="nav-list">
        <li><a href="/lobby" class="btn-back"><?= Localization::get('game.list.back_to_lobby') ?></a></li>
        <li><a href="/menu" class="btn-back"><?= Localization::get('game.lobby.back_to_menu') ?></a></li>
    </ul>
</div>

<form method="POST" action="/game/store">
    <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">

    <fieldset>
        <legend><?= Localization::get('game.create.name') ?></legend>
        <label>
            <?= Localization::get('game.create.game_name') ?>
            <input name="<?php echo Application::GAME_NAME; ?>">
        </label>
    </fieldset>

    <fieldset>
        <legend><?= Localization::get('game.create.game_options') ?></legend>
        <label>
            <?= Localization::get('game.options.is_private') ?>
            <select name="<?php echo Application::IS_PRIVATE; ?>">
                <option value="0" selected><?= Localization::get('application.general.no') ?></option>
                <option value="1"><?= Localization::get('application.general.yes') ?></option>
            </select>
        </label>
        <br><br>
        <label>
            <?= Localization::get('game.options.is_locked') ?>
            <select name="<?php echo Application::IS_LOCKED; ?>">
                <option value="0" selected><?= Localization::get('application.general.no') ?></option>
                <option value="1"><?= Localization::get('application.general.yes') ?></option>
            </select>
        </label>
    </fieldset>

    <fieldset>
        <legend><?= Localization::get('game.create.game_rules') ?></legend>
        <label>
            <?= Localization::get('game.rules.allow_bots') ?>
            <select name="<?php echo Application::ALLOW_BOTS; ?>">
                <option value="0"><?= Localization::get('application.general.no') ?></option>
                <option value="1" selected><?= Localization::get('application.general.yes') ?></option>
            </select>
        </label>
        <br><br>
        <label>
            <?= Localization::get('game.rules.roll_on_limit') ?>
            <select name="<?php echo Application::EXTRA_ROLL_LIMIT; ?>">
                <option value="0"><?= Localization::get('application.general.no') ?></option>
                <option value="3" selected><?= Localization::get('game.create.three') ?></option>
                <option value="255" selected><?= Localization::get('application.general.yes') ?></option>
            </select>
        </label>
        <br><br>
        <label>
            <?= Localization::get('game.rules.allow_stack') ?>
            <select name="<?php echo Application::ALLOW_STACK_OWN_FIGURES; ?>">
                <option value="0" selected><?= Localization::get('application.general.no') ?></option>
                <option value="1"><?= Localization::get('application.general.yes') ?></option>
            </select>
        </label>
        <br><br>
        <label>
            <?= Localization::get('game.rules.strict_goal_order') ?>
            <select name="<?php echo Application::STRICT_GOAL_ORDER; ?>">
                <option value="0"><?= Localization::get('application.general.no') ?></option>
                <option value="1" selected><?= Localization::get('application.general.yes') ?></option>
            </select>
        </label>
        <br><br>
        <label>
            <?= Localization::get('game.rules.start_field_must_be_cleared') ?>
            <select name="<?php echo Application::START_FIELD_MUST_BE_CLEARED; ?>">
                <option value="0"><?= Localization::get('application.general.no') ?></option>
                <option value="1" selected><?= Localization::get('application.general.yes') ?></option>
            </select>
        </label>
    </fieldset>

    <br>
    <button type="submit"><?= Localization::get('game.create.button_create') ?></button>
</form>
