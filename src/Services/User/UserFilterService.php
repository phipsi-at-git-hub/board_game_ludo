<?php
// src/Services/User/UserFilterService.php

namespace App\Services\User;

/**
 * UserFilterService
 */
final class UserFilterService
{
    private ?string $userId;
    private ?string $username;
    private ?string $email;

    /** @var string[]|null */
    private ?array $statuses;

    /** @var string[]|null */
    private ?array $roles;

    private ?string $preferredLanguage;
    private ?string $preferredCameraMode;

    private ?string $lastLoginFrom;
    private ?string $lastLoginTo;

    private ?string $createdFrom;
    private ?string $createdTo;

    private ?string $updatedFrom;
    private ?string $updatedTo;

    /**
     * __construct
     *
     * @param ?string $userId
     * @param ?string $username
     * @param ?string $email
     * @param ?array $statuses
     * @param ?array $roles
     * @param ?string $preferredLanguage
     * @param ?string $preferredCameraMode
     * @param ?string $lastLoginFrom
     * @param ?string $lastLoginTo
     * @param ?string $createdFrom
     * @param ?string $createdTo
     * @param ?string $updatedFrom
     * @param ?string $updatedTo
     */
    public function __construct(
        ?string $userId = null,
        ?string $username = null,
        ?string $email = null,
        ?array $statuses = null,
        ?array $roles = null,
        ?string $preferredLanguage = null,
        ?string $preferredCameraMode = null,
        ?string $lastLoginFrom = null,
        ?string $lastLoginTo = null,
        ?string $createdFrom = null,
        ?string $createdTo = null,
        ?string $updatedFrom = null,
        ?string $updatedTo = null,
    ) {
        $this->userId = $userId;
        $this->username = $username;
        $this->email = $email;

        $this->statuses = $statuses;
        $this->roles = $roles;

        $this->preferredLanguage = $preferredLanguage;
        $this->preferredCameraMode = $preferredCameraMode;

        $this->lastLoginFrom = $lastLoginFrom;
        $this->lastLoginTo = $lastLoginTo;

        $this->createdFrom = $createdFrom;
        $this->createdTo = $createdTo;

        $this->updatedFrom = $updatedFrom;
        $this->updatedTo = $updatedTo;
    }

    /**
     * Helper
     */
    /**
     * getStatusesByUsersArray
     *
     * @param array $users
     * @return ?array
     */
    public static function getStatusesByUsersArray(array $users): array
    {
        $statuses = []; 
        foreach ($users as $user) {
            if (!in_array($user->getStatus(), $statuses)) {
                $statuses[] = $user->getStatus(); 
            }
        }
        return $statuses; 
    }

    public static function getRolesByUsersArray(array $users): array
    {
        $roles = []; 
        foreach ($users as $user) {
            if (!in_array($user->getRole(), $roles)) {
                $roles[] = $user->getRole(); 
            }
        }
        return $roles; 
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
     * getUsername
     *
     * @return ?string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * setUsername
     *
     * @param ?string $username
     * @return self
     */
    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * getEmail
     *
     * @return ?string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * setEmail
     *
     * @param ?string $email
     * @return self
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * getStatuses
     *
     * @return string[]|null
     */
    public function getStatuses(): ?array
    {
        return $this->statuses;
    }

    /**
     * setStatuses
     *
     * @param string[]|null $statuses
     * @return self
     */
    public function setStatuses(?array $statuses): self
    {
        $this->statuses = $statuses;

        return $this;
    }

    /**
     * getRoles
     *
     * @return string[]|null
     */
    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     * setRoles
     *
     * @param string[]|null $roles
     * @return self
     */
    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * getPreferredLanguage
     *
     * @return ?string
     */
    public function getPreferredLanguage(): ?string
    {
        return $this->preferredLanguage;
    }

    /**
     * setPreferredLanguage
     *
     * @param ?string $preferredLanguage
     * @return self
     */
    public function setPreferredLanguage(?string $preferredLanguage): self
    {
        $this->preferredLanguage = $preferredLanguage;

        return $this;
    }

    /**
     * getPreferredCameraMode
     *
     * @return ?string
     */
    public function getPreferredCameraMode(): ?string
    {
        return $this->preferredCameraMode;
    }

    /**
     * setPreferredCameraMode
     *
     * @param ?string $preferredCameraMode
     * @return self
     */
    public function setPreferredCameraMode(?string $preferredCameraMode): self
    {
        $this->preferredCameraMode = $preferredCameraMode;

        return $this;
    }

    /**
     * getLastLoginFrom
     *
     * @return ?string
     */
    public function getLastLoginFrom(): ?string
    {
        return $this->lastLoginFrom;
    }

    /**
     * setLastLoginFrom
     *
     * @param ?string $lastLoginFrom
     * @return self
     */
    public function setLastLoginFrom(?string $lastLoginFrom): self
    {
        $this->lastLoginFrom = $lastLoginFrom;

        return $this;
    }

    /**
     * getLastLoginTo
     *
     * @return ?string
     */
    public function getLastLoginTo(): ?string
    {
        return $this->lastLoginTo;
    }

    /**
     * setLastLoginTo
     *
     * @param ?string $lastLoginTo
     * @return self
     */
    public function setLastLoginTo(?string $lastLoginTo): self
    {
        $this->lastLoginTo = $lastLoginTo;

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
     * getUpdatedFrom
     *
     * @return ?string
     */
    public function getUpdatedFrom(): ?string
    {
        return $this->updatedFrom;
    }

    /**
     * setUpdatedFrom
     *
     * @param ?string $updatedFrom
     * @return self
     */
    public function setUpdatedFrom(?string $updatedFrom): self
    {
        $this->updatedFrom = $updatedFrom;

        return $this;
    }

    /**
     * getUpdatedTo
     *
     * @return ?string
     */
    public function getUpdatedTo(): ?string
    {
        return $this->updatedTo;
    }

    /**
     * setUpdatedTo
     *
     * @param ?string $updatedTo
     * @return self
     */
    public function setUpdatedTo(?string $updatedTo): self
    {
        $this->updatedTo = $updatedTo;

        return $this;
    }
}
