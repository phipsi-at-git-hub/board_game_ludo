<?php

use App\Core\Csrf; 
use App\Core\Localization; 
use App\Constants\Application; 
?>

<div class="panel">

    <h1>
        <?= Localization::get('admin.users.create.title') ?>
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

            <li>
                <a href="/admin/users" class="btn-back">
                    <?= Localization::get('application.general.btn.back_to_users') ?>
                </a>
            </li>
        </ul>
    </div>

    <form action="/admin/user/store" method="POST">

        <input
            type="hidden"
            name="_csrf_token"
            value="<?= Csrf::generate() ?>">

        <div class="dashboard-grid">

            <!-- Account -->
            <div class="card dashboard-card">

                <h2>
                    <?= Localization::get('admin.users.create.card.account.title') ?>
                </h2>

                <div class="form-group">
                    <label for="username">
                        <?= Localization::get('admin.users.create.card.account.username') ?>
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        required>
                </div>

                <div class="form-group">
                    <label for="email">
                        <?= Localization::get('admin.users.create.card.account.email') ?>
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        required>
                </div>

            </div>

            <!-- Permissions -->
            <div class="card dashboard-card">

                <h2>
                    <?= Localization::get('admin.users.create.card.permissions.title') ?>
                </h2>

                <div class="form-row">
                    <label for="role">
                        <?= Localization::get('admin.users.create.card.permissions.role') ?>
                    </label>

                    <select
                        id="role"
                        name="role"
                        data-ui="badge-select">

                        <option
                            value="<?= Application::USER ?>"
                            selected>
                            <?= Localization::get('application.general.user') ?>
                        </option>

                        <option
                            value="<?= Application::MODERATOR ?>">
                            <?= Localization::get('application.general.moderator') ?>
                        </option>

                        <option
                            value="<?= Application::GAME_MASTER ?>">
                            <?= Localization::get('application.general.game_master') ?>
                        </option>

                        <option
                            value="<?= Application::ADMIN ?>">
                            <?= Localization::get('application.general.admin') ?>
                        </option>

                    </select>
                </div>

                <div class="form-row">

                    <span>
                        <?= Localization::get('admin.users.create.card.permissions.status') ?>
                    </span>

                    <select
                        name="status"
                        data-ui="switch"
                        class="enhanced">

                        <option
                            value="<?= Application::ACTIVE ?>"
                            data-state="active"
                            selected>
                            <?= Localization::get('application.general.active') ?>
                        </option>

                        <option
                            value="<?= Application::INACTIVE ?>"
                            data-state="inactive">
                            <?= Localization::get('application.general.inactive') ?>
                        </option>

                    </select>

                </div>

            </div>

            <!-- Password Setup -->
            <div class="card dashboard-card">

                <h2>
                    <?= Localization::get('admin.users.create.card.password.title') ?>
                </h2>

                <p>
                    <?= Localization::get('admin.users.create.card.password.description') ?>
                </p>

                <p>
                    <?= Localization::get('admin.users.create.card.password.mail_notice') ?>
                </p>

            </div>

        </div>

        <div class="nav-actions">

            <button
                type="submit"
                class="btn btn-save">

                <?= Localization::get('application.general.btn.create') ?>

            </button>

        </div>

    </form>

</div>
