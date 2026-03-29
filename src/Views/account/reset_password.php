<?php 
use App\Core\Csrf; 
use App\Core\Localization;
?>

<div class="auth-panel">
    <div class="card auth-card">
        <h1><?= Localization::get('account.reset_password.title') ?></h1>

        <form method="POST" action="" class="form">
            <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">

            <div class="form-group">
                <label><?= Localization::get('account.reset_password.new_password') ?></label>
                <input type="password" name="new_password" placeholder="<?= Localization::get('account.reset_password.placeholder.password') ?>" required>
            </div>

            <div class="form-group">
                <label><?= Localization::get('account.reset_password.confirm_password') ?></label>
                <input type="password" name="confirm_password" placeholder="<?= Localization::get('account.reset_password.placeholder.password') ?>" required>
            </div>

            <div class="nav-actions">
                <button type="submit" class="btn btn-primary full-width">
                    <?= Localization::get('account.reset_password.btn.reset') ?>
                </button>
            </div>
        </form>

        <div class="auth-links">
            <a href="/login"><?= Localization::get('account.reset_password.btn.back_to_login') ?></a>
        </div>
    </div>
</div>

 
