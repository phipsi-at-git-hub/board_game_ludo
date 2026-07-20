# Application Lifecycle

## Introduction

This document describes the lifecycle of a request inside the application. 

The application uses a request-based application context consisting of: 

- App
- Container 
- registered services 

The main principle is: 

```
Every HTTP request creates exactly one application instance and exactly one container. 
```

---

# Request Lifecycle Overview

The lifecycle start ins: 

````
public/index.php
````

The general flow: 

````
HTTP Request

    |
    v

index.php

    |
    v

bootstrap/app.php

    |
    v

App created

    |
    v

Container created

    |
    v

Application boot

    |
    v

Services registered

    |
    v

Router

    |
    v

Controller

    |
    v

Response
````

---

# Entry Point

The application entry point is: 

````
public/index.php
````

Responsibilities: 

- initialize security/session handling 
- load application paths 
- load Composer autoload 
- create application instance 
- dispatch router 
- render debug information 

Example: 

```php
$app = require BASE_PATH . '/bootstrap/app.php';

$router = require BASE_PATH . '/bootstrap/routes.php';

$router->dispatch($app);
```

---

# Bootstrap

Location: 

````
bootstrap/app.php
````

The bootstrap file creates the application. 

Example: 

```php
use App\Core\Application\App;

$app = new App();

$app->boot();

return $app;
```

The bootstrap layer should remain small. 

It starts the application lifecycle but does not contain application logic. 

---

# Application Class

Location: 

````
src/Core/Application/App.php
````

The App class acts as the application kernel. 

Responsibilities: 

- create the container 
- register core bindings 
- register application services 
- initialize environment 
- configure runtime 
- load helpers 
- initialize localization 

---

# Application Instance 

Each request receives exactly one application instance. 

Example: 

````
Request A

App A
 |
 Container A
 |
 SystemService A


Request B

App B
 |
 Container B
 |
 SystemService B
````

No state is shared between requests. 

---

# Container Lifecycle 

The container belongs to the application instance. 

````
App

 |
 v

Container

 |
 +----------------+
 |                |
 v                v

Services     Dependencies
````

The container manages object lifetime during one request. 

--- 

# Service Lifecycle

Services can be registered as request singleton. 

Example: 

````php
$container->singleton(
    SystemService::class,
    fn() => new SystemService()
);
````

Result: 
````
First access:

SystemService created


Second access:

same instance returned
````

--- 

# SystemService Example

The system settings flow: 

````
Request

 |
 v

App

 |
 v

Container

 |
 v

SystemService

 |
 v

Database
````

Within one request: 

- the service exists once 
- state remains consistent 
- repeated access does not recreate the object 

--- 

# Application Context Access

The application context provides access to request-wide dependencies. 

Example: 

````php
$app->resolve(SystemService::class);
````

The allowed usage from: 

- controllers 
- services 
- policies 
- health checks 
- middleware 

---

# Controller Lifecycle 

Controllers are created by the router. 

The router receives the current application instance. 

Example: 

````php 
$controller = new $controllerClass($app); 
````

Controllers therefore belong to the same request context. 

--- 

# Development Rules

## Do 

- register request services in App
- use the container for shared dependencies 
- keep services request-scoped 

## Avoid

Do not create services manually: 

````php
new SystemService(); 
````

Use the application context: 

````php
$app->resolve(SystemService::class); 
````

--- 

# Summary

The application lifecycle guarantees: 

- one application instance per request 
- one container per request 
- controlled service lifetime 
- consistent application state 
- predictable dependency access 

