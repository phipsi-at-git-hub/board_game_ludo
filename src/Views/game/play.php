<?php
use App\Core\Asset;
use App\Core\Csrf;
use App\Core\Localization;

/**
 * @var \App\Models\GameModel $game
 * @var \App\Models\UserModel $user 
 */
?>

<!-- 🎮 THREE.js Canvas -->
<div id="game-container">
    <canvas id="game_canvas"></canvas>

    <!-- 🧩 UI -->
    <div id="ui">

        <!-- HUD -->
        <div id="hud">
            <div>
                🎮 <?= $game->getName() ?>
            </div>
            <div>
                <span><?= Localization::get('game.play.current_player') ?>:</span>
                <span id="current-username"><?= $game->getCurrentPlayer()->getUsername() ?></span>
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
            <button id="btn-roll" class="btn btn-primary">
                🎲 <?= Localization::get('game.play.roll_dice') ?>
            </button>

            <button id="btn-menu" class="btn btn-primary"><?= Localization::get('application.general.icon.menu') ?></button>
        </div>

        <!-- Menu -->
        <div id="menu-overlay">
            <div id="menu">
                <h2><?= Localization::get('game.play.menu_title') ?></h2>

                <p>
                    <label>
                        <input type="checkbox" id="settings-camera-toggle"><?= Localization::get('game.play.menu_settings_camera_toggle') ?>
                    </label>
                </p>

                <button id="btn-resume" class="btn btn-primary">
                    <?= Localization::get('game.play.resume') ?>
                </button>

                <br><br>

                <button id="btn-exit" class="btn btn-primary">
                    <?= Localization::get('application.general.btn.back_to_detail') ?>
                </button>
            </div>
        </div>

    </div>
</div>

<!-- 🔧 Game Config -->
<script>
    window.GAME_CONFIG = {
        game_id: "<?= $game->getId() ?>",
        user_id: "<?= $user->getId() ?>", 
        user_player_index: "<?= $game->getPlayerIndexByPlayerId($user->getId()) ?>", 
        _csrf_token: "<?= Csrf::generate() ?>"
    };
</script>

<!-- 🔧 THREE.js Import Map -->
 <script type="importmap">
    {
        "imports": {
            "three": "https://unpkg.com/three@0.183.2/build/three.module.js",
            "three/RoundedBoxGeometry": "https://unpkg.com/three@0.183.2/examples/jsm/geometries/RoundedBoxGeometry.js"
        }
    }
 </script>

<!-- 🎮 Game entry point -->
<script type="module" src="<?= Asset::asset('/js/viewer/game.js') ?>"></script>