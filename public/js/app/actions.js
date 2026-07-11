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
    updateViews(data.views); 
    executeActionBehaviors(form); 
}

function updateGameRow(form, data) {
    const card = form.closest('[data-id]');
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

function updateViews(views) {
    if (!views) {
        return;
    }

    Object.entries(views).forEach(([target, html]) => {
        const element = document.querySelector(
            `[data-view-bind="${target}"]`
        );
        if (!element) {
            return;
        }
        element.innerHTML = html;
    });
}

function updateActions(card,permissions) {
    if (!permissions) {
        return;
    }

    Object.entries(permissions).forEach(([action,allowed]) => {
        const container =
            card.querySelector(
                `[data-action-container="${action}"]`
            );
        if (!container) {
            return;
        }
        container.hidden = !allowed;
    });
}

function executeActionBehaviors(form) {
    const target_id = form.dataset.actionTargetId;

    document.querySelectorAll(`[data-id="${target_id}"]`).forEach(element => {
        switch (element.dataset.actionBehavior) {
            case 'remove': 
                element.remove(); 
                break; 
            case 'hide': 
                element.hidden = true; 
                break; 
        }
    }); 
}

// Helper - Normalize dto and css cases
function normalizeClassPrefix(key) {
    return key.replaceAll('_', '-'); 
}
