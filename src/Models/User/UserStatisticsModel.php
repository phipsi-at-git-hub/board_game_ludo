<?php
// src/Models/User/UserStatisticsModel.php
namespace App\Models\User;

use App\Constants\Application;
use App\Core\Logging\Logger;
use App\Models\BaseModel;
use Throwable;

final class UserStatisticsModel extends BaseModel {
    private string $user_id;
    private int $logins;
    private int $games_created;
    private int $games_participated;
    private int $games_won;

    /**
     * User Statistics - Find statistics by user id
     *
     * findByUserId
     *
     * @param string $user_id
     * @return ?self
     */
    public static function findByUserId(string $user_id): ?self {
        $row = static::fetchOne(
            sprintf(
                "SELECT * FROM %s WHERE %s = :user_id LIMIT 1",
                Application::TABLE_USERS_STATISTICS,
                Application::USER_ID
            ),
            [
                'user_id' => $user_id
            ]
        );
        return $row ? self::fromArray($row) : null;
    }

    /**
     * User Statistics - Create statistics for user
     *
     * create
     *
     * @param string $user_id
     * @return self
     */
    public static function create(string $user_id): self {
        static::execute(
            sprintf(
                "INSERT INTO %s (
                    %s,
                    %s,
                    %s,
                    %s,
                    %s
                ) VALUES (
                    :user_id,
                    0,
                    0,
                    0,
                    0
                )",
                Application::TABLE_USERS_STATISTICS,
                Application::USER_ID,
                Application::LOGINS,
                Application::GAMES_CREATED,
                Application::GAMES_PARTICIPATED,
                Application::GAMES_WON
            ),
            [
                'user_id' => $user_id
            ]
        );
        return self::findByUserId($user_id);
    }

    /**
     * User Statistics - Update logins
     *
     * updateLogins
     *
     * @param int $logins
     * @return bool
     */
    public function updateLogins(int $logins): bool {
        $this->logins = $logins;
        return $this->update([Application::LOGINS => $this->logins]);
    }

    /**
     * User Statistics - Update created games
     *
     * updateGamesCreated
     *
     * @param int $games_created
     * @return bool
     */
    public function updateGamesCreated(int $games_created): bool {
        $this->games_created = $games_created;
        return $this->update([Application::GAMES_CREATED => $this->games_created]);
    }

    /**
     * User Statistics - Update participated games
     *
     * updateGamesParticipated
     *
     * @param int $games_participated
     * @return bool
     */
    public function updateGamesParticipated(int $games_participated): bool {
        $this->games_participated = $games_participated;
        return $this->update([Application::GAMES_PARTICIPATED => $this->games_participated]);
    }

    /**
     * User Statistics - Update won games
     *
     * updateGamesWon
     *
     * @param int $games_won
     * @return bool
     */
    public function updateGamesWon(int $games_won): bool {
        $this->games_won = $games_won;
        return $this->update([Application::GAMES_WON => $this->games_won]);
    }

    /**
     * User Statistics - Increment logins
     *
     * incrementLogins
     *
     * @return bool
     */
    public function incrementLogins(): bool {
        return $this->increment(Application::LOGINS);
    }

    /**
     * User Statistics - Increment created games
     *
     * incrementGamesCreated
     *
     * @return bool
     */
    public function incrementGamesCreated(): bool {
        return $this->increment(Application::GAMES_CREATED);
    }

    /**
     * User Statistics - Increment participated games
     *
     * incrementGamesParticipated
     *
     * @return bool
     */
    public function incrementGamesParticipated(): bool {
        return $this->increment(Application::GAMES_PARTICIPATED);
    }

    /**
     * User Statistics - Increment won games
     *
     * incrementGamesWon
     *
     * @return bool
     */
    public function incrementGamesWon(): bool {
        return $this->increment(Application::GAMES_WON);
    }

    /**
     * User Statistics - Update statistics
     *
     * update
     *
     * @param array $statistics
     * @return bool
     */
    private function update(array $statistics): bool {
        $updates = [];
        $params = ['user_id' => $this->user_id];

        foreach ($statistics as $column => $value) {
            $updates[] = sprintf('%s = :%s', $column, $column);
            $params[$column] = $value;
        }

        if (empty($updates)) {
            return false;
        }

        try {
            return static::execute(
                sprintf(
                    "UPDATE %s SET %s WHERE %s = :user_id",
                    Application::TABLE_USERS_STATISTICS,
                    implode(', ', $updates),
                    Application::USER_ID
                ),
                $params
            );
        } catch (Throwable $e) {
            Logger::app()->warning(
                'Failed to update user statistics.',
                [
                    'user_id' => $this->user_id,
                    'error' => $e->getMessage()
                ]
            );

            return false;
        }
    }

    /**
     * User Statistics - Increment statistic
     *
     * increment
     *
     * @param string $column
     * @return bool
     */
    private function increment(string $column): bool {
        try {
            $result = static::execute(
                sprintf(
                    "UPDATE %s SET %s = %s + 1 WHERE %s = :user_id",
                    Application::TABLE_USERS_STATISTICS,
                    $column,
                    $column,
                    Application::USER_ID
                ),
                [
                    'user_id' => $this->user_id
                ]
            );

            if ($result) {
                $this->{$column}++;
            }

            return $result;
        } catch (Throwable $e) {
            Logger::app()->warning(
                'Failed to increment user statistic.',
                [
                    'user_id' => $this->user_id,
                    'column' => $column,
                    'error' => $e->getMessage()
                ]
            );

            return false;
        }
    }

    /**
     * Helper - Create UserStatisticsModel from Array
     *
     * fromArray
     *
     * @param array $data
     * @return self
     */
    private static function fromArray(array $data): self {
        $statistics = new self();

        $statistics->user_id = self::hydrateUUIDOrNull($data, Application::USER_ID);
        $statistics->logins = self::hydrateInt($data, Application::LOGINS);
        $statistics->games_created = self::hydrateInt($data, Application::GAMES_CREATED);
        $statistics->games_participated = self::hydrateInt($data, Application::GAMES_PARTICIPATED);
        $statistics->games_won = self::hydrateInt($data, Application::GAMES_WON);

        return $statistics;
    }

    // Get the value of user_id
    public function getUserId(): string {
        return $this->user_id;
    }

    // Get the value of logins
    public function getLogins(): int {
        return $this->logins;
    }

    // Get the value of games_created
    public function getGamesCreated(): int {
        return $this->games_created;
    }

    // Get the value of games_participated
    public function getGamesParticipated(): int {
        return $this->games_participated;
    }

    // Get the value of games_won
    public function getGamesWon(): int {
        return $this->games_won;
    }
}
