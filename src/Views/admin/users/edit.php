<?php

use App\Constants\Application;
use App\Core\Csrf;
use App\Core\Localization;

/**
 * @var object $user
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

    <form action="/admin/users/edit/<?= $user->getId() ?>" method="POST">

        <input
            type="hidden"
            name="_csrf_token"
            value="<?= Csrf::generate() ?>">

        <div class="dashboard-grid">

            <!-- Account -->
            <div class="card dashboard-card">
                <h2><?= Localization::get('admin.users.edit.account') ?></h2>

                <div class="form-group">
                    <label for="username">
                        <?= Localization::get('admin.users.edit.username') ?>
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
                        <?= Localization::get('admin.users.edit.email') ?>
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
                <h2><?= Localization::get('admin.users.edit.permissions') ?></h2>

                <div class="form-row">
                    <label for="role">
                        <?= Localization::get('admin.users.edit.role') ?>
                    </label>

                    <select id="role" name="role" data-ui="badge-select">

                        <option
                            value="USER"
                            <?= $user->getRole() === Application::USER ? 'selected' : '' ?>>
                            USER
                        </option>

                        <option
                            value="MODERATOR"
                            <?= $user->getRole() === Application::MODERATOR ? 'selected' : '' ?>>
                            MODERATOR
                        </option>

                        <option
                            value="GAME_MASTER"
                            <?= $user->getRole() === Application::GAME_MASTER ? 'selected' : '' ?>>
                            GAME MASTER
                        </option>

                        <option
                            value="ADMIN"
                            <?= $user->getRole() === Application::ADMIN ? 'selected' : '' ?>>
                            ADMIN
                        </option>

                    </select>
                </div>

                <div class="form-row">
                    <?= Localization::get('admin.users.edit.status') ?>

                    <select
                        name="status"
                        data-ui="switch"
                        class="enhanced">

                        <option
                            value="ACTIVE"
                            data-state="active"
                            <?= $user->getStatus() === 'ACTIVE' ? 'selected' : '' ?>>
                            <?= Localization::get('application.general.active') ?>
                        </option>

                        <option
                            value="INACTIVE"
                            data-state="inactive"
                            <?= $user->getStatus() === 'INACTIVE' ? 'selected' : '' ?>>
                            <?= Localization::get('application.general.inactive') ?>
                        </option>

                    </select>
                </div>
            </div>

            <!-- Password -->
            <div class="card dashboard-card">
                <h2><?= Localization::get('admin.users.edit.password') ?></h2>

                <div class="form-group">
                    <label for="password">
                        <?= Localization::get('admin.users.edit.new_password') ?>
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="new-password">
                </div>

                <div class="form-group">
                    <label for="password_confirm">
                        <?= Localization::get('admin.users.edit.password_confirm') ?>
                    </label>

                    <input
                        type="password"
                        id="password_confirm"
                        name="password_confirm"
                        autocomplete="new-password">
                </div>

                <small>
                    <?= Localization::get('admin.users.edit.password_hint') ?>
                </small>
            </div>

            <!-- Security -->
            <div class="card dashboard-card">
                <h2><?= Localization::get('admin.users.edit.security') ?></h2>

                <p>
                    <?= Localization::get('admin.users.edit.reset_password_description') ?>
                </p>

                <div class="nav-actions">
                    <a
                        href="/admin/users/send-reset/<?= $user->getId() ?>"
                        class="btn btn-warning">

                        <?= Localization::get('admin.users.edit.send_reset_mail') ?>

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