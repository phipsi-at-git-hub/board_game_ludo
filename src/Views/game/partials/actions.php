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
 * @var bool $can_cancel
 * @var bool $can_create_test
 */

$can_join ??= false;
$can_leave ??= false;

$can_edit ??= false;
$can_delete ??= false;

$can_start ??= false;
$can_pause ??= false;
$can_play ??= false;

$can_reset ??= false;
$can_cancel ??= false;
$can_create_test ??= false;

$show_start_pause ??= false; 

$show_players_card ??= false; 
$api_delete ??= true; 
?>

<div
    class="btn-actions"
    onclick="event.stopPropagation();">

    <!-- JOIN -->

    <form 
        data-id="game-<?= $game->getId() ?>-join" 
        method="POST" 
        action="/api/game/join/<?= $game->getId() ?>" 
        data-response="json" 
        data-bind-targets="
            game-<?= $game->getId() ?>-player-count
            <?php if ($show_players_card): ?>, game-<?= $game->getId() ?>-players<?php endif; ?>, 
            game-<?= $game->getId() ?>-join, 
            game-<?= $game->getId() ?>-leave
            <?php if ($show_start_pause): ?>, game-<?= $game->getId() ?>-start<?php endif; ?>
        " 
        data-bind-sources="game-<?= $game->getId() ?>-join, game-<?= $game->getId() ?>-leave" 
        data-bind-1-dto-key="permissions.leave" 
        data-bind-1-type="hidden" 
        <?= $can_join ? '' : 'hidden' ?> >

        <input
            type="hidden"
            name="_csrf_token"
            value="<?= Csrf::generate() ?>">

        <button
            type="submit"
            class="btn btn-save"
            data-action="submit">

            <?= Localization::get('application.general.btn.join') ?>

        </button>

    </form>

    <!-- LEAVE -->

    <form 
        data-id="game-<?= $game->getId() ?>-leave" 
        method="POST" 
        action="/api/game/leave/<?= $game->getId() ?>" 
        data-response="json" 
        data-bind-targets="
            game-<?= $game->getId() ?>-player-count
            <?php if ($show_players_card): ?>, game-<?= $game->getId() ?>-players<?php endif; ?>, 
            game-<?= $game->getId() ?>-join, 
            game-<?= $game->getId() ?>-leave
            <?php if ($show_start_pause): ?>, game-<?= $game->getId() ?>-start<?php endif; ?>
        " 
        data-bind-sources="game-<?= $game->getId() ?>-join, game-<?= $game->getId() ?>-leave" 
        data-bind-1-dto-key="permissions.join" 
        data-bind-1-type="hidden" 
        <?= $can_leave ? '' : 'hidden' ?> >

        <input
            type="hidden"
            name="_csrf_token"
            value="<?= Csrf::generate() ?>">

        <button
            type="submit"
            class="btn btn-save"
            data-action="submit">

            <?= Localization::get('application.general.btn.leave') ?>

        </button>

    </form>

    <!-- PLAY -->

    <?php if ($can_play): ?>

        <a
            href="/game/play/<?= $game->getId() ?>"
            class="btn btn-save">

            <?= Localization::get('application.general.btn.play') ?>

        </a>

    <?php endif; ?>

    <!-- START -->

    <?php if ($show_start_pause): ?>

        <form
            method="POST"
            action="/game/start" 
            data-id="game-<?= $game->getId() ?>-start" 
            data-bind-sources="game-<?= $game->getId() ?>-join, game-<?= $game->getId() ?>-leave" 
            data-bind-1-dto-key="permissions.start"
            data-bind-1-type="hidden"
            data-bind-1-invert="true" 
            <?= $can_start ? '' : 'hidden' ?> >

            <input
                type="hidden"
                name="_csrf_token"
                value="<?= Csrf::generate() ?>">

            <input
                type="hidden"
                name="game_id"
                value="<?= $game->getId() ?>">

            <button
                type="submit"
                class="btn btn-save">

                <?= Localization::get('application.general.btn.start') ?>

            </button>

        </form>

    <?php endif; ?>

    <!-- PAUSE -->

    <?php if ($show_start_pause && $can_pause): ?>

        <form
            method="POST"
            action="/game/pause">

            <input
                type="hidden"
                name="_csrf_token"
                value="<?= Csrf::generate() ?>">

            <input
                type="hidden"
                name="game_id"
                value="<?= $game->getId() ?>">

            <button
                type="submit"
                class="btn btn-secondary">

                <?= Localization::get('application.general.btn.pause') ?>

            </button>

        </form>

    <?php endif; ?>

    <!-- SOLO TEST -->

    <?php if ($can_create_test): ?>

        <form
            method="POST"
            action="/game/create_solo_test"
            data-confirm
            data-confirm-title="<?= Localization::get('application.modal.messages.game.create.test.title') ?>"
            data-confirm-message="<?= Localization::get('application.modal.messages.game.create.test.confirm') ?>">

            <input
                type="hidden"
                name="_csrf_token"
                value="<?= Csrf::generate() ?>">

            <input
                type="hidden"
                name="game_id"
                value="<?= $game->getId() ?>">

            <button
                type="submit"
                class="btn btn-secondary">

                <?= Localization::get('application.general.btn.create.test') ?>

            </button>

        </form>

    <?php endif; ?>

    <!-- RESET -->

    <?php if ($can_reset): ?>

        <form
            method="POST"
            action="/game/reset"
            data-confirm
            data-confirm-title="<?= Localization::get('application.modal.messages.game.reset.title') ?>"
            data-confirm-message="<?= Localization::get('application.modal.messages.game.reset.confirm') ?>">

            <input
                type="hidden"
                name="_csrf_token"
                value="<?= Csrf::generate() ?>">

            <input
                type="hidden"
                name="game_id"
                value="<?= $game->getId() ?>">

            <button
                type="submit"
                class="btn btn-secondary">

                <?= Localization::get('application.general.btn.reset') ?>

            </button>

        </form>

    <?php endif; ?>

    <!-- EDIT -->

    <?php if ($can_edit): ?>

        <a
            href="/game/edit/<?= $game->getId() ?>"
            class="btn btn-secondary">

            <?= Localization::get('application.general.btn.edit') ?>

        </a>

    <?php endif; ?>

    <!-- CANCEL -->

    <?php if ($can_cancel): ?>

        <form
            method="POST"
            action="/game/cancel"
            data-confirm
            data-confirm-title="<?= Localization::get('application.modal.messages.game.cancel.title') ?>"
            data-confirm-message="<?= Localization::get('application.modal.messages.game.cancel.confirm') ?>">

            <input
                type="hidden"
                name="_csrf_token"
                value="<?= Csrf::generate() ?>">

            <input
                type="hidden"
                name="game_id"
                value="<?= $game->getId() ?>">

            <button
                type="submit"
                class="btn btn-danger">

                <?= Localization::get('application.general.btn.cancel') ?>

            </button>

        </form>

    <?php endif; ?>

    <!-- DELETE -->

    <?php if ($can_delete): ?>

        <form
            method="POST"
            action="<?= $api_delete ? '/api' : '' ?>/game/delete"
            data-id="game-<?= $game->getId() ?>-delete" 
            data-bind-targets="game-<?= $game->getId() ?>-row" 
            
            <?php if ($api_delete): ?>data-response="json"<?php endif; ?>
            data-confirm
            data-confirm-title="<?= Localization::get('application.modal.messages.game.delete.title') ?>"
            data-confirm-message="<?= Localization::get('application.modal.messages.game.delete.confirm') ?>">

            <input
                type="hidden"
                name="_method"
                value="DELETE">

            <input
                type="hidden"
                name="_csrf_token"
                value="<?= Csrf::generate() ?>">

            <input
                type="hidden"
                name="game_id"
                value="<?= $game->getId() ?>">

            <button
                type="submit"
                class="btn btn-danger"
                data-action="submit">

                <?= Localization::get('application.general.btn.delete') ?>

            </button>

        </form>

    <?php endif; ?>

</div>