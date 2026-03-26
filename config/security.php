<?php
// config/security.php

// No output before this file!
// Optional - Security Settings
ini_set('session.use_strict_mode', 1); // prevents Session-Fixation
ini_set('session.use_only_cookies', 1); // only Cookies, no URL-PHPSESSID

// Session-Cookie parameter
$secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'; // true nur bei HTTPS
session_set_cookie_params([
    'lifetime' => 0, // until browser is closed
    'path' => '/',
    'domain' => '', // current Domain
    'secure' => $secure, // only HTTPS
    'httponly' => true, // not accessible via JS
    'samesite' => 'Lax' // prevents against CSRF Cross-Site-Requests
]);

// Session starts only one time
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optional - Secure Header
header('X-Frame-Options: SAMEORIGIN'); 
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer-when-downgrade');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
