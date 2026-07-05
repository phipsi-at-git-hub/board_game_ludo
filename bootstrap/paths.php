<?php
// paths.php

// Define applications paths
defined('BASE_PATH') || define('BASE_PATH', dirname(__DIR__));
defined('CONFIG_PATH') || define('CONFIG_PATH', BASE_PATH . '/config');
defined('PUBLIC_PATH') || define('PUBLIC_PATH', BASE_PATH . '/public'); 
defined('TRANSLATIONS_PATH') || define('TRANSLATIONS_PATH', BASE_PATH . '/translations');

defined('SRC_PATH') || define('SRC_PATH', BASE_PATH . '/src');
defined('VIEWS_PATH') || define('VIEWS_PATH', SRC_PATH . '/Views');

defined('API_PATH') || define('API_PATH', '/api/game/');
defined('CSS_PATH') || define('CSS_PATH', '/css'); 
defined('IMG_PATH') || define('IMG_PATH', '/img'); 
defined('JS_PATH') || define('JS_PATH', '/js'); 