<?php
// src/Core/Asset.php
namespace App\Core;

class Asset {
    private const MANIFEST_FILENAME = 'asset-manifest.json';
    private const MANIFEST_FULL_PATH = PUBLIC_PATH . '/' . self::MANIFEST_FILENAME;
    protected static string $build_dir = __DIR__ . '/../..public'; 

    // Asset - Bust all asset files
    public static function buildAssets(bool $create_manifest = false): void {
        if (!Env::isDev()) {
            return;
        }

        $directories = [
            PUBLIC_PATH . '/js', 
            PUBLIC_PATH . '/css', 
            PUBLIC_PATH . '/images', 
        ];

        // 1. Look for files in directories
        $files = []; 
        foreach ($directories as $dir) {
            if (!is_dir($dir))  {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(( new \RecursiveDirectoryIterator($dir)));

            foreach ($iterator as $file) {
                // Skip already busted files
                if (preg_match('/\.\d+\./', $file->getPathname())) {
                    continue;
                }

                if ($file->isFile()) {
                    $files[] = $file->getPathname();
                }
            }
        }

        // 2. Create mapping (original to busted)
        $map = [];
        foreach ($files as $file_path) {
            $relative_path = str_replace(PUBLIC_PATH . '/', '', $file_path); 
            $info = pathinfo($file_path);
            $hash = filemtime($file_path); 
            $busted_name = $info['filename'] . '.' . $hash . '.' . $info['extension']; 
            $busted_path = $info['dirname'] . '/' . $busted_name; 
            $map[$relative_path] = str_replace(PUBLIC_PATH . '/', '', $busted_path);
        }

        // 3. Delete all old busted versions
        foreach ($map as $original => $busted) {
            self::cleanupOldBustedVersions($original);
        }

        // 4. Copy files and replace references
        foreach ($map as $original => $busted) {
            $original_full = PUBLIC_PATH . '/' . $original;
            $busted_full = PUBLIC_PATH . '/' . $busted;
            $content = file_get_contents($original_full);

            // Set references
            foreach ($map as $search => $replace) {
                // 4.1 Absolute path
                $content = str_replace($search, $replace, $content);

                // 4.2 Relative path
                $basename = basename($search);
                $busted_basename = basename($replace);

                $content = str_replace('./' . $basename, './' . $busted_basename, $content); 
                $content = str_replace($basename, $busted_basename, $content); 
            }
            file_put_contents($busted_full, $content);
        }

        // 5. Optional - Manifest
        if ($create_manifest) {
            file_put_contents(
                //PUBLIC_PATH . '/asset-manifest.json', 
                self::MANIFEST_FULL_PATH, 
                json_encode($map, JSON_PRETTY_PRINT)
            );
        }
    }

    // Asset - Get asset-url
    public static function asset(string $path, bool $use_manifest = false): string {
        $path = ltrim($path, '/'); 

        // 1. Use manifest (optional)
        if ($use_manifest) {
            //$manifest_path = PUBLIC_PATH . '/asset-manifest.json';
            $manifest_path = self::MANIFEST_FULL_PATH;

            if (file_exists($manifest_path)) {
                $manifest = json_decode(file_get_contents($manifest_path), true); 

                if (isset($manifest[$path])) {
                    return '/' . $manifest[$path];
                }
            }
        }

        // 2. Look out for busted files without a manifest
        $full_path = PUBLIC_PATH . '/' . $path; 
        $info = pathinfo($full_path); 
        $pattern = $info['dirname'] . '/' . $info['filename'] . '.[0-9]*.' . $info['extension'];
        $matches = glob($pattern);

        if ($matches && count($matches) > 0) {
            return '/' . str_replace(PUBLIC_PATH . '/', '', $matches[0]);
        }

        // 3. Fallback use original
        return '/' . $path;
    }

    // Delete old busted version of assets
    private static function cleanupOldBustedVersions(string $relative_path): void {
        $full_path = PUBLIC_PATH . '/' . $relative_path;
        $info = pathinfo($full_path);
        $pattern = $info['dirname'] . '/' . $info['filename'] . '.[0-9]*.' . $info['extension'];
        $files = glob($pattern);

        foreach ($files as $file) {
            unlink($file);
        }
    }
}