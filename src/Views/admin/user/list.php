<?php
use App\Core\Csrf;
use App\Core\Localization;
?>

<div class="panel">
    <h1><?= Localization::get('admin.users.list.title') ?></h1>

    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li><a href="/lobby" class="btn-back"><?= Localization::get('application.general.btn.back_to_lobby') ?></a></li>
            <li><a href="/admin" class="btn-back"><?= Localization::get('application.general.btn.back_to_dashboard') ?></a></li>
        </ul>
    </div>
    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li><a href="/admin/users/create" class="btn-back"><?= Localization::get('application.general.btn.create_new_user') ?></a></li>
        </ul>
    </div>

    <table class="table users-list">
        <thead>
            <tr>
                <th class="username"><?= Localization::get('admin.users.list.header.username') ?></th>
                <th class="email"><?= Localization::get('admin.users.list.header.email') ?></th>
                <th class="role"><?= Localization::get('admin.users.list.header.role') ?></th>
                <th class="status"><?= Localization::get('admin.users.list.header.status') ?></th>
                <th class="options"><?= Localization::get('admin.users.list.header.options') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="username"><?= htmlspecialchars($user->getUsername()) ?></td>
                        <td class="email"><?= htmlspecialchars($user->getEmail()) ?></td>
                        <td class="role">
                            <span class="role-badge role-<?= strtolower($user->getRole()) ?>">
                                <?= htmlspecialchars($user->getRole()) ?>
                            </span>
                        </td>
                        <td class="status">
                            <span class="status-badge status-<?= strtolower($user->getStatus()) ?>">
                                <?= htmlspecialchars($user->getStatus()) ?> 
                            </span>
                        </td>
                        <td class="options">
                            <div class="btn-actions">
                                <a href="/admin/user/edit/<?= $user->getId() ?>" class="btn action-btn btn-primary" title="Edit"><?= Localization::get('application.general.icon.edit') ?></a>

                                <form action="/admin/users/<?= $user->getId() ?>" method="POST">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_csrf_token" value="<?= Csrf::generate() ?>">
                                    <button type="submit" class="btn action-btn btn-danger" onclick="return confirm('Delete user: <?= htmlspecialchars($user->getUsername()) ?>?')">
                                        <?= Localization::get('application.general.icon.delete') ?>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4"><?= Localization::get('admin.users.list.header.no_users') ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>