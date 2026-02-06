<?php
// index.php
ob_start();

// 1. Load base paths
require __DIR__ . '/../bootstrap/paths.php';

// 2. Composer Autoload
require BASE_PATH . '/vendor/autoload.php';

// 3. Load app bootstrap
require BASE_PATH . '/bootstrap/app.php';

// 4. Security / Session
require CONFIG_PATH . '/security.php';

// 5. Router
use App\Core\Router;

//$router = require CONFIG_PATH . '/routes.php';
$router = require BASE_PATH . '/bootstrap/routes.php';
$router->dispatch();

// 6. Debug in DEV
use App\Core\Debug;
Debug::render();