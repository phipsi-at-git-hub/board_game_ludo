<?php
// src/Core/Application/App.php

namespace App\Core\Application;

use App\Constants\Application;
use App\Core\Asset;
use App\Core\Debug;
use App\Core\Env;
use App\Core\Localization;
use App\Services\GameService;
use App\Services\SystemService;
use Dotenv\Dotenv;

final class App {
    // Application itself
    private static ?App $instance = null; 

    // Application container 
    private Container $container;

    // Application boot state 
    private bool $booted = false;

    // Application start timestamp 
    private float $startedAt;

    // Constructor 
    public function __construct() {
        self::$instance = $this; 

        $this->startedAt = microtime(true);
        $this->container = new Container();
        $this->registerCoreBindings();
    }

    // Bootstrap application lifecycle 
    public function boot(): void {
        if ($this->booted) {
            return;
        }
        $this->loadEnvironment();
        $this->configureErrorHandling();
        $this->loadHelpers();
        $this->loadLocalization();
        $this->registerServices();
        $this->booted = true;
    }

    // Allow global access
    public static function instance(): App|null {
        if (self::$instance === null) {
            return null; 
        }
        return self::$instance; 
    }

    // Get application container 
    public function container(): Container {
        return $this->container;
    }

    // Resolve application container 
    public function resolve(string $class): mixed {
        return $this->container->get($class); 
    }

    // Check whether application is booted 
    public function isBooted(): bool {
        return $this->booted;
    }

    // Get application runtime duration 
    public function getRuntime(): float {
        return microtime(true) - $this->startedAt;
    }

    // Register framework level bindings - Infrastructure objects belong here. 
    private function registerCoreBindings(): void {
        $this->container->instance(self::class, $this);
        $this->container->instance(Container::class, $this->container);
    }

    // Register application services - These are request lifetime singletons. 
    private function registerServices(): void {
        $this->container->singleton(
            SystemService::class,
            function(Container $container) {
                return new SystemService();
            }
        );

        $this->container->singleton(
            GameService::class,
            function(Container $container) {
                return new GameService();
            }
        );
    }

    // Load environment configuration 
    private function loadEnvironment(): void {
        $dotenv = Dotenv::createImmutable(BASE_PATH);
        $dotenv->load();
        Env::get();
    }

    // Configure PHP runtime 
    private function configureErrorHandling(): void {
        if (Env::isDev()) {
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
            error_reporting(E_ALL);

            Asset::buildAssets();
            Debug::start();
        } else {
            ini_set('display_errors', '0');
            error_reporting(0);
        }
    }

    // Load helper functions 
    private function loadHelpers(): void {
        require_once BASE_PATH . '/src/Core/helpers.php';
    }

    // Initialize localization 
    private function loadLocalization(): void {
        Localization::load(TRANSLATIONS_PATH, Application::EN_US);
    }
}
