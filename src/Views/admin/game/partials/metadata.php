<?php

use App\Core\Localization;
use App\Models\GameModel;

/**
 * Required:
 * @var GameModel $game
 * @var string $status_text
 */

?>

<div class="card">

    <h2>
        <?= Localization::get('admin.game.partials.metadata.title') ?>
    </h2>

    <!-- Game -->
    <div class="nested-card">

        <h3>
            <?= Localization::get('admin.game.partials.metadata.card.game.title') ?>
        </h3>

        <div class="form-row">

            <span>
                <?= Localization::get('admin.game.partials.metadata.card.game.name') ?>
            </span>

            <span>
                <?= htmlspecialchars($game->getName()) ?>
            </span>

        </div>

        <div class="form-row">

            <span>
                <?= Localization::get('admin.game.partials.metadata.card.game.id') ?>
            </span>

            <span>
                <?= htmlspecialchars($game->getId()) ?>
            </span>

        </div>

        <div class="form-row">

            <span>
                <?= Localization::get('admin.game.partials.metadata.card.game.created_at') ?>
            </span>

            <span>
                <?= date(
                    'd.m.Y H:i',
                    strtotime($game->getCreatedAt())
                ) ?>
            </span>

        </div>

    </div>

    <!-- Owner -->
    <div class="nested-card">

        <h3>
            <?= Localization::get('admin.game.partials.metadata.card.owner.title') ?>
        </h3>

        <div class="form-row">

            <span>
                <?= Localization::get('admin.game.partials.metadata.card.owner.name') ?>
            </span>

            <span>
                <?= htmlspecialchars($game->getCreatedByUserName()) ?>
            </span>

        </div>

        <div class="form-row">

            <span>
                <?= Localization::get('admin.game.partials.metadata.card.owner.id') ?>
            </span>

            <span>
                <?= htmlspecialchars($game->getCreatedByUserId()) ?>
            </span>

        </div>

    </div>

    <!-- Status -->
    <div class="nested-card">

        <h3>
            <?= Localization::get('admin.game.partials.metadata.card.status.title') ?>
        </h3>

        <div class="form-row">

            <span>
                <?= Localization::get('admin.game.partials.metadata.card.status.state') ?>
            </span>

            <span class="status-badge <?= $status_class ?>">
                <?= $status_text ?>
            </span>

        </div>

        <?php if ($game->isFinished() && $game->getWinner()): ?>

            <div class="form-row">

                <span>
                    <?= Localization::get('admin.game.partials.metadata.card.status.winner') ?>
                </span>

                <span>
                    <?= htmlspecialchars(
                        $game->getWinner()->getUsername()
                    ) ?>
                </span>

            </div>

        <?php endif; ?>

    </div>

</div>
