<?php
// src/Core/Persistence/FileSystem.php

namespace App\Core\Persistence;

final class FileSystem {
    private static ?FileSystem $instance = null;

    private function __construct() {}

    public static function getInstance(): self {
        return self::$instance ??= new self();
    }

    /**
     * Write file
     */
    public function write(string $path, string $content): bool {
        $this->ensureDirectoryExists(dirname($path));
        return file_put_contents(
            $path,
            $content,
            LOCK_EX
        ) !== false;
    }

    /**
     * Append file
     */
    public function append(string $path, string $content): bool {
        $this->ensureDirectoryExists(dirname($path));
        return file_put_contents(
            $path,
            $content,
            FILE_APPEND | LOCK_EX
        ) !== false;
    }

    /**
     * Append file with size based rotation
     *
     * Creates:
     * file.log
     * file-1.log
     * file-2.log
     */
    public function appendRotated(string $path, string $content, int $maxSize): bool {
        $target = $this->getCurrentRotatedFilename($path);
        if (!$this->exists($target) || ($this->size($target) + strlen($content)) <= $maxSize) {
            return $this->append($target, $content);
        }

        return $this->append(
            $this->getNextRotatedFilename($target),
            $content
        );
    }

    /**
     * Read complete file
     */
    public function read(string $path): ?string {
        if (!$this->exists($path)) {
            return null;
        }
        return file_get_contents($path);
    }

    /**
     * Read file lines
     *
     * Reads only requested range.
     */
    public function readLines(string $path, int $start = 0, ?int $limit = null): array {
        if (!$this->exists($path)) {
            return [];
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return [];
        }

        $lines = [];
        $current = 0;

        while (($line = fgets($handle)) !== false) {
            if ($current >= $start) {
                $lines[] = rtrim($line, "\r\n");
                if ($limit !== null && count($lines) >= $limit) {
                    break;
                }
            }
            $current++;
        }
        fclose($handle);
        return $lines;
    }

    /**
     * Read lines from multiple files
     *
     * Reads files sequentially in given order.
     */
    public function readLinesFromFiles(array $files, int $start = 0, ?int $limit = null): array {
        $result = [];
        $offset = $start;

        foreach ($files as $file) {
            if (!$this->exists($file)) {
                continue;
            }

            $linesInFile = $this->countLines($file);

            if ($offset >= $linesInFile) {
                $offset -= $linesInFile;
                continue;
            }

            $remaining = $limit === null
                ? null
                : $limit - count($result);

            $result = array_merge(
                $result,
                $this->readLines(
                    $file,
                    $offset,
                    $remaining
                )
            );

            if ($limit !== null && count($result) >= $limit) {
                break;
            }
            $offset = 0;
        }
        return $result;
    }

    /**
     * Count file lines
     */
    public function countLines(string $path): int {
        if (!$this->exists($path)) {
            return 0;
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return 0;
        }

        $count = 0;

        while (fgets($handle) !== false) {
            $count++;
        }
        fclose($handle);
        return $count;
    }

    /**
     * File exists
     */
    public function exists(string $path): bool {
        return file_exists($path);
    }

    /**
     * Delete file
     */
    public function delete(string $path): bool {
        if (!$this->exists($path)) {
            return false;
        }
        return unlink($path);
    }

    /**
     * List files
     */
    public function listFiles(string $directory, string $pattern = '*'): array {
        if (!is_dir($directory)) {
            return [];
        }

        $files = glob(rtrim($directory, '/') . '/' . $pattern);
        return $files ?: [];
    } 

    /**
     * List only base files of all matching files
     * 
     * Removes rotated duplicates: 
     * file.text
     * file-1.txt
     * file-2.txt
     * 
     * return:
     * file.txt 
     */
    public function listBaseFiles(string $directory, string $pattern = '*'): array { 
        $files = $this->listFiles($directory, $pattern); 
        $baseFiles = []; 

        forEach ($files as $file) {
            $extension = pathinfo($file, PATHINFO_EXTENSION); 
            $basename = pathinfo($file, PATHINFO_FILENAME); 
            $basename = preg_replace('/-\d+$/', '', $basename); 
            $baseFile = dirname($file). '/' . $basename . ($extension ? '.' . $extension : ''); 
            $baseFiles[$baseFile] = $baseFile; 
        }
        natsort($baseFiles); 
        return array_values($baseFiles); 
    }

    /**
     * File size
     */
    public function size(string $path): ?int {
        if (!$this->exists($path)) {
            return null;
        }
        return filesize($path);
    }

    /**
     * Last modification timestamp
     */
    public function lastModified(string $path): ?int {
        if (!$this->exists($path)) {
            return null;
        }
        return filemtime($path);
    }

    /**
     * Returns currently active rotated filename
     *
     * Example:
     * log.txt
     * log-1.txt
     * log-2.txt
     *
     * Returns the last existing file.
     */
    private function getCurrentRotatedFilename(string $path): string {
        $files = $this->getRelatedRotatedFilenames($path);
        if (empty($files)) {
            return $path;
        }
        return end($files);
    }

    /**
     * Returns next rotated filename
     */
    private function getNextRotatedFilename(string $path): string {
        $directory = dirname($path);
        $filename = basename($path);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $basename = pathinfo($filename, PATHINFO_FILENAME);

        $counter = 1;

        do {
            $newFilename = $basename . '-' . $counter;
            if ($extension) {
                $newFilename .= '.' . $extension;
            }

            $newPath = $directory . '/' . $newFilename;
            $counter++;
        } while ($this->exists($newPath));
        return $newPath;
    }

    /**
     * Return all related rotated files
     *
     * Example:
     * log.txt
     * log-1.txt
     * log-2.txt
     */
    public function getRelatedRotatedFilenames(string $path): array {
        if (!$this->exists($path)) {
            return [];
        }

        $directory = dirname($path);
        $filename = basename($path);
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $pattern = $basename . '-*';

        if ($extension) {
            $pattern .= '.' . $extension;
        }

        $rotated = $this->listFiles($directory, $pattern);

        array_unshift($rotated, $path);
        natsort($rotated);
        return array_values($rotated);
    }

    /**
     * Ensure directory exists
     */
    private function ensureDirectoryExists(string $directory): void {
        if (!is_dir($directory)) {
            mkdir(
                $directory,
                0755,
                true
            );
        }
    }
}
