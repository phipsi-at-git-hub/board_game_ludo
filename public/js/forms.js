function initForms() {
    initToggleGroups();
    initMessageButtons();
    initAutoSave();
    initSwitchSelects();
}

/* ------------------------------
   TOGGLE GROUPS
------------------------------ */
function initToggleGroups() {
    document.querySelectorAll('[data-toggle-group]').forEach(group => {
        const switch_element = group.querySelector('[data-toggle-switch]');
        const children = group.querySelectorAll('[data-toggle-target]');
        if (!switch_element) return;

        const update = () => {
            children.forEach(el =>
                el.classList.toggle('active', switch_element.checked)
            );
        };

        switch_element.addEventListener('change', update);
        update();
    });
}

/* ------------------------------
   FORM SUBMIT
------------------------------ */
function submitForm(form) {
    const form_data = new FormData(form);

    const target_selector = form.dataset.ajaxTarget;
    const target = target_selector ? document.querySelector(target_selector) : null;

    fetch(form.action, {
        method: form.method || 'POST',
        body: form_data
    })
    .then(async res => {
        const type = res.headers.get('content-type') || '';
        return type.includes('application/json') ? res.json() : res.text();
    })
    .then(data => {

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

        if (target) {
            target.innerHTML = data;
            target.classList.add('active');
        }

    })
    .catch(err => {
        if (target) {
            target.innerHTML = 'Error';
            target.classList.add('error');
        }
        console.error(err);
    });
}

/* ------------------------------
   MESSAGE BUTTONS
------------------------------ */
function initMessageButtons() {
    document.addEventListener('click', event => {
        const btn = event.target.closest('[data-message-value]');
        if (!btn) return;

        const form = btn.closest('form');
        if (!form) return;

        const hidden = form.querySelector('input[type="hidden"][name$="_message"]');
        if (!hidden) return;

        form.querySelectorAll('[data-message-value]')
            .forEach(b => b.classList.remove('active'));

        btn.classList.add('active');
        hidden.value = btn.dataset.messageValue;

        submitForm(form);
    });
}

/* ------------------------------
   AUTO SAVE
------------------------------ */
function initAutoSave() {
    document.addEventListener('change', event => {
        const el = event.target;
        if (!el.matches('[data-auto-save="change"]')) return;

        const form = el.closest('form');
        if (!form) return;

        submitForm(form);
    });
}

/* ------------------------------
   SWITCH SELECTS (≤3 options only)
------------------------------ */
function initSwitchSelects() {

    document.querySelectorAll('select[data-ui="switch"]').forEach(select => {
        const options = [...select.options];

        if (options.length < 2) return;

        const switch_group = document.createElement('div');
        switch_group.className = 'switch-group'; 

        // set CSS variable for slider width
        switch_group.style.setProperty('--option-count', options.length);


        const indicator = document.createElement('div');
        indicator.className = 'switch-indicator';

        switch_group.appendChild(indicator);

        const updateStateClass = (state) => {
            switch_group.classList.remove('inactive', 'mid', 'active');
            if (state) switch_group.classList.add(state);
        };

        const moveIndicator = (index) => {
            indicator.style.transform = `translateX(${index * 100}%)`;
        };

        options.forEach((option, index) => {

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'switch-option';
            btn.textContent = option.text;

            if (option.selected) {
                btn.classList.add('active');
                moveIndicator(index);

                updateStateClass(option.dataset.state);
            }

            btn.addEventListener('click', () => {

                select.value = option.value;

                switch_group.querySelectorAll('.switch-option')
                    .forEach(b => b.classList.remove('active'));

                btn.classList.add('active');

                moveIndicator(index);

                updateStateClass(option.dataset.state);

                select.dispatchEvent(new Event('change', { bubbles: true }));
            });

            switch_group.appendChild(btn);
        });

        select.classList.add('switch-enhanced');

        select.insertAdjacentElement('afterend', switch_group);
    });
}
