<?php 
use App\Core\Csrf; 
use App\Domain\Game\Rules\GameRuleKey;
?>

<h1>Create new Game</h1>

<form method="POST" action="/game/store">
    <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
    <fieldset>
        <legend>Game Options</legend>
        <label>
            Allow Bots
            <select name="rules[<?php echo GameRuleKey::ALLOW_BOTS; ?>]">
                <option value="0">No</option>
                <option value="1" selected>Yes</option>
            </select>
        </label>
        <br><br>
        <label>
            Extra Roll on Six
            <select name="rules[<?php echo GameRuleKey::EXTRA_ROLL_ON_SIX; ?>]">
                <option value="0">No</option>
                <option value="1" selected>Yes</option>
            </select>
        </label>
        <br><br>
        <label>
            Allow stack on Figures
            <select name="rules[<?php echo GameRuleKey::ALLOW_STACK_OWN_FIGURES; ?>]">
                <option value="0" selected>No</option>
                <option value="1">Yes</option>
            </select>
        </label>
        <br><br>
        <label>
            Strict Goal Order
            <select name="rules[<?php echo GameRuleKey::STRICT_GOAL_ORDER; ?>]">
                <option value="0">No</option>
                <option value="1" selected>Yes</option>
            </select>
        </label>
        <br><br>
        <label>
            Start field must be cleared
            <select name="rules[<?php echo GameRuleKey::START_FIELD_MUST_BE_CLEARED; ?>]">
                <option value="0">No</option>
                <option value="1" selected>Yes</option>
            </select>
        </label>
    </fieldset>
    <br>
    <button type="submit">Create Game</button>
</form>
