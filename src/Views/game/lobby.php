<?php 
use App\Core\Localization;
?>

<h1><?= Localization::get('game.lobby.title') ?></h1>

<p>Welcome, <?= htmlspecialchars(\App\Core\Auth::user()->getUsername()) ?>!</p>

<ul>
    <li><a href="/game/create"><?= Localization::get('game.lobby.create_new_game') ?></a></li>
    <li><a href="/game/list"><?= Localization::get('game.lobby.open_games') ?></a></li>
    <li><a href="/menu"><?= Localization::get('game.lobby.back_to_menu') ?></a></li>
</ul>
