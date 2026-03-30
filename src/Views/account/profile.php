<?php 
use App\Core\Auth; 
use App\Core\Csrf;
use App\Core\Localization;

?>

<div class="panel">
    <h1><?= Localization::get('account.profile.title') ?></h1>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li><a href="/lobby" class="btn-back"><?= Localization::get('application.general.btn.back_to_lobby') ?></a></li>
        </ul>
    </div>

    <!-- User Data -->
    <div class="card">
        <h2><?= Localization::get('account.profile.information') ?></h2>

        <form action="/account/update" method="POST" class="form">
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">

            <div class="form-group">
                <label><?= Localization::get('account.profile.information.username') ?></label>
                <input type="text" name="username" value="<?= htmlspecialchars($current_user->getUsername()) ?>" required>
            </div>

            <div class="form-group">
                <label><?= Localization::get('account.profile.information.email') ?></label>
                <input type="email" name="email" value="<?= htmlspecialchars($current_user->getEmail()) ?>" required>
            </div>

            <div class="nav-actions left">
                <button type="submit" class="btn btn-primary"><?= Localization::get('account.profile.btn.update_profile') ?></button>
            </div>
        </form>
    </div>

    <!-- Change Password -->
    <div class="card">
        <h2><?= Localization::get('account.profile.change_password') ?></h2>

        <form method="POST" action="/account/password" class="form">
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">

            <div class="form-group">
                <label><?= Localization::get('account.profile.change_password.current_password') ?></label>
                <input type="password" name="current_password" required>
            </div>

            <div class="form-group">
                <label></label><?= Localization::get('account.profile.change_password.new_password') ?></label>
                <input type="password" name="new_password" required>
            </div>

            <div class="form-group">
                <label><?= Localization::get('account.profile.change_password.confirm_password') ?></label>
                <input type="password" name="confirm_password" required>
            </div>

            <div class="nav-actions left">
                <button type="submit" class="btn btn-primary"><?= Localization::get('account.profile.btn.change_password') ?></button>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="card danger-zone">
        <h2><?= Localization::get('account.profile.delete_account') ?></h2>

        <p><?= Localization::get('account.profile.delete_account.information') ?></p>

        <form action="/account" method="POST" onsubmit="return confirm('Are you sure? This cannot be undone.')">
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">

            <div class="nav-actions left">
                <button type="submit" class="btn btn-danger"><?= Localization::get('account.profile.btn.delete_account') ?></button>
            </div>
        </form>
    </div>

</div>

 
