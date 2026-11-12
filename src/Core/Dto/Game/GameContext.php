<?php
// src/Core/Dto/Game/GameContext.php

namespace App\Core\Dto\Game;

use App\Core\Localization;
use App\Models\Game\GameModel;
use App\Models\User\UserModel;
use App\Policies\GamePolicy;

/**
 * GameContext
 */
final readonly class GameContext
{
    public ?string $game_id; 
    public ?string $game_name; 

    public ?string $status; 
    public ?string $ruleset; 

    public int $player_count; 
    public string $player_count_label; 
    public string $player_count_category; 
    public int $player_max; 

    public bool $is_private; 
    public string $is_private_label; 

    public bool $is_locked; 
    public string $is_locked_label; 

    public bool $is_joined; 

    public bool $is_owner; 
    public string $is_owner_label; 

    public bool $is_admin; 

    public array $permissions; 

    /**
     * __construct
     *
     * @param GameModel $game
     * @param UserModel $user
     * @return void
     */
    public function __construct(
        GameModel $game, 
        UserModel $user, 
    )
    {
        $this->game_id = $game->getId(); 
        $this->game_name = $game->getName(); 

        $this->status = $game->getStatus(); 
        $this->ruleset = Localization::get('game.ruleset.' . $game->getRuleSetModel()->getPreset()); 

        $this->player_count = $game->getPlayerCount(); 
        $this->player_count_label = $game->getPlayerCount() . '/' . $game->getPlayerMax(); 
        $this->player_count_category = 'player-count-category-' . $game->getPlayerCountCategory(); 
        $this->player_max = $game->getPlayerMax(); 

        $this->is_private = $game->isPrivate(); 
        $this->is_private_label = strtoupper(
            $game->isPrivate() 
                ? Localization::get('application.general.label.private') 
                : Localization::get('application.general.label.public')
        );

        $this->is_locked = $game->isLocked(); 
        $this->is_locked_label = strtoupper(
            $game->isLocked() 
                ? Localization::get('application.general.label.locked') 
                : Localization::get('application.general.label.open')
        ); 

        $this->is_joined = $game->hasPlayer($user->getId()); 

        $this->is_owner = $game->getCreatedByUserId() === $user->getId(); 
        $this->is_owner_label = $this->is_owner 
            ? strtoupper(Localization::get('application.general.label.owner')) 
            : ''; 

        $this->is_admin = $user->isAdmin(); 

        $this->permissions = [
            'join' => GamePolicy::canJoin($game, $user), 
            'leave' => GamePolicy::canLeave($game, $user), 

            'edit' => GamePolicy::canEdit($game, $user), 
            'delete' => GamePolicy::canDelete($game, $user), 

            'start' => GamePolicy::canStart($game, $user), 
            'pause' => GamePolicy::canPause($game, $user), 

            'reset' => GamePolicy::canReset($game, $user), 
            'cancel' => GamePolicy::canCancel($game, $user), 
        ]; 
    }

    public static function fromGame(
        GameModel $game, 
        UserModel $user, 
    ): array {
        $context = new self(
            game: $game, 
            user: $user, 
        ); 

        return $context->jsonSerialize(); 
    } 

    /**
     * jsonSerialize
     *
     * @return array
     */
    public function jsonSerialize(): array 
    {
        return [
            'game_id' => $this->game_id, 
            'game_name' => $this->game_name, 

            'status' =>$this->status, 
            'ruleset' => $this->ruleset, 

            'player_count' => $this->player_count, 
            'player_count_label' => $this->player_count_label, 
            'player_count_category' => $this->player_count_category, 
            'player_max' => $this->player_max, 

            'is_private' => $this->is_private, 
            'is_private_label' => $this->is_private_label, 

            'is_locked' => $this->is_locked, 
            'is_locked_label' => $this->is_locked_label, 

            'is_joined' => $this->is_joined, 

            'is_owner' => $this->is_owner, 
            'is_owner_label' => $this->is_owner_label, 

            'is_admin' => $this->is_admin, 

            'permissions' => $this->permissions, 
        ]; 
    }
}
