<?php
// bootstrap/routes.php

use App\Core\Env;
use App\Core\Router;

$cache_file = BASE_PATH . '/storage/cache/routes.php';

if (Env::isProd()) {
    // PROD Environment - cache routes.php and refresh only with rebuild
    if (!file_exists($cache_file)) {
        throw new RuntimeException('Route cache missing. Run: php bin/cache-routes.php');
    }

    /** @var Router $router */
    $router = require $cache_file;
} else {
    // DEV Environment - load routes.php with every refresh
    $router = require CONFIG_PATH . '/routes.php';
}
return $router;
