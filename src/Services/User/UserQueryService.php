<?php
// src/Services/User/UserQueryService.php

namespace App\Services\User;

use App\Constants\Application;

/**
 * UserQueryService
 */
final class UserQueryService
{
    private array $joins = [];
    private array $conditions = [];
    private array $params = [];

    /**
     * __construct
     *
     * @param UserFilterService $filter
     * @return void
     */
    public function __construct(UserFilterService $filter)
    {
        $this->buildUserIdConditions($filter);
        $this->buildUsernameConditions($filter);
        $this->buildEmailConditions($filter);
        $this->buildStatusConditions($filter);
        $this->buildRoleConditions($filter);
        $this->buildPreferredLanguageConditions($filter);
        $this->buildPreferredCameraModeConditions($filter);
        $this->buildLastLoginConditions($filter);
        $this->buildCreatedConditions($filter);
        $this->buildUpdatedConditions($filter);
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
     * Builds conditions for a specific user ID.
     *
     * buildUserIdConditions
     *
     * @param UserFilterService $filter
     * @return void
     */
    private function buildUserIdConditions(UserFilterService $filter): void
    {
        $userId = $filter->getUserId();

        if ($userId === null) {
            return;
        }

        $this->conditions[] =
            Application::ID . ' = :user_id';

        $this->params['user_id'] = $userId;
    }

    /**
     * Builds conditions for username filters.
     *
     * buildUsernameConditions
     *
     * @param UserFilterService $filter
     * @return void
     */
    private function buildUsernameConditions(UserFilterService $filter): void
    {
        $username = $filter->getUsername();

        if ($username === null || $username === '') {
            return;
        }

        $this->conditions[] =
            Application::USERNAME . ' LIKE :username';

        $this->params['username'] = '%' . $username . '%';
    }

    /**
     * Builds conditions for email filters.
     *
     * buildEmailConditions
     *
     * @param UserFilterService $filter
     * @return void
     */
    private function buildEmailConditions(UserFilterService $filter): void
    {
        $email = $filter->getEmail();

        if ($email === null || $email === '') {
            return;
        }

        $this->conditions[] =
            Application::EMAIL . ' LIKE :email';

        $this->params['email'] = '%' . $email . '%';
    }

    /**
     * Builds conditions for user status filters.
     *
     * buildStatusConditions
     *
     * @param UserFilterService $filter
     * @return void
     */
    private function buildStatusConditions(UserFilterService $filter): void
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
            Application::STATUS .
            ' IN (' . implode(', ', $placeholders) . ')';
    }

    /**
     * Builds conditions for user role filters.
     *
     * buildRoleConditions
     *
     * @param UserFilterService $filter
     * @return void
     */
    private function buildRoleConditions(UserFilterService $filter): void
    {
        $roles = $filter->getRoles();

        if (empty($roles)) {
            return;
        }

        $placeholders = [];

        foreach ($roles as $index => $role) {
            $name = 'role_' . $index;

            $placeholders[] = ':' . $name;
            $this->params[$name] = $role;
        }

        $this->conditions[] =
            Application::ROLE .
            ' IN (' . implode(', ', $placeholders) . ')';
    }

    /**
     * Builds conditions for preferred language filters.
     *
     * buildPreferredLanguageConditions
     *
     * @param UserFilterService $filter
     * @return void
     */
    private function buildPreferredLanguageConditions(UserFilterService $filter): void {
        $language = $filter->getPreferredLanguage();

        if ($language === null) {
            return;
        }

        $this->conditions[] =
            Application::PREFERRED_LANGUAGE . ' = :preferred_language';

        $this->params['preferred_language'] = $language;
    }

    /**
     * Builds conditions for preferred camera mode filters.
     *
     * buildPreferredCameraModeConditions
     *
     * @param UserFilterService $filter
     * @return void
     */
    private function buildPreferredCameraModeConditions(UserFilterService $filter): void {
        $cameraMode = $filter->getPreferredCameraMode();

        if ($cameraMode === null) {
            return;
        }

        $this->conditions[] =
            Application::PREFERRED_CAMERA_MODE . ' = :preferred_camera_mode';

        $this->params['preferred_camera_mode'] = $cameraMode;
    }

    /**
     * Builds conditions for the last login date range.
     *
     * buildLastLoginConditions
     *
     * @param UserFilterService $filter
     * @return void
     */
    private function buildLastLoginConditions(UserFilterService $filter): void
    {
        $lastLoginFrom = $filter->getLastLoginFrom();

        if ($lastLoginFrom !== null) {
            $this->conditions[] =
                Application::LAST_LOGIN . ' >= :last_login_from';

            $this->params['last_login_from'] = $lastLoginFrom;
        }

        $lastLoginTo = $filter->getLastLoginTo();

        if ($lastLoginTo !== null) {
            $this->conditions[] =
                Application::LAST_LOGIN . ' <= :last_login_to';

            $this->params['last_login_to'] = $lastLoginTo;
        }
    }

    /**
     * Builds conditions for the creation date range.
     *
     * buildCreatedConditions
     *
     * @param UserFilterService $filter
     * @return void
     */
    private function buildCreatedConditions(UserFilterService $filter): void
    {
        $createdFrom = $filter->getCreatedFrom();

        if ($createdFrom !== null) {
            $this->conditions[] =
                Application::CREATED_AT . ' >= :created_from';

            $this->params['created_from'] = $createdFrom;
        }

        $createdTo = $filter->getCreatedTo();

        if ($createdTo !== null) {
            $this->conditions[] =
                Application::CREATED_AT . ' <= :created_to';

            $this->params['created_to'] = $createdTo;
        }
    }

    /**
     * Builds conditions for the update date range.
     *
     * buildUpdatedConditions
     *
     * @param UserFilterService $filter
     * @return void
     */
    private function buildUpdatedConditions(UserFilterService $filter): void
    {
        $updatedFrom = $filter->getUpdatedFrom();

        if ($updatedFrom !== null) {
            $this->conditions[] =
                Application::UPDATED_AT . ' >= :updated_from';

            $this->params['updated_from'] = $updatedFrom;
        }

        $updatedTo = $filter->getUpdatedTo();

        if ($updatedTo !== null) {
            $this->conditions[] =
                Application::UPDATED_AT . ' <= :updated_to';

            $this->params['updated_to'] = $updatedTo;
        }
    }
}
