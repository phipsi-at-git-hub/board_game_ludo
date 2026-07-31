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
        $newest_original = 0;
        $oldest_busted = null; 
        foreach ($directories as $dir) {
            if (!is_dir($dir))  {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(( new \RecursiveDirectoryIterator($dir)));

            foreach ($iterator as $file) {
                // Skip already busted files
                if (preg_match('/\.\d+\./', $file->getPathname())) {
                    // save busted asset file age
                    $mtime = filemtime($file->getPathname());
                    if ($oldest_busted === null || $mtime < $oldest_busted) {
                        $oldest_busted = $mtime; 
                    }
                    continue;
                }

                if ($file->isFile()) {
                    $files[] = $file->getPathname();

                    // save updated time original asset file
                    $mtime = $file->getMTime();
                    if ($mtime > $newest_original) {
                        $newest_original = $mtime;
                    }
                }
            }
        }

        // 1.1 Early exit if no newer original then busted asset
        if ($oldest_busted !== null && $newest_original < $oldest_busted) {
            return; 
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
            $busted_full   = PUBLIC_PATH . '/' . $busted;

            // Copy only if original has changed
            if (file_exists($busted_full) && filemtime($busted_full) >= filemtime($original_full)) {
                continue;
            }

            $content = file_get_contents($original_full);

            // Replace references
            // Sort map by path length desc to avoid partial replacements
            uksort($map, fn($a, $b) => strlen($b) - strlen($a));

            foreach ($map as $search => $replace) {
                $searchDir    = dirname($search);
                $replaceDir   = dirname($replace);
                $searchBase   = basename($search);
                $replaceBase  = basename($replace);

                // 4.1 Absolute references
                $content = str_replace($search, $replace, $content);

                // 4.2 Relative references "./file.js"
                $content = str_replace('./' . $searchBase, './' . $replaceBase, $content);

                // 4.3 Only basename
                $content = str_replace($searchBase, $replaceBase, $content);

                // 4.4 CSS url(...)
                $content = preg_replace_callback(
                    '#url\((.*?)\)#',
                    function ($matches) use ($search, $replace, $searchDir, $searchBase, $replaceDir, $replaceBase) {
                        $url = trim($matches[1], '"\' ');

                        if ($url === $search || $url === $searchDir . '/' . $searchBase || basename($url) === $searchBase) {
                            return 'url(' . $replace . ')';
                        }
                        return $matches[0];
                    },
                    $content
                );
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

    // Asset - Get asset-url in css
    public static function css(string $path, bool $use_manifest = false): string {
        return self::asset(CSS_PATH . '/' . $path, $use_manifest); 
    }

    // Asset - Get asset-url in img
    public static function img(string $path, bool $use_manifest = false): string {
        return self::asset(IMG_PATH . '/' . $path, $use_manifest); 
    }

    // Asset - Get asset-url in js
    public static function js(string $path, bool $use_manifest = false): string {
        return self::asset(JS_PATH . '/' . $path, $use_manifest); 
    }
}
