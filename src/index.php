<?php
// index.php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$router = require __DIR__ . '/../config/routes.php';
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
