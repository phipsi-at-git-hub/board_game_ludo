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