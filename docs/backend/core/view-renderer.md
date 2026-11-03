
# ViewRenderer – Views, Partials and Context Inheritance

## Purpose

The `ViewRenderer` is the central component responsible for rendering views and partials. In addition to including PHP templates, it manages a shared view context.

This allows nested partials to access data that has already been passed to a parent view or partial. The same data does not need to be passed again with every subsequent render call.

The context management follows a few fundamental principles:

- Variables passed to the renderer are inherited by nested render calls.
- Existing variables are readonly by default.
- New variables can always be added.
- Existing variables can only be replaced through an explicit `overwrite` permission.
- Every render call receives its own context. Changes to a child context do not modify the parent context.

## Basic Concept

A render call can pass data directly through the `$data` array:

```php
$viewRenderer->renderPartialString(
    'game',
    'entries',
    [
        'games' => $games,
        'is_admin_view' => $is_admin_view,
    ]
);
```

These variables form the context of the rendered partial. If that partial renders additional partials through the same `ViewRenderer` instance, all inherited variables remain available.

Example of a possible nesting structure:

```text
Views/game/list.php
└── Partials/entries.php
    └── Partials/entry.php
        ├── Partials/badges.php
        └── Partials/actions.php
```

If `list.php` passes `$games`, `$current_user` and `$is_admin_view` to `entries.php`, all subsequent partials (`entries.php`, `entry.php`, `badges.php` and `actions.php`) can access these variables without explicitly passing them again.

## Context Inheritance

With every render call, a new context is built from two possible sources:

1. The context of the currently active parent render call.
2. The data explicitly passed to the current render call.

The parent context itself remains unchanged.

Conceptually:

```text
Child Context = Parent Context + New Data
```

If a key already exists in the parent context, the `overwrite` permission determines whether its value may be replaced.

### Adding New Variables

Variables that do not yet exist in the parent context are added without requiring any additional permission:

```php
$viewRenderer->renderPartialString(
    'game',
    'entry',
    [
        'game' => $game,
    ]
);
```

If `game` does not exist in the inherited context, the supplied value is simply added.

### Existing Variables

Existing variables are not replaced by default.

If the parent context already contains a variable named `$game`, passing another value for `$game` to a child partial will not replace the inherited value unless explicit permission is granted.

This protects the context from unintended overwrites and makes exceptions explicit.

## Explicit Overwriting with `overwrite`

In certain situations, it may be necessary to deliberately replace an inherited variable. For this purpose, `renderPartialString()` provides the optional `$overwrite` parameter.

```php
$viewRenderer->renderPartialString(
    'game',
    'entry',
    [
        'game' => $game,
    ],
    overwrite: ['game']
);
```

The declaration `overwrite: ['game']` allows the existing `$game` value to be replaced with the newly supplied value for this render call only.

Multiple variable names can be explicitly allowed:

```php
$viewRenderer->renderPartialString(
    'game',
    'entry',
    [
        'game' => $game,
        'is_admin_view' => false,
    ],
    overwrite: ['game', 'is_admin_view']
);
```

The `overwrite` permission is not part of the context and is not inherited by nested render calls. It applies exclusively to the render call in which it is specified.

This preserves the fundamental principle: variables are readonly unless a render call explicitly defines an exception.

## Typical Use Case: Lists and Entries

A common use case is rendering a list in which each individual item is represented by its own partial.

### `list.php`

```php
<?= $viewRenderer->renderPartialString('game', 'entries') ?>
```

### `entries.php`

```php
<?php foreach ($games as $game): ?>

    <?= $viewRenderer->renderPartialString(
        'game',
        'entry',
        [
            'game' => $game,
        ]
    ) ?>

<?php endforeach; ?>
```

`$games` is inherited from the context of `list.php` and is therefore available in `entries.php`.

The variable `$game`, however, is created locally by the `foreach` loop.

It does not automatically become part of the renderer context. With every call to `entry.php`, the current loop value is added to the new context for the first time. Therefore, `overwrite` is not required in this particular scenario.

### `entry.php`

The entry partial can access the supplied `$game` value as well as all inherited variables.

Additional partials can be rendered without passing the same data again:

```php
<?= $viewRenderer->renderPartialString('game', 'badges') ?>
<?= $viewRenderer->renderPartialString('game', 'actions') ?>
```

Both partials receive the context of the current `entry.php` render call, including its `$game` value.

Context isolation ensures that the `$game` value of an individual entry is never written back into the parent context of `entries.php`. The next iteration can therefore pass its own `$game` value independently.

## Renderer Context vs. Local PHP Variables

The `ViewRenderer` only manages variables that have been passed through its render calls.

PHP variables created or modified locally inside a template are not automatically added to the renderer context.

For example:

```php
<?php

$is_owner = GamePolicy::isOwner($game, $current_user);
$can_edit = GamePolicy::canEdit($game, $current_user);

?>
```

`$is_owner` and `$can_edit` are local variables of the current template. A subsequently rendered partial does not automatically receive them.

If a nested partial requires these values, they must be passed explicitly:

```php
<?= $viewRenderer->renderPartialString(
    'game',
    'actions',
    [
        'is_owner' => $is_owner,
        'can_edit' => $can_edit,
    ]
) ?>
```

Alternatively, the nested partial can calculate the required values itself, provided this is appropriate from a design and responsibility perspective.

Context inheritance does not replace the local PHP scope and does not automatically collect all variables defined inside a template.

## Readonly Does Not Mean Deep Object Immutability

The readonly principle protects the association between variable names and values within the renderer context.

However, it does not make passed objects immutable. For example, if an object is passed through `$game`, its internal state can still be modified through its methods, provided the class allows it.

The `ViewRenderer` therefore ensures that nested render calls cannot unintentionally replace inherited variables with different values. It is not a general-purpose PHP object immutability mechanism.

## Automatically Provided Variables

Depending on the render call, the `ViewRenderer` adds certain application variables during data preparation.

These include, in particular:

- `$viewRenderer`: The current `ViewRenderer` instance.
- `$current_user`: The currently authenticated user, if available.
- `$system_settings`: System settings provided during the corresponding full view render calls.

These variables are available within the respective context and can be accessed by views and partials.

Nested render calls reuse the already established context. Data preparation must not unintentionally replace inherited values with newly resolved values.

## Technical Implementation

Context management is implemented through an internal stack within the `ViewRenderer` instance.

When a template file is rendered:

1. A new context is built from the parent context and the newly supplied data.
2. The context is pushed onto the stack.
3. The template file is included with the context variables available.
4. Once rendering is complete, the context is removed from the stack.

This stack-based approach allows render calls to be nested to any depth. Each call operates on its own context while the parent context remains intact.

Template variables are made available through PHP's `extract()` function. Context management itself remains separate from the local template scope, so local variable assignments inside a template do not modify the stored renderer context.

## Summary of Rules

| Behavior | Rule |
|---|---|
| Inherited variable | Passed down to nested partials. |
| New variable | Added without requiring additional permission. |
| Existing variable passed again | The inherited value is preserved by default. |
| Variable passed with `overwrite` | The new value replaces the inherited value for that render call. |
| `overwrite` permission | Applies only to the current render call and is not inherited. |
| Child context | Is isolated from its parent context. |
| Locally created template variable | Does not automatically become part of the renderer context. |
| Passed object | Is not deeply immutable. |

**Core principle:** The `ViewRenderer` inherits data throughout the partial hierarchy. Existing context variables are protected by default. New data can be added freely, while intentionally replacing existing values must be explicitly declared through `overwrite`.
