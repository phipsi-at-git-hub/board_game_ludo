<?php 
use App\Core\Localization;
use App\Core\Asset;
?>

<nav class="navbar">
    <div class="logo"><a href="/lobby" class="btn-home"><?= Localization::get('application.general.title') ?></a></div>
    <div class="nav-items">
        <?php if ($current_user->isAdmin()): ?>
            <a href="/admin"  class="btn btn-primary"><?= Localization::get('application.navbar.btn_admin') ?></a>
        <?php endif; ?>
        <a href="/account" class="btn btn-primary"><?= Localization::get('application.navbar.btn_account') ?></a>
        <form method="POST" action="/logout">
            <input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::generate() ?>">
            <button class="btn btn-danger" type="submit"><?= Localization::get('application.navbar.btn_logout') ?></button>
        </form>
    </div>
</nav>