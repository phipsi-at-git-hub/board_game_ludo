<?php
use App\Core\Localization;
use App\Core\Asset;
?>

<h1><?= $game->getName() ?></h1>

<p>
    <?= Localization::get('game.play.current_player') ?>:
    <?= $game->getCurrentPlayer()->getUsername() ?>
</p>

<div id="game_wrapper">
    <canvas id="game_canvas"></canvas>
</div>

<script>
    window.GAME_CONFIG = {
        game_id: "<?= $game->getId() ?>", 
        user_id: "<?= $user->getId() ?>"
    };
</script>

<!--<script type="module" src="<?= Asset::asset('/js/loader.js') ?>"></script>-->
<script type="module" src="<?= Asset::asset('/js/game.js') ?>"></script>

<style>
    body {
        margin: 0;
        overflow: hidden;
    }

    #game_wrapper {
        /*
        width: 500px;
        height: 500px;
        */
        margin: 0 auto;
    }
    #game_canvas {
        display: block;
        width: 100%;
        height: 100%;
    }
</style>