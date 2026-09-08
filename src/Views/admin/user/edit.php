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

    <div class="dashboard-grid">

        <!-- Account -->
        <div class="card dashboard-card">
            <h2><?= Localization::get('admin.users.edit.card.account.title') ?></h2>

            <form
                id="admin-user-account-form" 
                action="/api/admin/user/update"
                method="POST"
                data-bind-form 

                data-id="admin-user-account-form" 
                data-response="json" 
                
                data-notification-target="#form-response" >

                <input
                    type="hidden"
                    name="_method"
                    value="PUT" >

                <input
                    type="hidden"
                    name="_csrf_token"
                    value="<?= Csrf::generate() ?>" >

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

                <div class="nav-actions">
                    <button
                        type="submit"
                        class="btn btn-save">
                        <?= Localization::get('application.general.btn.save') ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- Permissions -->
        <div class="card dashboard-card">
            <h2><?= Localization::get('admin.users.edit.card.permissions.title') ?></h2>

            <form
                id="admin-user-role-form" 
                action="/api/admin/user/role"
                method="POST" 

                data-id="admin-user-role-form" 
                data-response="json" 
                
                data-notification-target="#form-response" >

                <input
                    type="hidden"
                    name="_method"
                    value="PUT">

                <input
                    type="hidden"
                    name="_csrf_token"
                    value="<?= Csrf::generate() ?>">

                <div class="form-row">
                    <label for="role">
                        <?= Localization::get('admin.users.edit.card.permissions.role') ?>
                    </label>

                    <select 
                        id="role"
                        name="role"
                        data-bind="role"
                        data-ui="badge-select" 
                        data-auto-save="change" >

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

            </form>

            <form
                id="admin-user-status-form" 
                action="/api/admin/user/status"
                method="POST" 

                data-id="admin-user-status-form" 
                data-response="json" 
                
                data-notification-target="#form-response" >

                <input
                    type="hidden"
                    name="_method"
                    value="PUT">

                <input
                    type="hidden"
                    name="_csrf_token"
                    value="<?= Csrf::generate() ?>">

                <div class="form-row">
                    <?= Localization::get('admin.users.edit.card.permissions.status') ?>

                    <select 
                        id="status"
                        name="status"
                        data-bind="status"
                        data-ui="switch" 
                        data-auto-save="change" 
                        class="enhanced" >

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

            </form>

        </div>

        <!-- Settings -->
        <div class="card dashboard-card">
            <h2><?= Localization::get('admin.users.edit.card.settings.title') ?></h2>

            <form
                id="admin-user-locale-form" 
                class="many" 
                action="/api/admin/user/locale"
                method="POST"
                data-bind-form 

                data-id="admin-user-locale-form" 
                data-response="json"
                data-after-success-navigation="reload" >

                <input
                    type="hidden"
                    name="_method"
                    value="PUT">

                <input
                    type="hidden"
                    name="_csrf_token"
                    value="<?= Csrf::generate() ?>">

                <div class="form-row">
                    <label for="role">
                        <?= Localization::get('admin.users.edit.card.settings.language') ?>
                    </label>

                    <select 
                        id="preferred_language"
                        name="preferred_language"
                        data-bind="preferred_language"
                        data-ui="badge-select" 
                        data-auto-save="change"  >

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

            </form>

            <form
                id="admin-user-settings-form" 
                action="/api/admin/user/settings"
                method="POST" 

                data-id="admin-user-settings-form" 
                data-response="json" 
                
                data-notification-target="#form-response" >

                <input
                    type="hidden"
                    name="_method"
                    value="PUT">

                <input
                    type="hidden"
                    name="_csrf_token"
                    value="<?= Csrf::generate() ?>">

                <div class="form-row">
                    <?= Localization::get('admin.users.edit.card.settings.camera_mode') ?>

                    <select 
                        id="preferred_camera_mode"
                        name="preferred_camera_mode"
                        data-bind="preferred_camera_mode"
                        data-ui="switch" 
                        data-auto-save="change" 
                        class="enhanced" >

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

            </form>

        </div>

        <!-- Security -->
        <div class="card dashboard-card danger-zone">
            <h2><?= Localization::get('admin.users.edit.card.security.title') ?></h2>

            <p>
                <?= Localization::get('admin.users.edit.card.security.reset_password_description') ?>
            </p>

            <form
                id="admin-user-send-reset-mail-form" 
                action="/api/admin/user/send_reset_mail"
                method="POST"
                data-bind-form 

                data-id="admin-user-send-reset-mail-form" 
                data-response="json" 
                
                data-notification-target="#form-response" >

                <input
                    type="hidden"
                    name="_method"
                    value="POST" >

                <input
                    type="hidden"
                    name="_csrf_token"
                    value="<?= Csrf::generate() ?>" >
                
                <input 
                    type="hidden" 
                    name="user_id" 
                    value="<?= $user->getId() ?>" >

                <div class="nav-actions">

                    <button class="btn btn-danger">
                        <?= Localization::get('admin.users.edit.card.security.send_reset_mail') ?>
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>