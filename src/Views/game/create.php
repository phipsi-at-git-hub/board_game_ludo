<?php 
use App\Core\Csrf; 
use App\Constants\Application;
use App\Core\Localization;

?>

<h1><?= Localization::get('game.create.title') ?></h1>

<ul>
    <li><a href="/lobby"><?= Localization::get('game.list.back_to_lobby') ?></a></li>
</ul>

<form method="POST" action="/game/store">
    <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
    <fieldset>
        <legend>Name</legend>
        <label>
            Allow Bots
            <input name="<?php echo Application::GAME_NAME; ?>">
        </label>
    </fieldset>
    <fieldset>
        <legend>Game Options</legend>
        <label>
            Allow Bots
            <select name="<?php echo Application::ALLOW_BOTS; ?>">
                <option value="0">No</option>
                <option value="1" selected>Yes</option>
            </select>
        </label>
        <br><br>
        <label>
            Extra Roll on Six Limit
            <select name="<?php echo Application::EXTRA_ROLL_LIMIT; ?>">
                <option value="0">No</option>
                <option value="3" selected>3</option>
                <option value="255" selected>Yes</option>
            </select>
        </label>
        <br><br>
        <label>
            Allow stack on Figures
            <select name="<?php echo Application::ALLOW_STACK_OWN_FIGURES; ?>">
                <option value="0" selected>No</option>
                <option value="1">Yes</option>
            </select>
        </label>
        <br><br>
        <label>
            Strict Goal Order
            <select name="<?php echo Application::STRICT_GOAL_ORDER; ?>">
                <option value="0">No</option>
                <option value="1" selected>Yes</option>
            </select>
        </label>
        <br><br>
        <label>
            Start field must be cleared
            <select name="<?php echo Application::START_FIELD_MUST_BE_CLEARED; ?>">
                <option value="0">No</option>
                <option value="1" selected>Yes</option>
            </select>
        </label>
    </fieldset>
    <br>
    <button type="submit">Create Game</button>
</form>
