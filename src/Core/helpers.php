<?php
// Core/helpers.php

function class_basename(string $class): string {
    return basename(str_replace('\\', '/', $class));
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

function back(): void {
    $referer = $_SERVER['HTTP_REFERER'] ?? '/';
    redirect($referer);
}

function flash (string $key, mixed $value): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $_SESSION['_flash'][$key] = $value;
}

function asset(string $path): string {
    // Public path
    $public_path = '/' . ltrim($path, '/');

    // Absolute server path to file
    $file_path = dirname(__DIR__, 2) . '/public/' . ltrim($path, '/');

    // Version via filemtime if file exists
    if (file_exists($file_path)) {
        $version = filemtime($file_path);
        return $public_path . '?v=' . $version;
    }

    // Fallback if file doesn't exists
    return $public_path;
}