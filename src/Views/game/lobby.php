<?php 
use App\Core\Localization;
use App\Core\SystemSettings;
use App\Services\SystemService;

/** 
 * @var Object $current_user 
 * @var SystemService $system_settings
*/
?>

<div class="panel">
    <h1 class="page-title"><?= Localization::get('game.lobby.title') ?></h1>

    <p class="welcome-text">
        <?= Localization::get('game.lobby.welcome') ?>, <strong><?= htmlspecialchars(\App\Core\Auth::user()->getUsername()) ?></strong>!
    </p>

    <div class="nav-actions">
        <ul class="nav-list">
            <?php if ($system_settings->isGameCreationEnabled() || $current_user->isAdmin()): ?>
            <li><a href="/game/create" class="btn-back"><?= Localization::get('application.general.btn.create_new_game') ?></a></li>
            <?php endif; ?>
            <li><a href="/game/list" class="btn-back"><?= Localization::get('game.lobby.open_games') ?></a></li>
            <li><a href="/game/my_games" class="btn-back"><?= Localization::get('game.lobby.my_games') ?></a></li>
        </ul>
    </div>
</div>