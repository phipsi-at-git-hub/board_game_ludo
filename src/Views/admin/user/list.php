<?php

use App\Core\Localization;

/**
 * @var array $users
 */
?>

<div class="panel">

    <h1>
        <?= Localization::get('admin.users.list.title') ?>
    </h1>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li>
                <a href="/lobby" class="btn-back">
                    <?= Localization::get('application.general.btn.back_to_lobby') ?>
                </a>
            </li>

            <li>
                <a href="/admin" class="btn-back">
                    <?= Localization::get('application.general.btn.back_to_dashboard') ?>
                </a>
            </li>
        </ul>
    </div>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li>
                <a href="/admin/user/create" class="btn-back">
                    <?= Localization::get('application.general.btn.create_new_user') ?>
                </a>
            </li>
        </ul>
    </div>

    <div class="entry-list-cards">

        <?php if (!empty($users)): ?>

            <?php foreach ($users as $user): ?>

                <?php include VIEWS_PATH . '/admin/user/partials/item.php'; ?>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="nested-card">
                <?= Localization::get('admin.users.list.header.no_users') ?>
            </div>

        <?php endif; ?>

    </div>

</div>
