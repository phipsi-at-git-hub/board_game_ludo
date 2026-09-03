<?php

use App\Constants\Application;
use App\Core\Application\App;
use App\Core\Csrf;
use App\Core\Localization;
use App\Models\User\UserModel;

/**
 * @var UserModel $user
 */
?>

<div class="panel">

    <h1><?= Localization::get('admin.users.edit.title') ?></h1>

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

            <li>
                <a href="/admin/users" class="btn-back">
                    <?= Localization::get('application.general.btn.back_to_users') ?>
                </a>
            </li>
        </ul>
    </div>

    <form action="/admin/user/edit/<?= $user->getId() ?>" method="POST">

        <input
            type="hidden"
            name="_csrf_token"
            value="<?= Csrf::generate() ?>">

        <div class="dashboard-grid">

            <!-- Account -->
            <div class="card dashboard-card">
                <h2><?= Localization::get('admin.users.edit.card.account.title') ?></h2>

                <div class="form-group">
                    <label for="username">
                        <?= Localization::get('admin.users.edit.card.account.username') ?>
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?= htmlspecialchars($user->getUsername()) ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="email">
                        <?= Localization::get('admin.users.edit.card.account.email') ?>
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($user->getEmail()) ?>"
                        required>
                </div>
            </div>

            <!-- Permissions -->
            <div class="card dashboard-card">
                <h2><?= Localization::get('admin.users.edit.card.permissions.title') ?></h2>

                <div class="form-row">
                    <label for="role">
                        <?= Localization::get('admin.users.edit.card.permissions.role') ?>
                    </label>

                    <select id="role" name="role" data-ui="badge-select">

                        <option
                            value="USER"
                            <?= $user->getRole() === Application::USER ? 'selected' : '' ?> >
                            <?= Localization::get('application.general.' . strtolower(Application::USER)) ?>
                        </option>

                        <option
                            value="MODERATOR"
                            <?= $user->getRole() === Application::MODERATOR ? 'selected' : '' ?> >
                            <?= Localization::get('application.general.' . strtolower(Application::MODERATOR)) ?>
                        </option>

                        <option
                            value="GAME_MASTER"
                            <?= $user->getRole() === Application::GAME_MASTER ? 'selected' : '' ?> >
                            <?= Localization::get('application.general.' . strtolower(Application::GAME_MASTER)) ?>
                        </option>

                        <option
                            value="ADMIN"
                            <?= $user->getRole() === Application::ADMIN ? 'selected' : '' ?> >
                            <?= Localization::get('application.general.' . strtolower(Application::ADMIN)) ?>
                        </option>

                    </select>
                </div>

                <div class="form-row">
                    <?= Localization::get('admin.users.edit.card.permissions.status') ?>

                    <select
                        name="status"
                        data-ui="switch"
                        class="enhanced">

                        <option
                            value="INACTIVE"
                            data-state="inactive"
                            <?= $user->getStatus() === 'INACTIVE' ? 'selected' : '' ?> >
                            <?= Localization::get('application.general.' . strtolower(Application::INACTIVE)) ?>
                        </option>

                        <option
                            value="ACTIVE"
                            data-state="active"
                            <?= $user->getStatus() === 'ACTIVE' ? 'selected' : '' ?> >
                            <?= Localization::get('application.general.' . strtolower(Application::ACTIVE)) ?>
                        </option>

                    </select>
                </div>
            </div>

            <!-- Settings -->
            <div class="card dashboard-card">
                <h2><?= Localization::get('admin.users.edit.card.settings.title') ?></h2>

                <div class="form-row">
                    <label for="role">
                        <?= Localization::get('admin.users.edit.card.settings.language') ?>
                    </label>

                    <select id="preferred_language" name="preferred_language" data-ui="badge-select">

                        <option
                            value="<?= Application::DE_DE ?>"
                            <?= $user->getPreferredLanguage() === Application::DE_DE ? 'selected' : '' ?> >
                            <?= Localization::get('languages.label.' . strtolower(Application::DE_DE)) ?>
                        </option>

                        <option
                            value="<?= Application::EN_US ?>"
                            <?= $user->getPreferredLanguage() === Application::EN_US ? 'selected' : '' ?> >
                            <?= Localization::get('languages.label.' . strtolower(Application::EN_US)) ?>
                        </option>

                    </select>
                </div>

                <div class="form-row">
                    <?= Localization::get('admin.users.edit.card.settings.camera_mode') ?>

                    <select
                        name="preferred_camera_mode"
                        data-ui="switch"
                        class="enhanced">

                        <option
                            value="<?= Application::CAMERA_MODE_FOLLOW ?>"
                            data-state="inactive"
                            <?= $user->getPreferredCameraMode() === Application::CAMERA_MODE_FOLLOW ? 'selected' : '' ?> >
                            <?= Localization::get('game.camera.mode.' . strtolower(Application::CAMERA_MODE_FOLLOW)) ?>
                        </option>

                        <option
                            value="<?= Application::CAMERA_MODE_FIXED ?>"
                            data-state="active"
                            <?= $user->getPreferredCameraMode() === Application::CAMERA_MODE_FIXED ? 'selected' : '' ?> >
                            <?= Localization::get('game.camera.mode.' . strtolower(Application::CAMERA_MODE_FIXED)) ?>
                        </option>

                    </select>
                </div>
            </div>

            <!-- Security -->
            <div class="card dashboard-card">
                <h2><?= Localization::get('admin.users.edit.card.security.title') ?></h2>

                <p>
                    <?= Localization::get('admin.users.edit.card.security.reset_password_description') ?>
                </p>

                <div class="nav-actions">
                    <a
                        href="/admin/user/send-reset/<?= $user->getId() ?>"
                        class="btn btn-danger">

                        <?= Localization::get('admin.users.edit.card.security.send_reset_mail') ?>

                    </a>
                </div>
            </div>

        </div>

        <div class="nav-actions">
            <button type="submit" class="btn btn-save">
                <?= Localization::get('application.general.btn.save') ?>
            </button>
        </div>

    </form>

</div>