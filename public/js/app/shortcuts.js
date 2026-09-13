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

    // Are game function available
    const gameActive = typeof window.handleRollDice === 'function' && typeof window.handleMoveShortcut === 'function'; 

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

        // Running Game - Roll dice
        case 'enter': 
            if (gameActive) window.handleRollDice(); 
            break;

        // Running Game - Move 1
        case '1': 
            if (gameActive) window.handleMoveShortcut(0); 
            break; 

        // Running Game - Move 1
        case '2': 
            if (gameActive) window.handleMoveShortcut(1); 
            break; 

        // Running Game - Move 1
        case '3': 
            if (gameActive) window.handleMoveShortcut(2); 
            break; 

        // Running Game - Move 1
        case '4': 
            if (gameActive) window.handleMoveShortcut(3); 
            break; 
    }
}

// Handle Escape
function handleEscapeShortcut() {

    // Modal handling will be added here
    if (closeModal()) {
        return;
    } 

    // Game menu handling will be added here
    if (typeof toggleMenu === 'function') {
        toggleMenu(); 
    }
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
