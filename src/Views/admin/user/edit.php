<?php

use App\Constants\Game\GameCameraModeConstants;
use App\Constants\LocalizationConstants;
use App\Constants\User\UserRoleConstants;
use App\Constants\User\UserStatusConstants;
use App\Core\Application\App;
use App\Core\Security\Csrf;
use App\Core\Localization;
use App\Models\User\UserModel;

/**
 * @var UserModel $user
 * @var UserModel $current_user
 */
?>

<div class="panel">

    <h1><?= Localization::get('admin.users.edit.title') . ' ' . $user->getUsername() ?></h1>

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
                
                <input 
                    type="hidden" 
                    name="user_id" 
                    value="<?= $user->getId() ?>" >

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
                
                <input 
                    type="hidden" 
                    name="user_id" 
                    value="<?= $user->getId() ?>" >

                <div class="form-row">
                    <label for="role">
                        <?= Localization::get('admin.users.edit.card.permissions.role') ?>
                    </label>

                    <select 
                        id="role"
                        name="role"
                        data-bind="role"
                        data-ui="badge-select" 
                        data-auto-save="change" 
                        <?= ($user->getId() === $current_user->getId()) ? 'disabled' : '' ?> >

                        <option
                            value="USER"
                            <?= $user->getRole() === UserRoleConstants::USER ? 'selected' : '' ?> >
                            <?= Localization::get('application.general.' . strtolower(UserRoleConstants::USER)) ?>
                        </option>

                        <option
                            value="MODERATOR"
                            <?= $user->getRole() === UserRoleConstants::MODERATOR ? 'selected' : '' ?> >
                            <?= Localization::get('application.general.' . strtolower(UserRoleConstants::MODERATOR)) ?>
                        </option>

                        <option
                            value="GAME_MASTER"
                            <?= $user->getRole() === UserRoleConstants::GAME_MASTER ? 'selected' : '' ?> >
                            <?= Localization::get('application.general.' . strtolower(UserRoleConstants::GAME_MASTER)) ?>
                        </option>

                        <option
                            value="ADMIN"
                            <?= $user->getRole() === UserRoleConstants::ADMIN ? 'selected' : '' ?> >
                            <?= Localization::get('application.general.' . strtolower(UserRoleConstants::ADMIN)) ?>
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
                
                <input 
                    type="hidden" 
                    name="user_id" 
                    value="<?= $user->getId() ?>" >

                <div class="form-row">
                    <?= Localization::get('admin.users.edit.card.permissions.status') ?>

                    <select 
                        id="status"
                        name="status"
                        data-bind="status"
                        data-ui="switch" 
                        data-auto-save="change" 
                        class="enhanced" 
                        <?= ($user->getId() === $current_user->getId()) ? 'disabled' : '' ?> >

                        <option
                            value="INACTIVE"
                            data-state="inactive"
                            <?= $user->getStatus() === 'INACTIVE' ? 'selected' : '' ?> >
                            <?= Localization::get('application.general.' . strtolower(UserStatusConstants::INACTIVE)) ?>
                        </option>

                        <option
                            value="ACTIVE"
                            data-state="active"
                            <?= $user->getStatus() === 'ACTIVE' ? 'selected' : '' ?> >
                            <?= Localization::get('application.general.' . strtolower(UserStatusConstants::ACTIVE)) ?>
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
                
                <input 
                    type="hidden" 
                    name="user_id" 
                    value="<?= $user->getId() ?>" >

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
                            value="<?= LocalizationConstants::DE_DE ?>"
                            <?= $user->getPreferredLanguage() === LocalizationConstants::DE_DE ? 'selected' : '' ?> >
                            <?= Localization::get('languages.label.' . strtolower(LocalizationConstants::DE_DE)) ?>
                        </option>

                        <option
                            value="<?= LocalizationConstants::EN_US ?>"
                            <?= $user->getPreferredLanguage() === LocalizationConstants::EN_US ? 'selected' : '' ?> >
                            <?= Localization::get('languages.label.' . strtolower(LocalizationConstants::EN_US)) ?>
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
                
                <input 
                    type="hidden" 
                    name="user_id" 
                    value="<?= $user->getId() ?>" >

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
                            value="<?= GameCameraModeConstants::FOLLOW ?>"
                            data-state="inactive"
                            <?= $user->getPreferredCameraMode() === GameCameraModeConstants::FOLLOW ? 'selected' : '' ?> >
                            <?= Localization::get('game.camera.mode.' . strtolower(GameCameraModeConstants::FOLLOW)) ?>
                        </option>

                        <option
                            value="<?= GameCameraModeConstants::FIXED ?>"
                            data-state="active"
                            <?= $user->getPreferredCameraMode() === GameCameraModeConstants::FIXED ? 'selected' : '' ?> >
                            <?= Localization::get('game.camera.mode.' . strtolower(GameCameraModeConstants::FIXED)) ?>
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
                
                data-notification-target="#form-response"
                data-confirm
                data-confirm-title="<?= Localization::get('application.modal.messages.security.send_reset_password.title') ?>"
                data-confirm-message="<?= Localization::get('application.modal.messages.security.send_reset_password.confirm') ?>" >

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