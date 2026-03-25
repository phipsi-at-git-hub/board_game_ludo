<?php
use App\Core\Asset;
use App\Core\Csrf;
use App\Core\Localization;
?>

<!-- 🎮 THREE.js Canvas -->
<canvas id="game_canvas"></canvas>

<!-- 🧩 UI -->
<div id="ui">

    <!-- HUD -->
    <div id="hud">
        <div>
            🎮 <?= $game->getName() ?>
        </div>
        <div>
            <?= Localization::get('game.play.current_player') ?>:
            <?= $game->getCurrentPlayer()->getUsername() ?>
        </div>
    </div>

    <!-- Dice -->
    <div id="dice-container">
    <div id="dice"></div>
    <div id="dice-value"></div>
    </div>

    <!-- Available Moves -->
     <div id="moves-container"></div>

    <!-- Controls -->
    <div id="controls">
        <button id="btn-roll">
            🎲 <?= Localization::get('game.play.roll_dice') ?>
        </button>

        <!--
        <button id="btn-end">
            ➡️ <?= Localization::get('game.play.pass_turn') ?>
        </button>
        -->

        <button id="btn-menu">☰</button>
    </div>

    <!-- Menu -->
    <div id="menu">
        <h2><?= Localization::get('game.play.menu') ?></h2>

        <button id="btn-resume">
            <?= Localization::get('game.play.resume') ?>
        </button>

        <br><br>

        <button id="btn-exit">
            <?= Localization::get('game.play.exit') ?>
        </button>
    </div>

</div>

<!-- 🔧 Game Config -->
<script>
    window.GAME_CONFIG = {
        game_id: "<?= $game->getId() ?>",
        user_id: "<?= $user->getId() ?>", 
        _csrf_token: "<?= Csrf::generate() ?>"
    };
</script>
<link rel="stylesheet" href="<?= Asset::asset('/css/game.css') ?>">

<!-- 🎮 Game entry point -->
<script type="module" src="<?= Asset::asset('/js/game.js') ?>"></script>