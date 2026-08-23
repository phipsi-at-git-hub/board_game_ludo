<?php

use App\Core\Csrf;
use App\Core\Localization;

/**
 * Required:
 * @var object $user
 */
?>

<div
    class="btn-actions"
    onclick="event.stopPropagation();">

    <!-- EDIT -->

    <a
        href="/admin/user/edit/<?= $user->getId() ?>"
        class="btn btn-secondary">

        <?= Localization::get('application.general.btn.edit') ?>

    </a>

    <!-- DELETE -->

    <form
        method="POST"
        action="/admin/user/<?= $user->getId() ?>"
        data-confirm
        data-confirm-title="<?= Localization::get('application.modal.messages.user.delete.title') ?>"
        data-confirm-message="<?= Localization::get('application.modal.messages.user.delete.confirm') ?>">

        <input
            type="hidden"
            name="_method"
            value="DELETE">

        <input
            type="hidden"
            name="_csrf_token"
            value="<?= Csrf::generate() ?>">

        <button
            type="submit"
            class="btn btn-danger"
            data-action="submit">

            <?= Localization::get('application.general.btn.delete') ?>

        </button>

    </form>

</div>