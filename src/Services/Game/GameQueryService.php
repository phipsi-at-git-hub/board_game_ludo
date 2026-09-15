<?php
// src/Services/Game/GameQueryService.php

namespace App\Services\Game;

use App\Constants\Application;

final class GameQueryService
{
    private array $joins = [];
    private array $conditions = [];
    private array $params = [];

    public function __construct(GameFilterService $filter)
    {
        $this->buildUserConditions($filter);
        $this->buildStatusConditions($filter);
        $this->buildDateConditions($filter);
        $this->buildPlayerCountConditions($filter);
        $this->buildRuleConditions($filter);
        $this->buildGameIdConditions($filter);
    }

    /**
     * Returns the query joins.
     *
     * @return array
     */
    public function getJoins(): array
    {
        return $this->joins;
    }

    /**
     * Returns the query conditions.
     *
     * @return array
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * Returns the query parameters.
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Builds conditions for user-related filters.
     */
    private function buildUserConditions(GameFilterService $filter): void
    {
        $userId = $filter->getUserId();
        $relations = $filter->getUserRelations();

        if ($userId === null || empty($relations)) {
            return;
        }

        $conditions = [];

        if (in_array('created', $relations, true)) {
            $conditions[] =
                'g.' . Application::CREATED_BY_USER_ID . ' = :user_creator_id';

            $this->params['user_creator_id'] = $userId;
        }

        if (in_array('participated', $relations, true)) {
            $conditions[] =
                'EXISTS (
                    SELECT 1
                    FROM ' . Application::TABLE_PLAYERS . ' p
                    WHERE p.' . Application::GAME_ID . ' = g.' . Application::ID . '
                      AND p.' . Application::USER_ID . ' = :user_player_id
                )';

            $this->params['user_player_id'] = $userId;
        }

        if (in_array('won', $relations, true)) {
            $conditions[] =
                'EXISTS (
                    SELECT 1
                    FROM ' . Application::TABLE_STATE . ' ws
                    WHERE ws.' . Application::GAME_ID . ' = g.' . Application::ID . '
                      AND ws.' . Application::WINNER_USER_ID . ' = :user_winner_id
                )';

            $this->params['user_winner_id'] = $userId;
        }

        if (empty($conditions)) {
            return;
        }

        /*
         * Multiple relations mean:
         *
         * created OR participated OR won
         *
         * This allows a user to search for games where at least
         * one of the selected relations applies.
         */
        $this->conditions[] = count($conditions) === 1
            ? $conditions[0]
            : '(' . implode(' OR ', $conditions) . ')';
    }

    /**
     * Builds conditions for game status filters.
     */
    private function buildStatusConditions(GameFilterService $filter): void
    {
        $statuses = $filter->getStatuses();

        if (empty($statuses)) {
            return;
        }

        $placeholders = [];

        foreach ($statuses as $index => $status) {
            $name = 'status_' . $index;

            $placeholders[] = ':' . $name;
            $this->params[$name] = $status;
        }

        $this->conditions[] =
            'g.' . Application::STATUS .
            ' IN (' . implode(', ', $placeholders) . ')';
    }

    /**
     * Builds conditions for the creation date range.
     */
    private function buildDateConditions(GameFilterService $filter): void
    {
        $createdFrom = $filter->getCreatedFrom();

        if ($createdFrom !== null) {
            $this->conditions[] =
                'g.' . Application::CREATED_AT . ' >= :created_from';

            $this->params['created_from'] = $createdFrom;
        }

        $createdTo = $filter->getCreatedTo();

        if ($createdTo !== null) {
            $this->conditions[] =
                'g.' . Application::CREATED_AT . ' <= :created_to';

            $this->params['created_to'] = $createdTo;
        }
    }

    /**
     * Builds conditions for player count filters.
     */
    private function buildPlayerCountConditions(GameFilterService $filter): void
    {
        $playerCount = $filter->getPlayerCount();

        if ($playerCount !== null) {
            $this->conditions[] =
                '(SELECT COUNT(*)
                  FROM ' . Application::TABLE_PLAYERS . ' pc
                  WHERE pc.' . Application::GAME_ID . ' = g.' . Application::ID . '
                ) = :player_count';

            $this->params['player_count'] = $playerCount;
        }

        $playerCountMin = $filter->getPlayerCountMin();

        if ($playerCountMin !== null) {
            $this->conditions[] =
                '(SELECT COUNT(*)
                  FROM ' . Application::TABLE_PLAYERS . ' pc_min
                  WHERE pc_min.' . Application::GAME_ID . ' = g.' . Application::ID . '
                ) >= :player_count_min';

            $this->params['player_count_min'] = $playerCountMin;
        }

        $playerCountMax = $filter->getPlayerCountMax();

        if ($playerCountMax !== null) {
            $this->conditions[] =
                '(SELECT COUNT(*)
                  FROM ' . Application::TABLE_PLAYERS . ' pc_max
                  WHERE pc_max.' . Application::GAME_ID . ' = g.' . Application::ID . '
                ) <= :player_count_max';

            $this->params['player_count_max'] = $playerCountMax;
        }
    }

    /**
     * Builds conditions for rule filters.
     */
    private function buildRuleConditions(GameFilterService $filter): void
    {
        $allowBots = $filter->getAllowBots();

        if ($allowBots !== null) {
            $this->conditions[] =
                'r.' . Application::ALLOW_BOTS . ' = :allow_bots';

            $this->params['allow_bots'] = $allowBots ? 1 : 0;
        }

        $strictGoalOrder = $filter->getStrictGoalOrder();

        if ($strictGoalOrder !== null) {
            $this->conditions[] =
                'r.' . Application::STRICT_GOAL_ORDER . ' = :strict_goal_order';

            $this->params['strict_goal_order'] =
                $strictGoalOrder ? 1 : 0;
        }
    }

    /**
     * Builds conditions for specific game IDs.
     */
    private function buildGameIdConditions(GameFilterService $filter): void
    {
        $gameIds = $filter->getGameIds();

        if (empty($gameIds)) {
            return;
        }

        $placeholders = [];

        foreach ($gameIds as $index => $gameId) {
            $name = 'game_id_' . $index;

            $placeholders[] = ':' . $name;
            $this->params[$name] = $gameId;
        }

        $this->conditions[] =
            'g.' . Application::ID .
            ' IN (' . implode(', ', $placeholders) . ')';
    }
}
