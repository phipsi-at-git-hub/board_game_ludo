// Initialize keyboard shortcuts
function initKeyboardShortcuts() {
    document.addEventListener("keydown", handleKeyboardShortcut);
}

// Handle keyboard shortcuts
function handleKeyboardShortcut(event) {
    // Ignore shortcuts while entering text
    if (isKeyboardInput(event.target)) {
        return;
    }

    // Escape
    if (event.key === "Escape") {
        handleEscapeShortcut();
        return;
    }

    // Ignore modified shortcuts
    if (event.ctrlKey || event.metaKey || event.altKey || event.shiftKey) {
        return;
    }

    switch (event.key.toLowerCase()) {
        // Home / Lobby
        case "h":
            redirect("/");
            break;

        // User profile
        case "p":
            redirect("/account");
            break;

        // Admin dashboard
        case "a":
            redirect("/admin");
            break;
    }
}

// Handle Escape
function handleEscapeShortcut() {

    // Modal handling will be added here
    if (closeModal()) {
        return;
    } 

    if (typeof toggleMenu === 'function') {
        toggleMenu(); 
    }

    // Game menu handling will be added here
}

// Check whether the target is an editable element
function isKeyboardInput(target) {
    if (!(target instanceof HTMLElement)) {
        return false;
    }

    return target.matches(
        "input, textarea, select, [contenteditable='true']"
    );
}
