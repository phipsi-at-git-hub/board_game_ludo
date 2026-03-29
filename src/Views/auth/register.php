<?php 
use App\Core\Csrf;
use App\Core\Localization;
?>

<div class="auth-panel">
    <div class="card auth-card">
        <h1><?= Localization::get('auth.register.title') ?></h1>

        <form method="POST" action="/register" class="form">
            <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">

            <div class="form-group">
                <label><?= Localization::get('auth.register.username') ?></label>
                <input type="text" name="username" placeholder="<?= Localization::get('auth.register.placeholder.username') ?>" required>
            </div>

            <div class="form-group">
                <label><?= Localization::get('auth.register.email') ?></label>
                <input type="email" name="email" placeholder="<?= Localization::get('auth.register.placeholder.email') ?>" required>
            </div>

            <div class="form-group">
                <label><?= Localization::get('auth.register.password') ?></label>
                <input type="password" name="password" placeholder="<?= Localization::get('auth.register.placeholder.password') ?>" required>
            </div>

            <div class="form-group">
                <label><?= Localization::get('auth.register.confirm_password') ?></label>
                <input type="password" name="confirm_password" placeholder="<?= Localization::get('auth.register.placeholder.password') ?>" required>
            </div>

            <div class="nav-actions">
                <button type="submit" class="btn btn-primary full-width"><?= Localization::get('auth.register.btn.register') ?></button>
            </div>
        </form>

        <div class="auth-links">
            <p><?= Localization::get('auth.register.already_registered') ?> <a href="/login"><?= Localization::get('auth.register.btn.login') ?></a></p>
        </div>
    </div>
</div>