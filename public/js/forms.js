function initForms() {
    initToggleGroups();
    initMessageButtons();
    initAutoSave();
    initSwitchSelects();
    initBadgeSelects(); 
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

/* ------------------------------
   BADGE SELECTS (>3 options)
------------------------------ */
function initBadgeSelects() {
    document.querySelectorAll('select[data-ui="badge-select"]').forEach(select => {
        const options = [...select.options];
        if (options.length < 2) return;

        const wrapper = document.createElement('div');
        wrapper.className = 'badge-select';

        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'badge-select-trigger';

        const label = document.createElement('span');
        label.className = 'badge-select-label';

        const arrow = document.createElement('span');
        arrow.className = 'badge-select-arrow';
        arrow.innerHTML = '▼';

        trigger.append(label, arrow);

        const dropdown = document.createElement('div');
        dropdown.className = 'badge-select-dropdown';

        /* ------------------------------
           ACTIVE STATE UPDATE
        ------------------------------ */
        const setActive = (option) => {
            label.textContent = option.text;
            dropdown.querySelectorAll('.badge-option').forEach(b => b.classList.remove('active'));

            const active = dropdown.querySelector(`.badge-option[data-value="${CSS.escape(option.value)}"]`);
            if (active) active.classList.add('active');

            wrapper.classList.remove('default', 'inactive', 'mid', 'active', 'warning');
            if (option.dataset.state) {
                wrapper.classList.add(option.dataset.state);
            }
        };

        /* ------------------------------
           OPTIONS BUILD
        ------------------------------ */
        options.forEach(option => {

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'badge-option';
            btn.textContent = option.text;
            btn.dataset.value = option.value;

            btn.addEventListener('click', () => {
                select.value = option.value;
                setActive(option);
                wrapper.classList.remove('open');
                select.dispatchEvent(new Event('change', { bubbles: true }));
            });
            dropdown.appendChild(btn);

            if (option.selected) {
                setActive(option);
            }
        });

        /* ------------------------------
           TOGGLE
        ------------------------------ */
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            wrapper.classList.toggle('open');
        });

        /* ------------------------------
           WIDTH CALCULATION (FIXED)
           -> MAX OPTION WIDTH ONCE
        ------------------------------ */
        const measure = document.createElement('span');
        measure.style.position = 'absolute';
        measure.style.visibility = 'hidden';
        measure.style.whiteSpace = 'nowrap';

        document.body.appendChild(measure);
        let maxWidth = 0;
        options.forEach(option => {
            measure.textContent = option.text;
            const w = measure.getBoundingClientRect().width;
            if (w > maxWidth) maxWidth = w;
        });

        document.body.removeChild(measure);

        const PADDING = getCssVariables('--badge-select-padding', 60); 
        const MAX_WIDTH = getCssVariables('--badge-select-max-width', 200);
        const finalWidth = Math.min(maxWidth + PADDING, MAX_WIDTH);
        wrapper.style.width = `${finalWidth}px`;

        /* ------------------------------
           INSERT
        ------------------------------ */
        wrapper.append(trigger, dropdown);

        select.classList.add('badge-select-enhanced');

        select.insertAdjacentElement('afterend', wrapper);
    });

    /* close on outside click */
    document.addEventListener('click', () => {
        document.querySelectorAll('.badge-select.open')
            .forEach(el => el.classList.remove('open'));
    });
}

function getCssVariables(var_name, fallback = 0) {
    const raw = getComputedStyle(document.documentElement).getPropertyValue(var_name).trim();
    if (!raw) return fallback; 

    // remove px at the end of line and convert to number
    const value = parseFloat(raw); 

    return isNaN(value) ? fallback : value; 
}
