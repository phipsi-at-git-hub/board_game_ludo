<?php
// src/Core/Application/Container.php

namespace App\Core\Application;

use ReflectionClass;
use ReflectionException;
use RuntimeException;

final class Container {
    /**
     * Registered bindings
     */
    private array $bindings = [];

    /**
     * Created singleton instances
     */
    private array $instances = [];

    /**
     * Register a factory binding
     *
     * Example:
     * $container->bind(
     *     SystemService::class,
     *     fn(Container $container) => new SystemService()
     * );
     */
    public function bind(string $abstract, callable $factory): void {
        $this->bindings[$abstract] = [
            'factory' => $factory,
            'singleton' => false
        ];
    }

    /**
     * Register a singleton binding
     *
     * Singleton means:
     * One instance during this request lifecycle.
     */
    public function singleton(string $abstract, callable $factory): void {
        $this->bindings[$abstract] = [
            'factory' => $factory,
            'singleton' => true
        ];
    }

    /**
     * Register an already created instance
     */
    public function instance(string $abstract, mixed $instance): void {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Resolve a dependency
     */
    public function get(string $abstract): mixed {
        /*
         * Return existing singleton instance
         */
        if (array_key_exists($abstract, $this->instances)) {
            return $this->instances[$abstract];
        }

        /*
         * Resolve registered binding
         */
        if (array_key_exists($abstract, $this->bindings)) {
            $binding = $this->bindings[$abstract];
            $object = ($binding['factory'])($this);
            if ($binding['singleton']) {
                $this->instances[$abstract] = $object;
            }
            return $object;
        }

        /*
         * Optional:
         * Automatically resolve simple classes
         */
        if (class_exists($abstract)) {
            return $this->resolve($abstract);
        }
        throw new RuntimeException("No binding found for: {$abstract}");
    }

    /**
     * Check whether a binding exists
     */
    public function has(string $abstract): bool {
        return array_key_exists($abstract, $this->bindings) || array_key_exists($abstract, $this->instances);
    }

    /**
     * Remove a singleton instance
     *
     * Useful for special cases,
     * but should rarely be required.
     */
    public function forget(string $abstract): void {
        unset($this->instances[$abstract]);
    }

    /**
     * Create class automatically using reflection
     *
     * This allows simple classes without manual binding.
     */
    private function resolve(string $class): mixed {
        try {
            $reflection = new ReflectionClass($class);
        } catch (ReflectionException $e) {
            throw new RuntimeException("Cannot resolve {$class}", 0, $e);
        }

        if (!$reflection->isInstantiable()) {
            throw new RuntimeException("Class {$class} is not instantiable");
        }

        $constructor = $reflection->getConstructor();
        if ($constructor === null) {
            return new $class();
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type === null || $type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                    continue;
                }
                throw new RuntimeException("Cannot resolve parameter {$parameter->getName()} of {$class}");
            }
            $dependencies[] = $this->get(
                $type->getName()
            );
        }
        return $reflection->newInstanceArgs($dependencies);
    }

    /**
     * Remove all stored singleton instances
     *
     * Mainly useful for testing.
     */
    public function clear(): void {
        $this->instances = [];
    }
}
