<?php
// src/Core/Dto/User/UserFilterContext.php

namespace App\Core\Dto\User;

use App\Constants\Application;

/**
 * UserFilterContext
 */
final readonly class UserFilterContext
{
    public array $statuses; 
    public array $roles; 
    public ?string $dateRangeLastLogin; 
    public int $users_count; 
    public array $availableStatuses; 
    public array $availableRoles; 

    /**
     * __construct
     *
     * @param array $statuses
     * @param array $roles
     * @return void
     */
    public function __construct(
        array $statuses, 
        array $roles, 
        ?string $dateRangeLastLogin, 
        array $users, 
    )
    {
        $this->statuses = $statuses; 
        $this->roles = $roles; 
        $this->dateRangeLastLogin = $dateRangeLastLogin; 
        $this->users_count = count($users); 

        $this->availableStatuses = [
            Application::ACTIVE, 
            Application::INACTIVE, 
            Application::CLOSED, 
            Application::BLOCKED, 
        ]; 
        $this->availableRoles = [
            Application::USER, 
            Application::ADMIN, 
            Application::MODERATOR, 
            Application::GAME_MASTER, 
        ]; 
    } 

    /**
     * fromFilter
     *
     * @param array $statuses
     * @param array $roles
     * @return array
     */
    public static function fromFilter(
        array $statuses, 
        array $roles, 
        ?string $dateRangeLastLogin, 
        array $users, 
    ): array
    {
        $context = new self(
            statuses: $statuses, 
            roles: $roles, 
            dateRangeLastLogin: $dateRangeLastLogin, 
            users: $users, 
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
            'statuses' => $this->statuses, 
            'roles' => $this->roles, 
            'date_range_last_login' => $this->dateRangeLastLogin, 
            'users_count' => $this->users_count, 
            'available_statuses' => $this->availableStatuses, 
            'available_roles' => $this->availableRoles, 
        ]; 
    } 
}