<?php 
use App\Core\Csrf;
use App\Core\Localization;
 ?>

<div class="auth-panel">
    <div class="card auth-card">
        <h1><?= Localization::get('auth.login.title') ?></h1>

        <?php if (!empty($error)): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/login" class="form">
            <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">

            <div class="form-group">
                <label><?= Localization::get('auth.login.email') ?></label>
                <input type="email" name="email" placeholder="<?= Localization::get('auth.login.placeholder.email') ?>" required>
            </div>

            <div class="form-group">
                <label><?= Localization::get('auth.login.password') ?></label>
                <input type="password" name="password" placeholder="<?= Localization::get('auth.login.placeholder.password') ?>" required>
            </div>

            <div class="nav-actions">
                <button type="submit" class="btn btn-primary full-width"><?= Localization::get('auth.login.btn.login') ?></button>
            </div>
        </form>

        <div class="auth-links">
            <p><?= Localization::get('auth.login.no_account') ?> <a href="/register"><?= Localization::get('auth.login.btn.register') ?></a></p>
            <p><a href="/forgot-password"><?= Localization::get('auth.login.btn.forgot_password') ?></a></p>
        </div>
    </div>
</div>