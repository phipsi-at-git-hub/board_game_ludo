<?php 
use App\Core\Csrf;
use App\Core\Localization;
?>

<div class="auth-panel">
    <div class="card auth-card">
        <h1><?= Localization::get('account.forgot_password.title') ?></h1>

        <p class="auth-hint"><?= Localization::get('account.forgot_password.label') ?></p>

        <form method="POST" action="/forgot-password" class="form">
            <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">

            <div class="form-group">
                <label><?= Localization::get('account.forgot_password.email') ?></label>
                <input type="email" name="email" placeholder="<?= Localization::get('account.forgot_password.placeholder.email') ?>" required>
            </div>

            <div class="nav-actions">
                <button type="submit" class="btn btn-primary full-width">
                    <?= Localization::get('account.forgot_password.btn.send') ?>
                </button>
            </div>
        </form>

        <div class="auth-links">
            <a href="/login"><?= Localization::get('account.forgot_password.btn.back_to_login') ?></a>
        </div>
    </div>
</div>

 
