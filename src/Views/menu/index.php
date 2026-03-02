<?php
use App\Core\Localization;
?>

<h1><?= Localization::get('application.menu.title') ?></h1>

<div class="back-link">
    <ul class="nav-list">
        <li><a href="/lobby"  class="btn-back"><?= Localization::get('application.menu.lobby') ?></a></li>
        <li><a href="/account"  class="btn-back"><?= Localization::get('application.menu.account') ?></a></li>
        <?php if ($user->isAdmin()): ?>
            <li><a href="/admin"  class="btn-back"><?= Localization::get('application.menu.admin') ?></a></li>
        <?php endif; ?>
    </ul>
</div>

<form method="POST" action="/logout">
    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::generate() ?>">
    <button type="submit">Logout</button>
</form>