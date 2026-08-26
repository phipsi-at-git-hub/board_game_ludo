<?php 
// src/Views/account/set_password.php

use App\Core\Csrf; 
use App\Core\Localization;

/**
 * @var string $label
 */
?>

<div class="auth-panel">
    <div class="card auth-card">
        <h1><?= Localization::get('account.' . $label . '_password.title') ?></h1>

        <form method="POST" action="" class="form">
            <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">

            <div class="form-group">
                <label><?= Localization::get('account.' . $label . '_password.new_password') ?></label>
                <input type="password" name="new_password" placeholder="<?= Localization::get('account.' . $label . '_password.placeholder.password') ?>" required>
            </div>

            <div class="form-group">
                <label><?= Localization::get('account.' . $label . '_password.confirm_password') ?></label>
                <input type="password" name="confirm_password" placeholder="<?= Localization::get('account.' . $label . '_password.placeholder.password') ?>" required>
            </div>

            <div class="nav-actions">
                <button type="submit" class="btn btn-primary full-width">
                    <?= Localization::get('account.' . $label . '_password.btn.set') ?>
                </button>
            </div>
        </form>

        <div class="auth-links">
            <a href="/login"><?= Localization::get('application.general.btn.back_to_login') ?></a>
        </div>
    </div>
</div>

 
