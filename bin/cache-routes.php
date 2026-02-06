<?php
// bin/cache-routes.php

require __DIR__ . '/../bootstrap/paths.php';
require BASE_PATH . '/vendor/autoload.php';

use App\Core\Env;
use App\Core\Router;

if (!Env::isProd()) {
    echo "Route cache only allowed in PROD\n";
    exit(1);
}

/**@var Router $router */
$router = require CONFIG_PATH . '/routes.php';

$cache_dir = BASE_PATH . '/storage/cache';
$cache_file = $cache_dir . '/routes.php';

if (!is_dir($cache_dir)) {
    mkdir($cache_dir, 0775, true);
}

$serialized_router = serialize($router);

$content = <<<PHP
<?php
// AUTO-GENERATED FILE
// DO NOTE EDIT MANUALLY
// Generated at: {date('Y-m-d H:i:s')}

user App\Core\Router;

/** @var Router \$router */
\$router = unserialize(
    {$serialized_router}, 
    ['allowed_classes' => true]
);

return \$router;
PHP;

file_put_contents($cache_file, $content);

echo "Routes cached successfully\n";