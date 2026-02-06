<?php
// app.php

// 1. Paths
require __DIR__ . '/paths.php';

// 1.1
use App\Core\Env;
Env::get();

// 2. Composer Autoload
require BASE_PATH . '/vendor/autoload.php';

use App\Core\Localization;
use Dotenv\Dotenv;

// 3. Load environment variables
$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// 4. Set error reporting based on environment
if ($_ENV['APP_ENV'] === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// 5. Load Localization
Localization::load(TRANSLATIONS_PATH, 'en-us');

// 6. Optional: global error handling, Logger, etc.
