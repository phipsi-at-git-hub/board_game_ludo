<?php

use App\Constants\Application;
use App\Core\Csrf;
use App\Core\Localization;
use App\Models\User\UserModel;

/**
 * @var UserModel $current_user
 */
?>

<div class="panel">

    <h1>
        <?= Localization::get('account.profile.title') ?>
    </h1>

    <!-- Navigation -->
    <div class="nav-actions left">
        <ul class="nav-list horizontal">
            <li>
                <a href="/lobby" class="btn-back">
                    <?= Localization::get(
                        'application.general.btn.back_to_lobby'
                    ) ?>
                </a>
            </li>
        </ul>
    </div>


    <!-- Profile -->
    <div class="dashboard-grid">

        <!-- Profile Information -->
        <div class="card dashboard-card">

            <h2>
                <?= Localization::get(
                    'account.profile.information'
                ) ?>
            </h2>

            <form
                action="/api/account/update"
                method="POST"
                data-bind-form>

                <input
                    type="hidden"
                    name="_method"
                    value="PUT">

                <input
                    type="hidden"
                    name="_csrf_token"
                    value="<?= Csrf::generate() ?>">

                <div class="form-group">

                    <label for="username">
                        <?= Localization::get(
                            'account.profile.information.username'
                        ) ?>
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?= htmlspecialchars(
                            $current_user->getUsername()
                        ) ?>"
                        required>

                </div>

                <div class="form-group">

                    <label for="email">
                        <?= Localization::get(
                            'account.profile.information.email'
                        ) ?>
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars(
                            $current_user->getEmail()
                        ) ?>"
                        required>

                </div>

                <div class="nav-actions">

                    <button
                        type="submit"
                        class="btn btn-save">

                        <?= Localization::get(
                            'application.general.btn.save'
                        ) ?>

                    </button>

                </div>

            </form>

        </div>


        <!-- Profile Settings -->
        <div class="card dashboard-card">

            <h2>
                <?= Localization::get(
                    'account.profile.settings.title'
                ) ?>
            </h2>

            <form
                action="/api/account/settings"
                method="POST"
                data-bind-form>

                <input
                    type="hidden"
                    name="_method"
                    value="PUT">

                <input
                    type="hidden"
                    name="_csrf_token"
                    value="<?= Csrf::generate() ?>">


                <!-- Language -->
                <div class="form-row">

                    <label for="preferred_language">
                        <?= Localization::get(
                            'account.profile.settings.language'
                        ) ?>
                    </label>

                    <select
                        id="preferred_language"
                        name="preferred_language"
                        data-bind="preferred_language"
                        data-ui="badge-select">

                        <option
                            value="<?= Application::DE_DE ?>"
                            <?= $current_user->getPreferredLanguage()
                                === Application::DE_DE
                                ? 'selected'
                                : '' ?>>

                            <?= Localization::get(
                                'languages.label.' .
                                strtolower(Application::DE_DE)
                            ) ?>

                        </option>

                        <option
                            value="<?= Application::EN_US ?>"
                            <?= $current_user->getPreferredLanguage()
                                === Application::EN_US
                                ? 'selected'
                                : '' ?>>

                            <?= Localization::get(
                                'languages.label.' .
                                strtolower(Application::EN_US)
                            ) ?>

                        </option>

                    </select>

                </div>


                <!-- Camera Mode -->
                <div class="form-row">

                    <label for="preferred_camera_mode">
                        <?= Localization::get(
                            'account.profile.settings.camera_mode'
                        ) ?>
                    </label>

                    <select
                        id="preferred_camera_mode"
                        name="preferred_camera_mode"
                        data-bind="preferred_camera_mode"
                        data-ui="switch"
                        class="enhanced">

                        <option
                            value="<?= Application::CAMERA_MODE_FOLLOW ?>"
                            data-state="inactive"
                            <?= $current_user->getPreferredCameraMode()
                                === Application::CAMERA_MODE_FOLLOW
                                ? 'selected'
                                : '' ?>>

                            <?= Localization::get(
                                'game.camera.mode.' .
                                strtolower(
                                    Application::CAMERA_MODE_FOLLOW
                                )
                            ) ?>

                        </option>

                        <option
                            value="<?= Application::CAMERA_MODE_FIXED ?>"
                            data-state="active"
                            <?= $current_user->getPreferredCameraMode()
                                === Application::CAMERA_MODE_FIXED
                                ? 'selected'
                                : '' ?>>

                            <?= Localization::get(
                                'game.camera.mode.' .
                                strtolower(
                                    Application::CAMERA_MODE_FIXED
                                )
                            ) ?>

                        </option>

                    </select>

                </div>

            </form>

        </div>

    </div>



    <!-- Security -->
    <div class="dashboard-grid">

        <!-- Change Password -->
        <div class="card dashboard-card">

            <h2>
                <?= Localization::get('account.profile.change_password') ?>
            </h2>

            <form
                action="/api/account/password"
                method="POST"
                data-bind-form>

                <input
                    type="hidden"
                    name="_method"
                    value="PUT">

                <input
                    type="hidden"
                    name="_csrf_token"
                    value="<?= Csrf::generate() ?>" >

                <div class="form-group">

                    <label for="current_password">
                        <?= Localization::get('account.profile.change_password.current_password') ?>
                    </label>

                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        autocomplete="current-password"
                        required >

                </div>

                <div class="form-group">

                    <label for="new_password">
                        <?= Localization::get('account.profile.change_password.new_password') ?>
                    </label>

                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        autocomplete="new-password"
                        required>

                </div>

                <div class="form-group">

                    <label for="confirm_password">
                        <?= Localization::get('account.profile.change_password.confirm_password') ?>
                    </label>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        autocomplete="new-password"
                        required >

                </div>

                <div class="nav-actions">

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <?= Localization::get('account.profile.btn.change_password') ?>

                    </button>

                </div>

            </form>

        </div>

        <!-- Danger Zone -->
        <div class="card dashboard-card danger-zone">

            <h2>
                <?= Localization::get('account.profile.delete_account') ?>
            </h2>

            <p>
                <?= Localization::get('account.profile.delete_account.information') ?>
            </p>

            <form
                action="/api/account"
                method="POST" 
                data-id="user-<?= $current_user->getId() ?>-delete" 
                data-confirm
                data-confirm-title="<?= Localization::get('application.modal.messages.account.delete.title') ?>"
                data-confirm-message="<?= Localization::get('application.modal.messages.account.delete.confirm') ?>" >

                <input
                    type="hidden"
                    name="_method"
                    value="DELETE">

                <input
                    type="hidden"
                    name="_csrf_token"
                    value="<?= Csrf::generate() ?>">

                <div class="card-actions bottom">

                    <div class="nav-actions">

                        <button
                            type="submit"
                            class="btn btn-danger" 
                            data-action="submit" >

                            <?= Localization::get('account.profile.btn.delete_account') ?>

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>