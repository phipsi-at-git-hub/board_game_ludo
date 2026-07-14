<?php
// src/Core/Dto/Game/GameContext.php

namespace App\Core\Dto\Game;

use App\Core\Localization;
use App\Models\GameModel;
use App\Models\UserModel;
use App\Policies\GamePolicy;

final class GameContext {
    private static function create(): array {
        return [
            'game_id' => null,
            'game_name' => null,

            'status' => null,
            'ruleset' => null,

            'player_count' => 0,
            'player_max' => 0,

            'is_private' => false,
            'is_locked' => false,

            'is_joined' => false,
            'is_owner' => false,
            'is_admin' => false,

            'permissions' => [],
        ];
    }

    public static function fromGame(GameModel $game, UserModel $user): array {
        $dto = self::create();

        $dto['game_id'] = $game->getId();
        $dto['game_name'] = $game->getName();

        $dto['status'] = $game->getStatus();

        $dto['ruleset'] = Localization::get('game.ruleset.' . $game->getRuleSetModel()->getPreset()); 

        $dto['player_count'] = $game->getPlayerCount();
        $dto['player_count_label'] = $game->getPlayerCount() . '/' . $game->getPlayerMax();
        $dto['player_count_category'] = $game->getPlayerCountCategory();
        $dto['player_max'] = $game->getPlayerMax();

        $dto['is_private'] = $game->isPrivate();
        $dto['is_private_label'] = strtoupper($game->isPrivate() ? Localization::get('application.general.label.private') : Localization::get('application.general.label.public')); // $game->isPrivate();
        
        $dto['is_locked'] = $game->isLocked();
        $dto['is_locked_label'] = strtoupper($game->isLocked() ? Localization::get('application.general.label.locked') : Localization::get('application.general.label.open')); // $game->isLocked();

        $dto['is_joined'] = $game->hasPlayer($user->getId());

        $dto['is_owner'] = $game->getCreatedByUserId() === $user->getId(); 
        $dto['is_owner_label'] = ($game->getCreatedByUserId() === $user->getId()) ? strtoupper(Localization::get('application.general.label.owner')) : ''; // $game->getCreatedByUserId() === $user->getId()

        $dto['is_admin'] = $user->isAdmin();

        $dto['permissions'] = [
            'join' => GamePolicy::canJoin($game, $user), 
            'leave' => GamePolicy::canLeave($game, $user), 

            'edit' => GamePolicy::canEdit($game, $user), 
            'delete' => GamePolicy::canDelete($game, $user), 

            'start' => GamePolicy::canStart($game, $user), 
            'pause' => GamePolicy::canPause($game, $user), 

            'reset' => GamePolicy::canReset($game, $user), 
            'cancel' => GamePolicy::canCancel($game, $user), 
        ]; 
        return $dto;
    }
}
