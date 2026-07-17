# JSON Binding System

## Overview

The JSON Binding System provides a generic, declarative mechanism for updating DOM elements based on JSON responses from backend actions.

Instead of implementing individual JavaScript logic for every user interaction, HTML elements define:

- which actions they react to
- which elements should be updated
- which response values are mapped
- how the update should be applied

The JavaScript binding layer remains generic and contains no application-specific business logic.

---

# Design Goals

The Binding System follows these principles:

- no business logic inside JavaScript bindings
- no endpoint-specific JavaScript
- reusable frontend behavior
- backend-controlled state
- declarative DOM updates
- separation between data, presentation and behavior

The backend provides the current state through DTOs.
The frontend only applies these states to the DOM.

---

# Architecture Overview

The Binding System consists of three parts:

```
HTML
 |
 | declares bindings
 v

binding.js
 |
 | sends requests
 | processes responses
 | updates DOM
 v

Backend API
 |
 | returns DTOs and optional views
 v

JSON Response
```

---

# Request Flow

Example:

```
User clicks JOIN

        |
        v

HTML Form

        |
        v

binding.js

        |
        v

POST /api/game/join/{id}

        |
        v

Backend

        |
        v

JSON Response

        |
        v

processBindings()

        |
        v

DOM Update
```

---

# JSON Response Contract

A successful JSON response follows this structure:

```json
{
    "success": true,
    "message": "Action completed",

    "data": {
        "status": "WAITING",
        "player_count": 2
    },

    "views": {
        "players": "<div>...</div>"
    }
}
```

## Response properties

| Property | Description |
|---|---|
| success | Indicates whether the action was successful |
| message | Optional human-readable message |
| data | DTO containing values used for bindings |
| views | Optional server-rendered HTML fragments |

---

# Binding Source

A binding source is usually a form which triggers an action.

A JSON binding form requires:

```html
<form
    data-response="json"
    action="/api/example"
    method="POST">

</form>
```

## Attributes

| Attribute | Purpose |
|-|-|
| data-response="json" | Enables JSON binding handling |

---

# Binding Targets

A source defines which elements should be updated after a successful response.

Example:

```html
<form
    data-id="game-join"
    data-response="json"
    data-bind-targets="
        game-player-count,
        game-status
    ">
</form>
```

The targets must explicitly accept the source.

Example:

```html
<div
    data-id="game-status"
    data-bind-sources="game-join">

</div>
```

The binding system therefore prevents accidental updates.

---

# Binding Source and Target Relationship

A binding only happens when:

```
Source
 |
 | data-bind-targets
 |
 v

Target
 |
 | data-bind-sources
 |
 v

Accepted
```

Example:

Source:

```html
<form
    data-id="game-delete"
    data-bind-targets="game-row">
</form>
```

Target:

```html
<div
    data-id="game-row"
    data-bind-sources="game-delete">

</div>
```

---

# Binding Attributes

Binding attributes use an indexed format:

```
data-bind-{index}-{property}
```

Example:

```html
data-bind-1-dto-key="player_count"

data-bind-1-type="text"
```

The index allows multiple bindings on the same element.

Example:

```html
<span
    data-bind-1-dto-key="player_count"
    data-bind-1-type="text"

    data-bind-2-dto-key="player_count_category"
    data-bind-2-type="class"
    data-bind-2-class-prefix="player-count">

</span>
```

---

# DTO Binding

## data-bind-X-dto-key

Defines which value from the response DTO should be used.

Example:

```html
<span
    data-bind-1-dto-key="player_count"
    data-bind-1-type="text">

</span>
```

Response:

```json
{
    "data": {
        "player_count": 3
    }
}
```

Result:

```html
<span>
3
</span>
```

---

# Nested DTO Values

Nested objects are supported.

Example:

```html
<form
    data-bind-1-dto-key="permissions.join"
    data-bind-1-type="hidden">

</form>
```

Response:

```json
{
    "data": {
        "permissions": {
            "join": true
        }
    }
}
```

The resolver follows the path:

```
permissions
    |
    join
```

---

# Binding Types

## text

Updates the text content of an element.

Attribute:

```html
data-bind-X-type="text"
```

Example:

```html
<span
    data-bind-1-dto-key="status"
    data-bind-1-type="text">

</span>
```

Response:

```json
{
    "status": "WAITING"
}
```

Result:

```html
<span>
WAITING
</span>
```

---

## html

Replaces the inner HTML of an element.

Attribute:

```html
data-bind-X-type="html"
```

Example:

```html
<div
    data-bind-1-dto-key="message"
    data-bind-1-type="html">

</div>
```

Used when backend content should directly replace the element content.

---

## view

Updates an element using a server-rendered HTML fragment.

Unlike `html`, the value does not come from `data`.

The value comes from:

```json
{
    "views": {
        "players": "<div>Players</div>"
    }
}
```

Required attribute:

```html
data-bind-X-view-key
```

Example:

```html
<div
    data-bind-1-type="view"
    data-bind-1-view-key="players">

</div>
```

---

## value

Updates form values.

Attribute:

```html
data-bind-X-type="value"
```

Example:

```html
<input
    data-bind-1-dto-key="game_name"
    data-bind-1-type="value">
```

---

## checked

Updates checkbox state.

Example:

```html
<input
    type="checkbox"
    data-bind-1-dto-key="is_private"
    data-bind-1-type="checked">
```

The value is converted to boolean.

---

## hidden

Controls element visibility.

Example:

```html
<form
    data-bind-1-dto-key="permissions.join"
    data-bind-1-type="hidden">

</form>
```

Behavior:

```javascript
element.hidden = Boolean(value)
```

Example:

```json
{
    "permissions": {
        "join": false
    }
}
```

Result:

```html
<form hidden>
</form>
```

---

## remove

Removes an element completely from the DOM.

Example:

```html
<div
    data-bind-1-dto-key="deleted"
    data-bind-1-type="remove">

</div>
```

Response:

```json
{
    "deleted": true
}
```

Result:

```javascript
element.remove()
```

Typical usage:

- deleting list items
- removing cards
- removing notifications

---

## class

Updates CSS state classes.

Required additional attribute:

```html
data-bind-X-class-prefix
```

Example:

```html
<span
    class="player-count-empty"

    data-bind-1-dto-key="player_count_category"
    data-bind-1-type="class"
    data-bind-1-class-prefix="player-count">

</span>
```

Response:

```json
{
    "player_count_category": "high"
}
```

Result:

Before:

```html
class="player-count-empty"
```

After:

```html
class="player-count-high"
```

The binding system removes previous matching classes:

```
player-count-*
```

before adding the new state.

---

# Multiple Bindings

One element can have multiple bindings.

Example:

```html
<div

    data-bind-1-dto-key="player_count"
    data-bind-1-type="text"

    data-bind-2-dto-key="player_count_category"
    data-bind-2-type="class"
    data-bind-2-class-prefix="player-count"

>
</div>
```

The bindings are processed sequentially.

---

# Navigation After Success

Actions may optionally trigger navigation after successful completion.

Example:

```html
<form
    data-response="json"
    data-after-success-navigation="back">

</form>
```

Supported navigation actions:

| Value | Description |
|-|-|
| back | Uses browser history navigation |
| redirect | Redirects to defined target |

Navigation behavior itself is handled separately by the navigation module.

---

# Adding New Binding Types

New binding types must remain generic.

They must:

- not contain business rules
- not know specific endpoints
- operate only on supplied values

Implementation location:

```
binding.js
```

Function:

```javascript
updateElement()
```

Example:

```javascript
case "newType":

    break;
```

---

# Development Rules

## Do

- keep bindings declarative
- keep business logic in backend services
- return complete DTO state
- reuse existing binding types
- document new generic capabilities


## Do not

- add game-specific logic into binding.js
- access endpoints directly from binding.js
- duplicate form handling logic
- create custom JavaScript for simple DOM updates

---

# Philosophy

The Binding System intentionally moves UI state handling away from imperative JavaScript.

Instead of:

> "When the user clicks JOIN, find this element and change this class."

the application describes:

> "This element represents player state and should reflect the current DTO value."

The backend provides the state.
The binding system synchronizes the interface.
