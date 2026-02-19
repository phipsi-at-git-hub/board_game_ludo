<?php
use App\Core\Localization;
?>

<h1><?= Localization::get('application.menu.title') ?></h1>

<ul>
    <li><a href="/lobby"><?= Localization::get('application.menu.lobby') ?></a></li>
    <li><a href="/account"><?= Localization::get('application.menu.account') ?></a></li>
    <?php if ($user->isAdmin()): ?>
        <li><a href="/admin"><?= Localization::get('application.menu.admin') ?></a></li>
    <?php endif; ?>
</ul>

<form method="POST" action="/logout">
    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::generate() ?>">
    <button type="submit">Logout</button>
</form>