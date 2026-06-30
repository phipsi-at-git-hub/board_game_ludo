function initGameRuleset() {
    // read out ruleset presets
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

    // read out original ruleset of current game if there
    const originalElement = document.getElementById('ruleset-original'); 
    let originalRuleset = null; 
    if (originalElement) {
        try {
            const parsed = JSON.parse(originalElement.textContent); 
            originalRuleset = parsed?.original?.ruleset ?? null; 
        } catch (e) {
            console.warn('Invalid original ruleset JSON', e); 
        }
    }

    // get restore button if in edit view
    const restoreButtonGroup = document.getElementById('restore-ruleset-group'); 
    const restoreButton = document.getElementById('restore-ruleset'); 

    // check for and define selector
    const rulesetSelect = document.querySelector('select[name="ruleset"]');
    if (!rulesetSelect) {
        return;
    }

    let isApplyingPreset = false;

    // return DOM select element with given name
    function getField(name) {
        return document.querySelector(`select[name="${name}"]`);
    }

    // normalize given value
    function normalizeValue(value) {
        if (value === true) {
            return '1';
        }
        if (value === false) {
            return '0';
        }
        return String(value);
    }

    // set given value to select
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

    // apply presets
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
            updateRestoreButton(); 
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

    // update all rules according to the chosen ruleset preset 
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

    // get the current ruleset in the view
    function getCurrentRuleset() {
        const result = {}; 
        ruleFields.forEach(fieldName => {
            const select = getField(fieldName); 
            if (!select) {
                return; 
            } 
            result[fieldName] = normalizeValue(select.value); 
        }); 
        return result; 
    }

    // check if current ruleset equal the games original ruleset
    function isRulesetDirty() {
        if (!originalRuleset) {
            return false; 
        } 
        return ruleFields.some(fieldName => {
            const select = getField(fieldName); 
            if (!select) {
                false; 
            }
            return normalizeValue(select.value) !== normalizeValue(originalRuleset[fieldName]); 
        }); 
    }

    // update ui - fade in or out restore button
    function updateRestoreButton() {
        if (!restoreButtonGroup) {
            return; 
        } 

        if (!isRulesetDirty()) {
            restoreButtonGroup.classList.add('invisible'); 
            return; 
        }
        restoreButtonGroup.classList.remove('invisible'); 
    }

    // restore original ruleset of loaded game 
    function restoreOriginalRuleset() {
        if (!originalRuleset) {
            return; 
        } 

        ruleFields.forEach(fieldName => {
            const select = getField(fieldName); 
            if (!select) {
                return; 
            } 

            const value = originalRuleset[fieldName]; 
            if (value === undefined) {
                return; 
            } 
            setSelectValue(select, value); 
        }); 
        updateRulesetSelection(); 
        updateRestoreButton(); 
    }

    // add eventlistener to ruleset select / switch
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
        updateRestoreButton(); 
    });

    // add eventlistener to rule select / switch 
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
            updateRestoreButton(); 
        });
    });

    // add eventlistener to restore button if it exists 
    if (restoreButton) {
        restoreButton.addEventListener('click', () => {
            restoreOriginalRuleset(); 
        }); 
    }
    updateRulesetSelection();
    updateRestoreButton(); 
}
