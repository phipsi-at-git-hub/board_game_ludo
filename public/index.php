<?php
// index.php

// 1. Security / Session
require __DIR__ . '/../config/security.php';
ob_start();

// 2. Load base paths
require __DIR__ . '/../bootstrap/paths.php';

// 3. Composer Autoload
require BASE_PATH . '/vendor/autoload.php';

// 4. Load app bootstrap
require BASE_PATH . '/bootstrap/app.php';


// 5. Router
$router = require BASE_PATH . '/bootstrap/routes.php';
$router->dispatch();

// 6. Debug in DEV
use App\Core\Debug;
Debug::render();