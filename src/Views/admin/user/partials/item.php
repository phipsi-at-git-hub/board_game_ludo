<?php

use App\Models\User\UserModel;

/**
 * @var UserModel $user
 */
?>

<div class="card entry-row"
    onclick="window.location='/admin/user/detail/<?= $user->getId() ?>'">

    <div class="entry-row-header">

        <div class="entry-row-title">
            <?= htmlspecialchars($user->getUsername()) ?>
        </div>

        <div class="entry-row-header-badges">

            <span class="role-badge role-<?= strtolower($user->getRole()) ?>">
                <?= htmlspecialchars($user->getRole()) ?>
            </span>

            <span class="status-badge status-<?= strtolower($user->getStatus()) ?>">
                <?= htmlspecialchars($user->getStatus()) ?>
            </span>

        </div>

    </div>

    <div class="entry-row-footer">

        <div class="entry-row-badges">

            <span>
                <?= htmlspecialchars($user->getEmail()) ?>
            </span>

        </div>

        <?php include VIEWS_PATH . '/admin/user/partials/actions.php'; ?>

    </div>

</div>
