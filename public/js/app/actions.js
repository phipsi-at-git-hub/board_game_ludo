

async function handleActionSubmit(form) { 
    const response =
        await fetch(
            form.action,
            {
                method: form.method || 'POST',
                body: new FormData(form)
            }
        );

    const data = await response.json();

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
        const dto_key = element.dataset.classBind; 
        const css_prefix = dto_key.replaceAll('_', '-'); 

        // remove old dynamic classes
        [...element.classList].filter(
            cls => cls.startsWith(css_prefix + '-')
        ).forEach(
            cls => element.classList.remove(cls)
        );

        // add new class
        element.classList.add(`${css_prefix}-${value}`); 
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
