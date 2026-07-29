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

// Process and handle bindings and json responses
async function submitJsonBindingForm(form) {
    const formData = new FormData(form);
    let response;
    try {
        response = await fetch(
            form.action,
            {
                method: form.method || "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            }
        );

    } catch (error) {
        console.error("JSON Binding request failed", error);
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

    processBindings(form, json); 
    processSuccessNavigational(form); 
}

// Process the bindings
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

// Check if targets and sources are correctly set - otherwise they will not bind
function targetAcceptsSource(target, sourceId) {
    const allowedSources = parseList(target.dataset.bindSources);
    return allowedSources.includes(sourceId);
}

// Apply the target bindings
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

        let value = resolveDtoValue(response.data, key); 
        if (value === undefined) {
            console.warn("DTO key missing:", key); 
            index++; 
            continue; 
        }

        if (target.getAttribute(`data-bind-${index}-invert`) === "true" && supportInvert(type)) { 
            value = !Boolean(value); 
        }

        updateElement(
            target,
            type, 
            value, 
            target,
            index
        );
        index++;
    }
}

// Update the target element
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

        case "remove": 
            if (Boolean(value)) {
                element.remove(); 
            }
            break; 
        case "class": 
            updateClasses(element, value, target, index); 
            break; 
        default:
            console.warn("Unknown binding type:", type);
    }
}

// Update css classes
function updateClasses(element, value, target, index) {
    const classesFixed = parseList(target.getAttribute(`data-bind-${index}-classes-fixed`)); 
    const classesNew = Array.isArray(value) ? value : parseList(value); 

    [...element.classList].forEach(className => {
        if (!classesFixed.includes(className)) {
            element.classList.remove(className); 
        }
    }); 

    classesNew.forEach(className => {
        element.classList.add(className); 
    }); 
} 

// Process navigational actions bind to success
function processSuccessNavigational(form) { 
    const navigation = form.getAttribute('data-after-success-navigation'); 
    if (!navigation) { 
        return; 
    } 

    switch (navigation) {
        case "back": 
            backOrFallback(form.getAttribute('data-after-success-navigation-fallback') || '/'); 
            break; 
        
        case "redirect": 
            const url = form.getAttribute('data-after-success-navigation-url'); 
            if (!url) {
                console.warn('Redirect navigation requires data-after-success-navigation-url'); 
                return; 
            }
            redirect(url); 
            break; 
        
        case "reload": 
            reload(); 
            break; 
        
        default: 
            console.warn("Unknown navigation action: ", navigation); 
    }
} 

// Parse list
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

// Helper - Resolve the DTO value
function resolveDtoValue(object, path) {
    return path.split(".").reduce(
        (value, key) => value?.[key], object
    ); 
}

// Helper - Defines invertible binding types (usable boolean values) and return if invertible 
function supportInvert(type) {
    return [
        "hidden", 
        "checked",
    ]. includes(type); 
} 
