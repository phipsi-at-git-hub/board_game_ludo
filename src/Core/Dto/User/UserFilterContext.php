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
    )
    {
        $this->statuses = $statuses; 
        $this->roles = $roles; 

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
    ): array
    {
        $context = new self(
            statuses: $statuses, 
            roles: $roles, 
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
            'available_statuses' => $this->availableStatuses, 
            'available_roles' => $this->availableRoles, 
        ]; 
    } 
}