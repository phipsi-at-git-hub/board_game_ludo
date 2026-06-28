function initGameRuleset() {
    const presetElement = document.getElementById('ruleset-presets');
    if (!presetElement) {
        return;
    }
    const presets = JSON.parse(presetElement.textContent);
    const classicPreset = presets?.classic?.ruleset; 
    if (!classicPreset) {
        return;
    }
    const ruleFields = Object.keys(classicPreset); 

    const rulesetSelect = document.querySelector('select[name="ruleset"]');
    if (!rulesetSelect) {
        return;
    }

    let isApplyingPreset = false;

    function getField(name) {
        return document.querySelector(`select[name="${name}"]`);
    }

    function normalizeValue(value) {
        if (value === true) {
            return '1';
        }
        if (value === false) {
            return '0';
        }
        return String(value);
    }

    function setSelectValue(select, value) {
        const normalizedValue = normalizeValue(value);
        if (select.value === normalizedValue) {
            return;
        }

        select.value = normalizedValue;
        select.dispatchEvent(
            new Event('change', {
                bubbles: true
            })
        );
    }

    function applyPreset(presetName) {
        const preset = presets?.[presetName]?.ruleset;
        if (!preset) {
            return;
        }

        isApplyingPreset = true;

        try {
            ruleFields.forEach(fieldName => {
                const select = getField(fieldName);
                if (!select) {
                    return;
                }
                if (!(fieldName in preset)) {
                    return;
                }
                setSelectValue(select, preset[fieldName]);
            });
        } finally {
            isApplyingPreset = false;
            updateRulesetSelection();
        }
    }

    function matchesPreset(presetName) {
        const preset = presets?.[presetName]?.ruleset;
        if (!preset) {
            return false;
        }

        return ruleFields.every(fieldName => {
            const select = getField(fieldName);
            if (!select) {
                return false;
            }
            return (
                normalizeValue(select.value) ===
                normalizeValue(preset[fieldName])
            );
        });
    }

    function updateRulesetSelection() {
        if (matchesPreset('classic')) {
            if (rulesetSelect.value !== 'classic') {
                rulesetSelect.value = 'classic';
                rulesetSelect.dispatchEvent(
                    new Event('change', {
                        bubbles: true
                    })
                );
            }
            return;
        }

        if (matchesPreset('advanced')) {
            if (rulesetSelect.value !== 'advanced') {
                rulesetSelect.value = 'advanced';
                rulesetSelect.dispatchEvent(
                    new Event('change', {
                        bubbles: true
                    })
                );
            }
            return;
        }

        if (rulesetSelect.value !== 'custom') {
            rulesetSelect.value = 'custom';
            rulesetSelect.dispatchEvent(
                new Event('change', {
                    bubbles: true
                })
            );
        }
    }

    rulesetSelect.addEventListener('change', () => {
        if (isApplyingPreset) {
            return;
        }

        switch (rulesetSelect.value) {
            case 'classic':
                applyPreset('classic');
                break;
            case 'advanced':
                applyPreset('advanced');
                break;
            case 'custom':
                break;
        }
    });

    ruleFields.forEach(fieldName => {
        const select = getField(fieldName);
        if (!select) {
            return;
        }

        select.addEventListener('change', () => {
            if (isApplyingPreset) {
                return;
            }
            updateRulesetSelection();
        });
    });
    updateRulesetSelection();
}
