<?php
// bin/cache-routes.php

require __DIR__ . '/../bootstrap/paths.php';
require BASE_PATH . '/vendor/autoload.php';

use App\Core\Env;

if (!Env::isProd()) {
    echo "Route cache only allowed in PROD\n";
    exit(1);
}

$router = require CONFIG_PATH . '/routes.php';

$cache_file = BASE_PATH . '/storage/cache/routes.php';

$content = <<<PHP
<?php
// AUTO-GENERATED FILE - DO NOT EDIT

return unserialize(%s);
PHP;

file_put_contents(
    $cache_file, 
    sprintf($content, var_export(serialize($router), true))
);

echo "Routes cached successfully\n";