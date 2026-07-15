/**
 * Generic JSON Binding System
 *
 * Responsibilities:
 * - Intercept JSON based actions
 * - Execute requests
 * - Process DTO responses
 * - Resolve n:m bindings
 * - Update DOM declaratively
 *
 * No business logic allowed.
 */

document.addEventListener("DOMContentLoaded", () => {
    initializeJsonActions();
});

function initializeJsonActions() {
    document.querySelectorAll('[data-response="json"]').forEach(source => {
        if (source.tagName === "FORM") {
            source.addEventListener("submit", handleJsonAction);
        }
    });
}

async function handleJsonAction(event) {
    event.preventDefault();
    const source = event.currentTarget;
    const formData = new FormData(source);
    let response;
    try {
        response = await fetch(
            source.action,
            {
                method: source.method || "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            }
        );

    } catch (error) {
        console.error(
            "JSON Binding request failed",
            error
        );
        return;
    }

    let json;
    try {
        json = await response.json();
    } catch (error) {
        console.error("Invalid JSON response", error);
        return;
    }

    if (!json.success) {
        return;
    }

    processBindings(
        source,
        json 
    );
}

function processBindings(source, response) {
    const sourceId = source.dataset.id;
    if (!sourceId) {
        console.warn("JSON Binding source has no data-id", source);
        return;
    }

    const targets = parseList(source.dataset.bindTargets);

    targets.forEach(
        targetId => {
            const target = document.querySelector(`[data-id="${targetId}"]`);

            if (!target) {
                console.warn("Binding target not found:", targetId);
                return;
            }

            if (!targetAcceptsSource(target, sourceId)) {
                console.warn("Binding rejected:", sourceId, "->", targetId);
                return;
            }

            applyTargetBindings(target, response);
        }
    );
}

function targetAcceptsSource(target, sourceId) {
    const allowedSources = parseList(target.dataset.bindSources);
    return allowedSources.includes(sourceId);
}

function applyTargetBindings(target, response) {
    let index = 1;

    while (target.hasAttribute(`data-bind-${index}-type`)) {
        const type = target.getAttribute(`data-bind-${index}-type`);

        if (!type) {
            break;
        }

        if (type === "view") {
            const viewKey = target.getAttribute(`data-bind-${index}-view-key`);

            if (!viewKey) {
                console.warn("View binding requires view-key", target, index);
                index++;
                continue;
            }

            if (!response.views || !response.views[viewKey]) {
                console.warn("View not found:", viewKey);
                index++;
                continue;
            }
            updateElement(target, "view", response.views[viewKey]);
            index++;
            continue;
        }

        const key = target.getAttribute(`data-bind-${index}-dto-key`);

        if (!key) {console.warn("DTO binding requires dto-key", target, index);
            index++;
            continue;
        }

        const value = resolveDtoValue(response.data, key); 
        if (value === undefined) {
            console.warn("DTO key missing:", key); 
            index++; 
            continue; 
        }

        updateElement(
            target,
            type,
            //response.data[key],
            resolveDtoValue(response.data, key), 
            target,
            index
        );
        index++;
    }
}

function updateElement(
    element,
    type,
    value,
    target, 
    index
) {
    switch(type) {
        case "text":
            element.textContent = value;
            break;

        case "html":
            element.innerHTML = value;
            break;

        case "view":
            element.innerHTML = value;
            break;

        case "value":
            element.value = value;
            break;

        case "checked":
            element.checked = Boolean(value);
            break;

        case "hidden":
            element.hidden = Boolean(value);
            break;

        case "class":
            const prefix = target.getAttribute(`data-bind-${index}-class-prefix`);

            if (!prefix) {
                console.warn("Class binding requires prefix", element);
                return;
            }

            removePrefixedClasses(element, prefix);
            element.classList.add(`${prefix}-${value}`);
            break;
        default:
            console.warn("Unknown binding type:", type);
    }
}

function removePrefixedClasses(element, prefix) {
    [...element.classList].forEach(
        className => {
            if (className.startsWith(prefix + "-")) {
                element.classList.remove(className);
            }
        }
    );
}

function parseList(value) {
    if (!value) {
        return [];
    }

    return value
        .split(",")
        .map(
            item => item.trim()
        )
        .filter(
            item => item.length > 0
        );
}

// Helper 
function resolveDtoValue(object, path) {
    return path.split(".").reduce(
        (value, key) => value?.[key], object
    ); 
}
