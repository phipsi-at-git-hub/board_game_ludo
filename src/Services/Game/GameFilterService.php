<?php

// src/Service/Game/GameFilterService.php

namespace App\Services\Game;

use App\Constants\Application;
use App\Core\Date\DateRange;
use App\Models\User\UserModel;

/**
 * GameFilterService
 */
final class GameFilterService
{
    private ?string $userId;

    /** @var string[]|null */
    private ?array $userRelations;

    /** @var string[]|null */
    private ?array $statuses;

    private ?string $createdFrom;
    private ?string $createdTo;

    private ?int $playerCount;
    private ?int $playerCountMin;
    private ?int $playerCountMax;

    private ?bool $allowBots;
    private ?bool $strictGoalOrder;

    /** @var string[]|null */
    private ?array $gameIds;

    /**
     * __construct
     *
     * @param ?string $userId
     * @param ?array $userRelations
     * @param ?array $statuses
     * @param ?DateRange $dateRange
     * @param ?int $playerCount
     * @param ?int $playerCountMin
     * @param ?int $playerCountMax
     * @param ?bool $allowBots
     * @param ?bool $strictGoalOrder
     * @param ?array $gameIds
     * @return void
     */
    public function __construct(
        ?string $userId = null,
        ?array $userRelations = null,
        ?array $statuses = null,
        ?DateRange $dateRange = null,
        ?int $playerCount = null,
        ?int $playerCountMin = null,
        ?int $playerCountMax = null,
        ?bool $allowBots = null,
        ?bool $strictGoalOrder = null,
        ?array $gameIds = null,
    ) {
        $this->userId = $userId;
        $this->userRelations = $userRelations;
        $this->statuses = $statuses;

        $this->createdFrom = null;
        $this->createdTo = null;

        $this->playerCount = $playerCount;
        $this->playerCountMin = $playerCountMin;
        $this->playerCountMax = $playerCountMax;

        $this->allowBots = $allowBots;
        $this->strictGoalOrder = $strictGoalOrder;

        $this->gameIds = $gameIds;

        if ($dateRange !== null && !$dateRange->isEmpty()) {
            $this->setDateRange($dateRange);
        }
    }

    /**
     * Helper
     */
    /**
     * getStatusesByGamesArray
     *
     * @param array $games
     * @return ?array
     */
    public static function getStatusesByGamesArray(array $games): ?array 
    {
        $statuses = []; 
        foreach ($games as $game) {
            if (!in_array($game->getStatus(), $statuses)) {
                $statuses[] = $game->getStatus(); 
            }
        }
        return $statuses; 
    }

    /**
     * getUserRelationsByGamesArrayAndUser
     *
     * @param array $games
     * @param UserModel $user
     * @return ?array
     */
    public static function getUserRelationsByGamesArrayAndUser(array $games, UserModel $user): ?array 
    {
        $userRelations = []; 
        foreach ($games as $game) {
            if (!in_array(Application::GAMES_CREATED, $userRelations) && $game->isCreator($user)) {
                $userRelations[] = Application::GAMES_CREATED; 
            }

            if (!in_array(Application::GAMES_PARTICIPATED, $userRelations) && $game->isParticipant($user)) {
                $userRelations[] = Application::GAMES_PARTICIPATED; 
            }

            if (!in_array(Application::GAMES_WON, $userRelations) && $game->isWinner($user)) {
                $userRelations[] = Application::GAMES_WON; 
            }
        }
        return $userRelations; 
    }

    /**
     * getUserId
     *
     * @return ?string
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * setUserId
     *
     * @param ?string $userId
     * @return self
     */
    public function setUserId(?string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getUserRelations(): ?array
    {
        return $this->userRelations;
    }

    /**
     * @param string[]|null $userRelations
     */
    public function setUserRelations(?array $userRelations): self
    {
        $this->userRelations = $userRelations;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getStatuses(): ?array
    {
        return $this->statuses;
    }

    /**
     * @param string[]|null $statuses
     */
    public function setStatuses(?array $statuses): self
    {
        $this->statuses = $statuses;

        return $this;
    }

    /**
     * getCreatedFrom
     *
     * @return ?string
     */
    public function getCreatedFrom(): ?string
    {
        return $this->createdFrom;
    }

    /**
     * setCreatedFrom
     *
     * @param ?string $createdFrom
     * @return self
     */
    public function setCreatedFrom(?string $createdFrom): self
    {
        $this->createdFrom = $createdFrom;

        return $this;
    }

    /**
     * getCreatedTo
     *
     * @return ?string
     */
    public function getCreatedTo(): ?string
    {
        return $this->createdTo;
    }

    /**
     * setCreatedTo
     *
     * @param ?string $createdTo
     * @return self
     */
    public function setCreatedTo(?string $createdTo): self
    {
        $this->createdTo = $createdTo;

        return $this;
    }

    /**
     * setDateRange
     *
     * @param DateRange $dateRange
     * @return self
     */
    public function setDateRange(DateRange $dateRange): self
    {
        if ($dateRange->isEmpty()) {
            $this->createdFrom = null;
            $this->createdTo = null;

            return $this;
        }

        $start = $dateRange->getStart();
        $end = $dateRange->getEnd();

        $this->createdFrom = $start?->format(Application::FILE_DATE_TIME_FORMAT);
        $this->createdTo = $end?->format(Application::FILE_DATE_TIME_FORMAT);

        return $this;
    }

    /**
     * getPlayerCount
     *
     * @return ?int
     */
    public function getPlayerCount(): ?int
    {
        return $this->playerCount;
    }

    /**
     * setPlayerCount
     *
     * @param ?int $playerCount
     * @return self
     */
    public function setPlayerCount(?int $playerCount): self
    {
        $this->playerCount = $playerCount;

        return $this;
    }

    /**
     * getPlayerCountMin
     *
     * @return ?int
     */
    public function getPlayerCountMin(): ?int
    {
        return $this->playerCountMin;
    }

    /**
     * setPlayerCountMin
     *
     * @param ?int $playerCountMin
     * @return self
     */
    public function setPlayerCountMin(?int $playerCountMin): self
    {
        $this->playerCountMin = $playerCountMin;

        return $this;
    }

    /**
     * getPlayerCountMax
     *
     * @return ?int
     */
    public function getPlayerCountMax(): ?int
    {
        return $this->playerCountMax;
    }

    /**
     * setPlayerCountMax
     *
     * @param ?int $playerCountMax
     * @return self
     */
    public function setPlayerCountMax(?int $playerCountMax): self
    {
        $this->playerCountMax = $playerCountMax;

        return $this;
    }

    public function getAllowBots(): ?bool
    {
        return $this->allowBots;
    }

    public function setAllowBots(?bool $allowBots): self
    {
        $this->allowBots = $allowBots;

        return $this;
    }

    public function getStrictGoalOrder(): ?bool
    {
        return $this->strictGoalOrder;
    }

    public function setStrictGoalOrder(?bool $strictGoalOrder): self
    {
        $this->strictGoalOrder = $strictGoalOrder;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getGameIds(): ?array
    {
        return $this->gameIds;
    }

    /**
     * @param string[]|null $gameIds
     */
    public function setGameIds(?array $gameIds): self
    {
        $this->gameIds = $gameIds;

        return $this;
    }
}
