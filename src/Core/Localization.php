<?php
// Localization.php
namespace App\Core;

class Localization {
    private static array $translations = [];
    private static string $locale = 'en-us';

    public static function load(string $translationsPath, string $locale): void {
        self::$locale = $locale;
        $file = $translationsPath . '/' . $locale . '/messages.php';
        if (file_exists($file)) {
            self::$translations = require $file;
        }
    }

    public static function get(string $key, array $replace = []): string {
        $text = self::$translations[$key] ?? $key;
        foreach ($replace as $k => $v) {
            $text = str_replace("{{$k}}", $v, $text);
        }
        return $text;
    }
}
