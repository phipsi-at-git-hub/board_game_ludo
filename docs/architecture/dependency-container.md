# Dependency Container

## Introduction 

The application uses a dependency container to manage object creation and dependencies during a single HTTP request. 

The container is part of the application context: 

````
App

 |

 v

Container

 |

 +----------------------+
 |                      |
 v                      v

Services             Dependencies
````

The container is not a global service locator for arbitrary application state. 

Its purpose is: 

- managing object creation 
- controlling object lifetime 
- providing shared request dependencies 
- avoiding manual dependency creation 

--- 

# Container Location 

The container implementation is located at: 

````
src/Core/Application/Container.php
````

The class belongs to the application infrastructure layer. 

It is created by: 

````
src/Core/Application/App.php 
````

--- 

# Container Lifecycle

A new container is created for every request. 

Example: 

````
Request 1

App
 |
 Container
 |
 SystemService


Request 2

App
 |
 Container
 |
 SystemService
````

No instances are shared between requests. 

This prevents: 

- stale application state 
- unintended cross-request data 
- hidden global dependencies 

--- 

# Container Responsibilities 

The container is responsible for: 

## Registration 

Objects can be registered explicitly. 

Examples: 

- services 
- infrastructure classes 
- application components 

--- 

## Resolution

The container can create or retrieve objects. 

Example: 

```php
$systemService = $container->get(SystemService::class); 
```

--- 

## Lifetime Management 

The container controls how long an objects exists. 

Supported lifetimes: 

- transient objects 
- request singletons 
- predefined instances 

--- 

# Binding Types 

The container supports three main registration types. 

--- 

# Factory Binding 

A normal binding creates a new instance whenever requested. 

Example: 

```php
$container->bind(
    ExampleService::class,
    function(Container $container) {
        return new ExampleService();
    }
);
``` 

Usage: 

```php
$service = $container->get(ExampleService::class); 
```

Each call creates a new object. 

--- 

# Singleton Binding 

A singleton exists once during the request lifecycle. 

Example: 

```php
$container->singleton(
    SystemService::class,
    function(Container $container) {
        return new SystemService();
    }
);
```

Behavior: 

````
First request:

get(SystemService)

        |
        v

create instance


Second request:

get(SystemService)

        |
        v

return existing instance
````

Important: 

This is a request singleton, not a system-wide singleton. 

--- 

# Instance Registration 

Existing objects can be registered directly. 

Example: 

```php
$container->instance(
    Logger::class,
    $logger
);
```

The container returns this exact instance. 

--- 

# Automatic Resolution 

The container supports automatic dependency resolution. 

Example: 

```php
class ExampleService
{
    public function __construct(
        Database $database
    ) {
        $this->database = $database;
    }
}
```

The container can resolve: 

````
ExampleService

 |

 v

Database
````

using reflection. 

--- 

# Application Services 

Application services are registered in: 

````
src/Core/Application/App.php
````

Example: 

```php
private function registerServices(): void
{
    $this->container->singleton(
        SystemService::class,
        function(Container $container) {
            return new SystemService();
        }
    );
}
```

--- 

# SystemService Example 

The system configuration is request-wide. 

The intended flow: 

````
Controller
Service
Policy
Health Check
Middleware

        |

        v

Application Context

        |

        v

Container

        |

        v

SystemService
````

Every component accesses the same request instance. 

--- 

# Accessing Dependencies

The application instance provides access: 

```php
$app->resolve(SystemService::class); 
```

or: 

```php
$app->getContainer()->get(SystemService::class); 
```

The preferred abstraction is the application context. 

Application code should not manually instantiate container objects. 

--- 

# Container Design Rules

## Prefer explicit registration 

Important application services should be registered explicitly. 

Example: 

Good:

```php
$container->singleton(
    SystemService::class,
    fn() => new SystemService()
);
```

Avoid relying only on automatic reflection resolution for important services. 

--- 

## Avoid unnecessary singletons

A singleton should represent: 

- request-wide state 
- expensive initialization 
- shared infrastructure 

Examples: 

Good candidates: 

- SystemService 
- Configuration service 
- Logger

Poor candidates: 

- temporary calculation objects 
- DTO objects 
- database result objects 

--- 

# Testing

The container supports replacing dependencies. 

Example: 

```php
$container->instance(
    SystemService::class,
    $mockSystemService
);
```

This allows: 

- isolated tests
- mocked dependencies 
- controlled environments 

--- 

# Summary

The dependency container provides: 

- centralized dependency management 
- predictable object lifetime 
- request scoped services 
- reduced coupling 
- extensible application infrastructure 

The container is a core part of the application architecture and should be used consistently throughout the backend. 
