<?php
// src/Views/account/password_reset_sent.php

use App\Core\Localization;
?>

<div class="panel">
    <div class="card-header">
        <h1><?= htmlspecialchars(Localization::get('account.password_reset_sent.title')) ?></h1>
    </div>

    <div class="card-body">
        <p>
            <?= htmlspecialchars(Localization::get('account.password_reset_sent.message')) ?>
        </p>

        <p>
            <?= htmlspecialchars(Localization::get('account.password_reset_sent.expiration')) ?>
        </p>

        <a href="/login">
            <?= htmlspecialchars(Localization::get('account.password_reset_sent.btn.back_to_login')) ?>
        </a>
    </div>
</div>
