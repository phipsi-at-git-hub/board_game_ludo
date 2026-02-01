<?php
// index.php
require_once __DIR__ . '/../vendor/autoload.php';

session_start();

$router = require __DIR__ . '/../config/routes.php';
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
