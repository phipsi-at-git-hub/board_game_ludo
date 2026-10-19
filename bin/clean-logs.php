<?php 
// bin/clean-logs.php

// Security: executable only in terminal (CLI)

use App\Core\Logging\LoggingConfiguration;
use App\Core\Persistence\FileSystem;
use App\Models\System\LoggingModel;

if (php_sapi_name() !== 'cli') {
    die("Access denied!\n"); 
}

// 1. Loading essential stuff
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap/paths.php'; 

// 2. Only get required channels 
$fileChannels = LoggingConfiguration::getChannelsWithFileStorage(); 
$channels = LoggingConfiguration::getChannels(); 

echo "Start log cleaning...\n"; 
$deleteCount = 0; 

foreach ($fileChannels as $channel) {
    // 3. Get retention days per channel from configuration
    // Fallback: 30 days 
    $retentionDays = LoggingConfiguration::getRetentionDays($channel); 

    echo "Checking channel '$channel' ...\n"; 

    // Calculate time frame for check 
    $startDay = $retentionDays + 1; 
    $endDay = $retentionDays + 60; 

    for ($i = $startDay; $i <= $endDay; $i++) {
        $targetDate = date('Y-m-d', strtotime("-$i days")); 
        $filename = LoggingConfiguration::getLogFileByDate($channel, $targetDate); 

        // Check and remove via logging classes 
        if ($filename !== null && FileSystem::getInstance()->exists($filename)) {
            if (LoggingModel::delete($channel, $targetDate)) {
                echo " -> $channel logs from $targetDate deleted.\n"; 
                $deleteCount++; 
            }
        }
    }
} 

echo "Cleanup completed. $deleteCount days removed.\n"; 
