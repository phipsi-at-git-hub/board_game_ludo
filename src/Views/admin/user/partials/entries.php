<?php

use App\Core\Localization;
use App\Models\User\UserModel;

/**
 * @var array $users
 * @var UserModel $user
 */

?>

<div class="entry-list-cards">

    <?php if (!empty($users)): ?>

        <?php foreach ($users as $user): ?>

            <div class="card entry-row"
                onclick="window.location='/admin/user/detail/<?= $user->getId() ?>'">

                <?php include VIEWS_PATH . '/admin/user/partials/entry.php'; ?>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="nested-card">
            <?= Localization::get('admin.users.list.header.no_users') ?>
        </div>

    <?php endif; ?>

</div>