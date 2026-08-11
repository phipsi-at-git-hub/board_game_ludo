<?php
// src/Core/Dto/Logging/EntryFilterContext.php

namespace App\Core\Dto\Logging;

final class EntryFilterContext {
    private static function create(): array {
        return [
            // Filter
            'channels' => [],
            'date_range' => null,

            // Available filter options
            'available_channels' => [],

            // Filtered entries
            'entries_count' => 0,
        ];
    }


    public static function fromFilter(array $channels, String $date_range, array $available_channels,  int $entries_count ): array {
        $dto = self::create();

        // Filter
        $dto['channels'] = $channels;
        $dto['date_range'] = $date_range;

        // Available filter options
        $dto['available_channels'] = $available_channels;

        // Filtered entries count
        $dto['entries_count'] = $entries_count;

        return $dto;
    }
}
