<?php
// src/Constants/HealthConstants.php 

namespace App\Constants;

final class HealthConstants
{
    /**
     * Defines health check identifiers and health states.
     */
    public const CHECK = 'health';
    public const STATUS_HEALTHY = 'healthy';
    public const STATUS_CRITICAL = 'critical';
    public const STATUS_WARNING = 'warning';
}
