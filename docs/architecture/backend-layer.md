# Backend Layer Architecture

## Introduction

The backend follows a layered architecture inspired by MVC principles. 

The main goal is separation between: 

- HTTP handling
- business logic 
- persistence 
- application infrastructure 

The typical request flow is: 

````
Request

 |

 v

Router

 |

 v

Controller

 |

 v

Service

 |

 v

Model

 |

 v

Database
````

--- 

# Layer Responsibilities 

Each layer has a defined responsibility. 

A component should only handle tasks belonging to irs own layer. 

--- 

# Router Layer 

Location: 

````
src/Core/Router.php
````

The router is responsible for: 

- matching HTTP requests 
- resolving routes 
- executing middleware 
- creating controllers 

The router does not contain business logic. 

--- 

# Controller Layer 

Location: 

````
src/Controllers/
````

Controllers represent the HTTP interface of the application. 

Responsibilities: 

- receiving requests 
- reading input 
- validating request data
- calling services 
- returning responses 

Example: 

```php
public function join(string $gameId)
{
    $this->gameService->join(
        $game,
        $user
    );
}
```

--- 

# Controllers Rules 

Controllers should: 

## Do

- handle HTTP concerns 
- call services 
- return views or JSON

## Avoid

Controllers should not: 

- contain complex business rules 
- directly perform workflows 
- duplicate service logic 

Bad: 

```php
if ($game->players >= 4) {
    ...
}
```

Better: 

```php
$gameService->join($game, $user); 
```

--- 

# Service Layer 

Location: 

````
src/Services/
````

Services contain application logic. 

Examples: 

````
GameService

SystemService
````

Responsibilities: 

- workflows 
- business rules 
- coordination between models 
- permission decisions 

--- 

# Example Service Flow

A game action: 

```
Controller

 |
 v

GameService

 |
 +------------+
 |            |
 v            v

GameModel   Policy

 |
 v

Database
```

--- 

# Policy Layer 

Policies represent authorization and business permission checks. 

Examples: 

````
GamePolicy

SystemPolicy
````

Policies answer question like: 

- Is this action allowed? 
- Can this user perform this operation? 
- Is the current system state compatible? 

Policies should not execute workflows. 

--- 

# Model Layer

Location: 

````
src/Models/
````

Models represent application entities. 

Responsibilities: 

- database access 
- entity state 
- persistence operations 

Examples: 

````
GameModel

UserModel

GameRuleSetModel
````

--- 

# Models Should Avoid

Models should not: 

- render views 
- handle HTTP requests
- manage sessions 
- decide application workflows 

--- 

# View Layer

Location: 

````
src/Views/
````

Views are responsible for presentation. 

They receive prepared data from controllers. 

Example: 

```php
$this->render(
    'game/detail',
    [
        'game' => $game
    ]
);
```

Views should not: 

- query databases 
- execute business logic 
- modify application state 

--- 

# API Layer

API responses use dedicated JSON endpoints. 

Flow:

````
Frontend

 |

 v

API Controller

 |

 v

Service

 |

 v

Model
````

API controllers should follow the same principles as normal controllers. 

--- 

# Authentication 

Authentication is handled centrally. 

Typical flow: 

````
Middleware

 |

 v

Auth

 |

 v

Controller
````

Controllers can access the authenticated user but should not implement authentication logic themselves. 

--- 

# System Configuration Access

System configuration follows the application context. 

Example: 

````
Controller
Service
Policy
Health Check

        |

        v

App

        |

        v

Container

        |

        v

SystemService
````

This ensures: 

- one consistent state per request 
- no repeated service creation 
- centralizes configuration access

--- 

# Backend Development Principles 

## Thin Controllers

Controllers coordinate. 

They do not decide. 

--- 

## Business Logic in Services

All application decisions should have one central location. 

--- 

## Model Represent Data

Models should remain focused on persistence. 

--- 

# Summary 

The backend architecture provides: 

- clear responsibility boundaries 
- maintainable business logic 
- reusable services 
- predictable request handling 
- easier testing and extension 
