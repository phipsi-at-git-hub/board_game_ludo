<?php 
use App\Core\Localization;
?>

<h1><?= Localization::get('game.lobby.title') ?></h1>

<p>Welcome, <?= htmlspecialchars(\App\Core\Auth::user()->getUsername()) ?>!</p>

<div class="back-link">
    <ul class="nav-list">
        <li><a href="/game/create" class="btn-back"><?= Localization::get('game.list.create_new_game') ?></a></li>
        <li><a href="/game/list" class="btn-back"><?= Localization::get('game.lobby.open_games') ?></a></li>
        <li><a href="/menu" class="btn-back"><?= Localization::get('game.lobby.back_to_menu') ?></a></li>
    </ul>
</div>
