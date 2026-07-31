<?php
// src/Core/Persistence/FileSystem.php

namespace App\Core\Persistence;

final class FileSystem {
    private static ?FileSystem $instance = null;

    private function __construct() {}

    public static function getInstance(): self {
        return self::$instance ??= new self();
    }

    // Creates / Writes given content in given file 
    public function write(
        string $path,
        string $content
    ): bool {
        $this->ensureDirectoryExists(
            dirname($path)
        );

        return file_put_contents(
            $path,
            $content,
            LOCK_EX
        ) !== false;
    }

    // Adds given content in given existing file
    public function append(
        string $path,
        string $content
    ): bool {
        $this->ensureDirectoryExists(
            dirname($path)
        );

        return file_put_contents(
            $path,
            $content,
            FILE_APPEND | LOCK_EX
        ) !== false;
    }

    // Return content of given file
    public function read(string $path): ?string {
        if (!file_exists($path)) {
            return null;
        }
        return file_get_contents($path);
    }

    // Checks if given file exists
    public function exists(string $path): bool {
        return file_exists($path);
    }

    // Delete given file
    public function delete(string $path): bool {
        if (!file_exists($path)) {
            return false;
        }
        return unlink($path);
    }

    // Ensures the given directory exists
    private function ensureDirectoryExists(
        string $directory
    ): void {
        if (!is_dir($directory)) {
            mkdir(
                $directory,
                0755,
                true
            );
        }
    }
}
