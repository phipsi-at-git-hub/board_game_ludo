function initActions() {
    document.addEventListener(
        'submit',
        handleActionSubmit
    );
}



async function handleActionSubmit(event) {
    const form = event.target;
    const button = form.querySelector('[data-action="submit"]');
    if (!button) {
        return;
    }

    event.preventDefault();
    const response =
        await fetch(
            form.action,
            {
                method: form.method || 'POST',
                body: new FormData(form)
            }
        );

    const data =
        await response.json();

    if (!data.success) {
        console.error(data.errors);
        return;
    }

    updateGameRow(
        form,
        data.data
    );
}

function updateGameRow(form, data) {
    const card = form.closest('[data-game-id]');
    if (!card) {
        return;
    }

    Object.entries(data)
        .forEach(([key,value]) => {
            updateBinding(
                card,
                key,
                value, 
                data
            );
            updateClassBinding(
                card, 
                key, 
                value 
            ); 
        });

    updateActions(card, data.permissions);
}

function updateBinding(card, key, value, data) {
    const element = card.querySelector(`[data-bind="${key}"]`);
    if (!element) {
        return;
    }

    switch(key) {
        case 'player_count': 
            element.textContent = `${value}/${data.player_max}`; 
            break;
        default:
            element.textContent = value;
    }
}

function updateClassBinding(card, key, value) {
    const elements = card.querySelectorAll(`[data-class-bind="${key}"]`); 

    elements.forEach(element => {
        const prefix = element.dataset.classBind; 
        const prefix_normalized = prefix.replaceAll('_', '-'); 

        // remove old dynamic classes
        [...element.classList].filter(
            cls => cls.startsWith(prefix_normalized + '-')
        ).forEach(
            cls => element.classList.remove(cls)
        );

        // add new class
        element.classList.add(`${prefix_normalized}-${value}`); 
    }); 
}

function updateActions(card,permissions) {
    if (!permissions) {
        return;
    }

    Object.entries(permissions)
        .forEach(([action,allowed]) => {
            const container =
                card.querySelector(
                    `[data-action-container="${action}"]`
                );
            if (!container) {
                return;
            }

            container.hidden =
                !allowed;
        });
}

// Helper - Normalize dto and css cases
function normalizeClassPrefix(key) {
    return key.replaceAll('_', '-'); 
}
