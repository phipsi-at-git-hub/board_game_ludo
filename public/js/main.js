// Adding event listener to define toggle groups
document.querySelectorAll('[data-toggle-group]').forEach(group => {
    const switch_element = group.querySelector('[data-toggle-switch]'); 
    const children = group.querySelectorAll('[data-toggle-target]'); 

    if (!switch_element) return; 

    const update = () => {
        children.forEach(element => {
            element.classList.toggle('active', switch_element.checked); 
        }); 
    }; 

    switch_element.addEventListener('change', update); 

    // Initial state
    update(); 
}); 

/**
 * Form functions
 */
// Function - submit form
function submitForm(form) {
    const form_data = new FormData(form); 

    const target_selector = form.dataset.ajaxTarget; 
    const target = target_selector ? document.querySelector(target_selector) : null; 

    fetch(form.action, {
        method: form.method || 'POST', 
        body: form_data
    }).then(async (response) => {
        const content_type = response.headers.get('content-type') || ''; 

        if (content_type.includes('application/json')) {
            return response.json();
        }
        return response.text(); 
    }).then((data) => {
        // Case 1: JSON response
        if (typeof data === 'object') {
            if (target) {
                target.innerHTML = data.message || 'OK'; 
                target.classList.add('success', 'active'); 

                setTimeout(() => {
                    target.classList.remove('success', 'active'); 
                }, 3000); 
            }
            return; 
        }

        // Case 2: HTML response
        if (target) {
            target.innerHTML = data; 
            target.classList.add('active'); 
        }
    }).catch((error) => {
        if (target) {
            target.innerHTML = 'Error'; 
            target.classList.add('error'); 
        }
        console.error(error); 
    }); 
}

/**
 * Event Listener
 */
// Add eventListener to clickable elements like buttons
document.addEventListener('click', (event) => {
    const btn = event.target.closest('[data-message-value]');
    if (!btn) return; 

    const form = btn.closest('form'); 
    if (!form) return; 

    const hidden = form.querySelector('input[type="hidden"][name$="_message"]');
    if (!hidden) return; 

    // active state
    form.querySelectorAll('[data-message-value]').forEach(button => button.classList.remove('active')); 
    btn.classList.add('active'); 

    // sync value
    hidden.value = btn.dataset.messageValue; 

    submitForm(form); 
}); 

// Add eventListener to changeable elements like checkboxes
document.addEventListener('change', (event) => {
    const element = event.target; 
    if (!element.matches('[data-auto-save="change"]')) return; 

    const form = element.closest('form'); 
    if (!form) return; 

    submitForm(form); 
}); 

