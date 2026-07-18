# Architecture Decisions

This document records important architectural decisions made throughout the project.

The purpose of this file is to document **why** certain solutions were chosen, not only **how** they were implemented.

---

# ADR-001: Layered Backend Architecture

## Status

Accepted

## Date

2026-02-01

## Decision

The application follows a layered architecture:

```text
Controller
    ↓
Service
    ↓
Model
    ↓
Database
```

Controllers are responsible for:

- Request handling
- Validation
- Response generation

Services are responsible for:

- Business logic
- State transitions
- Permission evaluation
- Complex workflows

Models are responsible for:

- Data access
- Persistence
- Entity representation

## Reasoning

Separating responsibilities improves:

- Maintainability
- Testability
- Readability
- Reusability

Business logic should never be spread across controllers, views, or JavaScript files.

---

# ADR-002: Service Layer Ownership

## Status

Accepted

## Date

2026-07-06

## Decision

All application business logic belongs to the service layer.

Controllers may call multiple services but must not implement business rules themselves.

Examples:

### Allowed

```php
$game = GameService::joinGame(
    $game_id,
    $user_id
);
```

### Not Allowed

```php
if ($game->getStatus() === 'WAITING') {
    ...
}
```

inside controllers.

## Reasoning

Business rules often evolve.

Keeping them centralized avoids duplicated logic and inconsistent behavior.

---

# ADR-003: DTO-Based API Responses

## Status

Accepted

## Date

YYYY-MM-DD

## Decision

API responses are returned using DTO structures.

Example:

```json
{
    "success": true,
    "data": {
        "game_id": "...",
        "permissions": {
            "join": true
        }
    }
}
```

## Reasoning

The frontend should never depend directly on database entities or internal model structures.

DTOs provide:

- Stable contracts
- Frontend independence
- Easier refactoring

---

# ADR-004: Declarative Frontend Binding System

## Status

Accepted

## Date

YYYY-MM-DD

## Decision

Frontend updates are handled through a declarative binding system.

Bindings are defined using HTML attributes.

Example:

```html
<div
    data-id="game-123-player-count"
    data-bind-sources="game-123-join"
    data-bind-1-dto-key="player_count"
    data-bind-1-type="text">
</div>
```

JavaScript remains generic and unaware of business logic.

## Reasoning

This approach:

- Reduces duplicated JavaScript
- Improves maintainability
- Keeps behavior close to markup
- Allows reusable UI updates

---

# ADR-005: No Business Logic in JavaScript

## Status

Accepted

## Date

YYYY-MM-DD

## Decision

JavaScript may only:

- Trigger requests
- Process responses
- Update UI
- Handle user interaction

JavaScript must not:

- Evaluate permissions
- Implement game rules
- Calculate application state

## Reasoning

Business logic belongs to the backend.

Keeping it server-side prevents:

- Logic duplication
- Security issues
- Inconsistent behavior

---

# ADR-006: API and MVC Routes Coexist

## Status

Accepted

## Date

YYYY-MM-DD

## Decision

The application supports both:

### MVC Routes

```text
/game/delete
/game/edit
/admin/user/create
```

### API Routes

```text
/api/game/delete
/api/game/join
/api/game/leave
```

Both route types may internally call the same services.

## Reasoning

Different UI workflows require different response types.

MVC routes:

- Render complete pages
- Support traditional navigation

API routes:

- Return JSON
- Support dynamic UI updates

---

# ADR-007: Docker-Based Development Environment

## Status

Accepted

## Date

2026-02-01

## Decision

Development environments are standardized using Docker.

Main containers:

```text
web
app
db
phpmyadmin
```

## Reasoning

Docker ensures:

- Consistent environments
- Easier onboarding
- Reduced "works on my machine" problems

---

# ADR-008: Shared JavaScript Utilities

## Status

Accepted

## Date

2026-07-17

## Decision

Reusable frontend functionality belongs in:

```text
public/js/shared/
```

Examples:

```text
navigation.js
api.js
html.js
storage.js
```

Application-specific functionality belongs in:

```text
public/js/app/
```

Viewer-specific functionality belongs in:

```text
public/js/viewer/
```

## Reasoning

This separation keeps responsibilities clear and improves reusability.

---

# ADR-009: Navigation Handling in Frontend

## Status

Accepted

## Date

YYYY-MM-DD

## Decision

Generic navigation helpers are provided through:

```javascript
reload();
redirect(url);
backOrFallback(fallback);
```

located in:

```text
public/js/shared/navigation.js
```

## Reasoning

Navigation behavior should be reusable and independent from individual modules.

---

# ADR-010: Localization Through Translation Files

## Status

Accepted

## Date

2026-02-08

## Decision

All user-facing text is loaded through translation files.

Locations:

```text
/src/translation/de-de/
/src/translation/en-us/
```

Views should never contain hardcoded text.

Example:

```php
Localization::get(
    'application.general.btn.save'
);
```

## Reasoning

Centralized translations provide:

- Multi-language support
- Consistency
- Easier maintenance

---

# ADR-011: Three.js Viewer Separation

## Status

Accepted

## Date

2026-06-16

## Decision

The Three.js viewer is separated from application logic.

Location:

```text
/public/js/viewer/
```

The viewer is responsible only for:

- Rendering
- Camera handling
- Scene management
- Visual interaction

Game rules remain in backend services.

## Reasoning

Rendering and business logic should evolve independently.

---

# Future Decisions

New architecture decisions should be appended below using the same structure:

```md
# ADR-XXX: Title

## Status

Accepted | Proposed | Deprecated

## Date

YYYY-MM-DD

## Decision

...

## Reasoning

...
```

---

# Guiding Principle

Whenever a new architectural decision is made, document:

1. The problem
2. The chosen solution
3. The alternatives considered
4. The reasoning behind the decision

Future developers should be able to understand not only **what** was built, but also **why** it was built that way.
