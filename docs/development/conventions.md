# Development Conventions

## Introduction

This document defines the development conventions used throughout the project.

The purpose of these conventions is to:

- maintain a consistent codebase
- improve readability
- reduce architectural drift
- simplify onboarding
- make future refactoring easier

These conventions apply to all backend and frontend code.

---

# General Principles

## Prefer Clarity Over Cleverness

Code should be easy to understand.

Prefer:

```php
if ($user->isAdmin()) {
    ...
}
```

over:

```php
$user->isAdmin() && ...
```

when readability suffers.

---

## Consistency Beats Personal Preference

Even if multiple solutions are technically valid, the project should use one consistent style.

Consistency is more important than individual coding preferences.

---

## Keep Files Focused

A file should have a clear responsibility.

Avoid:

- utility dumping grounds
- giant service classes
- controllers containing business logic

---

## Favor Explicitness

Prefer:

```php
GamePolicy::canJoin($user, $game);
```

over:

```php
GamePolicy::check($user, $game, 'join');
```

Explicit methods are easier to understand and maintain.

---

# Naming Conventions

## Classes

Use PascalCase.

Examples:

```php
GameService
GamePolicy
SystemService
GameController
```

---

## Interfaces

Prefix with `Interface`.

Examples:

```php
GameRepositoryInterface
LoggerInterface
```

---

## Traits

Suffix with `Trait`.

Examples:

```php
UuidTrait
TimestampTrait
```

---

## Methods

Use camelCase.

Examples:

```php
getGame()
joinGame()
createUser()
```

---

## Variables

Use snake_case.

Examples:

```php
$game_id
$user_id
$current_player
```

---

## Constants

Use UPPER_SNAKE_CASE.

Examples:

```php
MAX_PLAYERS
DEFAULT_LANGUAGE
REQUEST_METHOD_POST
```

---

## Database Tables

Use snake_case plural names.

Examples:

```sql
users
games
game_players
system_settings
```

---

## Database Columns

Use snake_case.

Examples:

```sql
created_at
updated_at
created_by_user_id
current_player_id
```

---

# Directory Structure

## Backend

```text
src/

├── Controllers/
├── Services/
├── Models/
├── Policies/
├── Core/
├── DTO/
├── Helpers/
└── Constants/
```

---

## Frontend

```text
js/

├── app/
├── viewer/
└── shared/
```

---

## Documentation

```text
docs/

├── architecture/
├── development/
├── frontend/
└── backend/
```

---

# Controller Conventions

## Controllers Should Stay Thin

Controllers should:

- receive requests
- validate input
- call services
- return responses

Controllers should not:

- contain business logic
- execute permission logic
- contain database queries

Good:

```php
$service->join($game, $user);
```

Bad:

```php
if (...) {
    ...
}
```

with large business logic blocks.

---

## Controllers Return Responses

Controllers should not perform direct rendering logic beyond:

```php
$this->render(...);
$this->json(...);
$this->redirect(...);
```

---

# Service Conventions

## Services Contain Business Logic

Services are responsible for:

- state changes
- workflows
- application rules

Examples:

```php
GameService
SystemService
UserService
```

---

## Services Should Be Reusable

A service should not depend on:

```php
$_POST
$_GET
$_SESSION
```

directly.

Instead:

```php
$service->join($game, $user);
```

---

## Services May Use Multiple Models

Services orchestrate multiple entities.

Example:

```php
Game
Player
RuleSet
```

working together.

---

# Model Conventions

## Models Represent Data

Models are responsible for:

- persistence
- loading
- saving
- database interaction

---

## Avoid Business Logic In Models

Good:

```php
GameModel::findById(...)
```

Bad:

```php
GameModel::joinPlayer(...)
```

if significant business rules are involved.

Such logic belongs into services.

---

# Policy Conventions

## Policies Handle Permissions

Policies answer:

```text
Can user do X?
```

Examples:

```php
GamePolicy::canJoin()
GamePolicy::canPlay()
GamePolicy::canDelete()
```

---

## Policies Must Not Modify State

Policies return:

```php
true
false
```

only.

They do not:

- save data
- update data
- perform actions

---

# Container Conventions

## Register Shared Services As Singletons

Examples:

```php
SystemService
GameService
```

Registration:

```php
$container->singleton(...)
```

---

## Avoid Container Access Everywhere

Prefer:

```php
App::get(SystemService::class)
```

only when necessary.

Most classes should receive dependencies through constructors.

---

## Container Is Infrastructure

The container manages object creation.

The container is not business logic.

---

# Application Conventions

## One App Instance Per Request

The application bootstraps:

```text
Request

    |

    v

App

    |

    v

Container

    |

    v

Controllers / Services
```

---

## App Is Global Request Context

The App instance provides:

- container access
- boot lifecycle
- request-wide shared services

---

# View Conventions

## Views Contain Presentation Only

Views should:

- display data
- render HTML

Views should not:

- perform database access
- execute business logic
- contain permission decisions

---

## Prefer Prepared Data

Good:

```php
<?= $game->getName() ?>
```

Bad:

```php
<?= GameModel::findById(...) ?>
```

inside views.

---

# Frontend Conventions

## Keep Viewer Separate From Game Logic

Viewer responsibility:

```text
Rendering
Camera
Scene
Animation
```

Game logic responsibility:

```text
Backend
Services
Policies
```

---

## Prefer Declarative Binding

Prefer:

```html
data-bind-*
```

over custom JavaScript for every endpoint.

---

## Reuse Shared Components

Common functionality belongs in:

```text
js/shared/
```

not duplicated across features.

---

# Error Handling

## Fail Fast

Invalid states should fail immediately.

Good:

```php
throw new LogicException(...);
```

instead of silently ignoring problems.

---

## Do Not Hide Errors

Unexpected situations should be visible during development.

---

# Documentation Conventions

## Document Architecture Decisions

Whenever a significant architectural change is introduced:

Create or update documentation.

Examples:

```text
Container
App Lifecycle
Viewer Architecture
Binding System
```

---

## Keep Documentation Close To Reality

Documentation should describe:

```text
What exists today
```

not:

```text
What might exist someday
```

Future ideas should be clearly marked.

---

# Refactoring Guidelines

## Improve Existing Code

When touching code:

- improve naming
- improve structure
- remove duplication

when reasonable.

---

## Avoid Premature Abstractions

Do not create abstractions before a real need exists.

Prefer:

```php
Simple implementation first.
```

Generalize later if duplication appears.

---

# Testing Philosophy

## Test Behavior

Focus on:

```text
What should happen?
```

rather than:

```text
How is it implemented?
```

---

## Critical Areas

Particularly important:

- permissions
- game state transitions
- player actions
- system settings
- API endpoints

---

# Code Review Checklist

Before merging changes:

- Naming is consistent
- No duplicated logic introduced
- Controllers remain thin
- Services contain business logic
- Policies contain permission logic
- Views remain presentation-only
- Documentation updated if needed
- No unnecessary complexity added

---

# Summary

The architecture follows a simple rule:

```text
Controllers

    |

    v

Services

    |

    v

Models
```

with

```text
Policies
```

handling permissions,

```text
App + Container
```

handling infrastructure,

and

```text
Views
```

handling presentation.

Maintaining this separation is the most important convention in the project.
