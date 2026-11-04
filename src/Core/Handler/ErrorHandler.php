<?php
// src/Core/Handler/ErrorHandler.php

namespace App\Core\Handler;

use App\Core\Config\Env;
use App\Core\Http\Response;
use App\Core\Logging\Logger;
use ErrorException;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class ErrorHandler
{
    /**
     * Output formats
     */
    private const FORMAT_HTML = 'html'; 
    private const FORMAT_JSON = 'json'; 
    private const FORMAT_CLI = 'cli'; 

    /**
     * Generic production error message 
     */
    private const PRODUCTION_MESSAGE = 'An unexpected error occurred.'; 

    /**
     * Current output format
     */
    private static string $outputFormat = self::FORMAT_HTML; 

    /**
     * Prevent recursive error handling
     */
    private static bool $handling = false; 

    /**
     * Prevent duplicate shutdown handling
     */
    private static bool $handled = false; 

    /**
     * register
     * 
     * Register PHP error handling 
     *
     * @return void
     */
    public static function register(): void 
    {
        // Handle reported PHP errors 
        set_error_handler(
            static function (
                int $severity, 
                string $message, 
                string $file, 
                int $line 
            ): bool {
                // Respect the current error reporting level 
                if (!(error_reporting() && $severity)) {
                    return false; 
                }

                throw new ErrorException(
                    $message, 
                    0, 
                    $severity, 
                    $file, 
                    $line
                ); 
            }
        ); 

        // Handle uncaught exceptions and errors 
        set_exception_handler(
            static function (Throwable $exception): void {
                self::handleException($exception); 
            }
        ); 

        // Handle fatal errors during shutdown 
        register_shutdown_function(
            static function (): void {
                self::handleShutdown(); 
            }
        ); 
    }

    /**
     * setOutputFormat
     * 
     * Set out format
     * This only configures the output context. 
     * It does not trigger error handling. 
     *
     * @param string $format
     * @return void
     */
    public static function setOutputFormat(string $format): void 
    {
        if (!in_array($format, [
            self::FORMAT_HTML, 
            self::FORMAT_JSON, 
            self::FORMAT_CLI 
        ], true)) {
            throw new InvalidArgumentException(
                "Unsupported error output format: {$format}"
            ); 
        }

        self::$outputFormat = $format; 
    }

    /**
     * handleException
     * 
     * Handle uncaught exceptions and errors 
     *
     * @param Throwable $exception
     * @return void
     */
    private static function handleException(Throwable $exception): void 
    {
        if (self::$handling) {
            error_log(
                'ErrorHandler: Recursive error handling: ' . $exception->getMessage()
            ); 
            exit(1); 
        }

        self::$handling = true; 
        self::$handled = true; 

        self::log($exception); 
        self::render($exception); 

        exit(1); 
    }

    /**
     * handleShutdown
     * 
     * Handle fatal errors during shutdown 
     *
     * @return void
     */
    private static function handleShutdown(): void 
    {
        if (self::$handled) {
            return; 
        }

        $error = error_get_last(); 
        if ($error === null) {
            return; 
        }

        $fatalErrors = [
            E_ERROR, 
            E_PARSE, 
            E_CORE_ERROR, 
            E_COMPILE_ERROR, 
            E_USER_ERROR, 
            E_RECOVERABLE_ERROR
        ]; 

        if (!in_array($error['type'], $fatalErrors, true)) {
            return; 
        }

        $exception = new ErrorException(
            $error['message'], 
            0, 
            $error['type'], 
            $error['file'], 
            $error['line']
        ); 

        self::$handling = true; 
        self::$handled = true; 

        self::log($exception); 
        self::render($exception); 
    }

    /**
     * log
     * 
     * Log handled exception
     *
     * @param Throwable $exception
     * @return void
     */
    private static function log(Throwable $exception): void 
    {
        $message = sprintf(
            '%s: %s', 
            get_class($exception), 
            $exception->getMessage()
        ); 

        $context = [
            'exception' => get_class($exception), 
            'file' => $exception->getFile(), 
            'line' => $exception->getLine(), 
            'trace' => $exception->getTraceAsString()
        ]; 

        try {
            Logger::app()->critical($message, $context); 
        } catch (Throwable $loggingException) {
            // Fallback if the logger itself fails 
            error_log('ErrorHandler: Logging failed.'); 
            error_log(
                $message
                . ' in ' 
                . $exception->getFile() 
                . ':' 
                . $exception->getLine()
            ); 
            error_log('Logger exception: ' . $loggingException->getMessage()); 
        }
    }

    /**
     * render
     * 
     * Render error response
     *
     * @param Throwable $exception
     * @return void
     */
    private static function render(Throwable $exception): void 
    {
        try {
            $isDev = Env::isDev(); 

            // CLI always takes precedence over HTTP output formats
            if (PHP_SAPI === 'cli') {
                self::renderCli($exception, $isDev); 
                return; 
            }

            http_response_code(500); 

            switch (self::$outputFormat) {
                case self::FORMAT_JSON: 
                    self::renderJson($exception, $isDev); 
                    break; 
                case self::FORMAT_HTML: 
                default: 
                    self::renderHTML($exception, $isDev); 
                    break; 
            }
        } catch (Throwable $renderException) {
            // Never allow rendering errors to escape the ErrorHandler 
            error_log('ErrorHandler: Rendering failed: ' . $renderException->getMessage()); 
            self::renderFallback($exception); 
        }
    }

    /**
     * renderJson
     * 
     * Render JSON response 
     *
     * @param Throwable $exception
     * @param bool $isDev
     * @return void
     */
    private static function renderJson(
        Throwable $exception, 
        bool $isDev
    ): void {
        $message = $isDev ? $exception->getMessage() : self::PRODUCTION_MESSAGE; 
        $errors = []; 

        if ($isDev) {
            $errors = [
                'exception' => get_class($exception), 
                'file' => $exception->getFile(), 
                'line' => $exception->getLine(), 
                'trace' => $exception->getTraceAsString() 
            ]; 
        }

        try {
            $response = Response::error($message, $errors); 
            $json = json_encode(
                $response, 
                JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES 
                    | JSON_INVALID_UTF8_SUBSTITUTE 
            ); 

            if ($json === false) {
                throw new RuntimeException('Failed to encode error response.'); 
            }

            if (!headers_sent()) {
                header('Content-Type: application/json; charset=utf-8'); 
            }

            echo $json; 
        } catch (Throwable $renderException) {
            error_log('ErrorHandler: JSON rendering failed: ' . $renderException->getMessage()); 

            if (!headers_sent()) {
                header('Content-Type: application/json; charset=utf-8');
            }

            echo '{"success":false,"type":"error",' 
                . '"message":"An unexpected error occurred.",' 
                . '"data":[],"errors":[],"views":[]}'; 
        }
    }

    /**
     * renderHtml
     * 
     * Render HTML response 
     *
     * @param Throwable $exception
     * @param bool $isDev
     * @return void
     */
    private static function renderHtml(
        Throwable $exception, 
        bool $isDev 
    ): void {
        if (!headers_sent()) {
            header('Content-Type: text/html; charset=utf-8'); 
        }

        if (!$isDev) {
            echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Error</title>
</head>
<body>
    <h1>Application Error</h1>
    <p>' . self::PRODUCTION_MESSAGE . '</p>
</body>
</html>'; 
            return;
        }

        $message = htmlspecialchars(
            $exception->getMessage(), 
            ENT_QUOTES | ENT_SUBSTITUTE, 
            'UTF-8'
        ); 

        $class = htmlspecialchars(
            get_class($exception), 
            ENT_QUOTES | ENT_SUBSTITUTE, 
            'UTF-8'
        ); 

        $file = htmlspecialchars(
            $exception->getFile(), 
            ENT_QUOTES | ENT_SUBSTITUTE, 
            'UTF-8'
        ); 

        $line = $exception->getLine(); 

        $trace = htmlspecialchars(
            $exception->getTraceAsString(), 
            ENT_QUOTES | ENT_SUBSTITUTE, 
            'UTF-8'
        ); 

        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Error</title>
    <style>
        body {
            font-family: sans-serif; 
            margin: 40px; 
            color: #333; 
            background: #f5f5f5; 
        }
        main {
            max-width: 1000px; 
            margin: auto; 
            padding: 30px; 
            background: #fff; 
            border-radius: 8px; 
        }
        pre {
            padding: 20px; 
            overflow-x: auto; 
            background: #eee; 
            white-space: pre-wrap; 
            overflow-wrap: anywhere; 
        }
    </style>
</head>
<body>
<main>
    <h1>Application Error</h1>
    <h2>' . $class . '</h2>
    <p>' . $message . '</p>
    <p><strong>File:</strong> ' . $file . '</p>
    <p><string>Line:</string> ' . $line . '</p>
    <h3>Stack Trace</h3>
    <pre>' . $trace . '</pre>
</main>
</body>
</html>'; 
    }

    /**
     * renderCli
     * 
     * Render CLI error response 
     *
     * @param Throwable $exception
     * @param bool $isDev
     * @return void
     */
    private static function renderCli(
        Throwable $exception, 
        bool $isDev 
    ): void {
        $message = self::PRODUCTION_MESSAGE; 

        if ($isDev) {
            $message = sprintf(
                "%s: %s\n\nFile: %s\nLine: %d\n\nStack Trace:\n%s", 
                get_class($exception), 
                $exception->getMessage(), 
                $exception->getFile(), 
                $exception->getLine(), 
                $exception->getTraceAsString()
            ); 
        }

        $output = "\nApplication Error\n" . $message . "\n\n"; 

        if (defined('STDERR')) {
            fwrite(STDERR, $output); 
        } else {
            error_log($output); 
        }
    }

    private static function renderFallback(Throwable $exception): void 
    {
        try {
            $isCli = PHP_SAPI === 'cli'; 
            $isDev = Env::isDev(); 

            $message = $isDev 
                ? get_class($exception) . ': ' 
                    . $exception->getMessage() 
                    . "\n\nFile: " . $exception->getFile() 
                    . "\nLine: " . $exception->getLine() 
                    . "\n\nStack Trace:\n" 
                    . $exception->getTraceAsString() 
                : self::PRODUCTION_MESSAGE; 
            
            if ($isCli) {
                $output = "\nApplication Error\n" . $message . "\n\n"; 

                if (defined('STDERR')) {
                    fwrite(STDERR, $output); 
                } else {
                    error_log($output); 
                }
                return; 
            }

            if (!headers_sent()) {
                http_response_code(500); 
                header('Content-Type: text/plain; charset=utf-8'); 
            }

            echo $message; 
        } catch (Throwable $fallbackException) {
            // Last possible fallback
            error_log('ErrorHandler: Fallback rendering failed: ' . $fallbackException->getMessage()); 
            error_log(self::PRODUCTION_MESSAGE); 
        }
    }
}
