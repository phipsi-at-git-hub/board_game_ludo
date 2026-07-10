<?php

use App\Core\Csrf;
use App\Core\Localization;

/**
 * Required:
 * @var object $game
 * @var object $current_user
 *
 * Optional:
 * @var bool $can_join
 * @var bool $can_leave
 * @var bool $can_edit
 * @var bool $can_delete
 * @var bool $can_start
 * @var bool $can_pause
 * @var bool $can_play
 * @var bool $can_reset
 */
?>

<div
    class="btn-actions"
    onclick="event.stopPropagation();">

    <form
        method="POST"
        action="/api/game/join/<?= $game->getId() ?>"
        data-action-container="join" 
            data-response="json" 
        <?= $can_join ? '' : 'hidden' ?> >

        <input
            type="hidden"
            name="_csrf_token"
            value="<?= Csrf::generate() ?>" >

        <button
            type="submit"
            class="btn btn-save"
            data-action="submit" >
            <?= Localization::get('application.general.label.join') ?>
        </button>

    </form>

    <form
        method="POST"
        action="/api/game/leave/<?= $game->getId() ?>"
        data-action-container="leave" 
            data-response="json" 
        <?= $can_leave ? '' : 'hidden' ?> >

        <input
            type="hidden"
            name="_csrf_token"
            value="<?= Csrf::generate() ?>" >

        <button
            type="submit"
            class="btn btn-save"
            data-action="submit" >
            <?= Localization::get('application.general.label.leave') ?>
        </button>

    </form>

    <?php if ($can_edit): ?>

        <a
            href="/game/edit/<?= $game->getId() ?>"
            class="btn btn-secondary" >
            <?= Localization::get('application.general.label.edit') ?>
        </a>

    <?php endif; ?>

    <?php if ($can_delete): ?>

        <form
            method="POST"
            action="/api/game/delete" 
            data-action="delete" 
            data-action-target-id="<?= $game->getId() ?>" 
            data-action-container="delete" 
            data-response="json" 
            data-confirm 
            data-confirm-title="<?= Localization::get('application.modal.messages.game.delete.title') ?>" 
            data-confirm-message="<?= Localization::get('application.modal.messages.game.delete.confirm') ?>" 
            >

            <input
                type="hidden"
                name="_method"
                value="DELETE" >

            <input
                type="hidden"
                name="_csrf_token"
                value="<?= Csrf::generate() ?>" >

            <input
                type="hidden"
                name="game_id"
                value="<?= $game->getId() ?>" >

            <button
                type="submit"
                class="btn btn-danger"
                data-action="submit" >
                <?= Localization::get('application.general.label.delete') ?>
            </button>

        </form>

    <?php endif; ?>

</div>
