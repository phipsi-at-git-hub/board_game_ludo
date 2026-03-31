import { setTheme } from "./theme_manager.js";
import { theme_candy } from "./themes/theme_candy.js";
import { initRenderer, renderLoop, animateCameraTo, camera } from "./renderer.js";
import { fetchState, rollDice, getAvailableMoves, applyMove, passTurn } from './api.js';
import { updateScene, updateCameraTarget, setCameraMode, getCameraTarget } from './scene.js';

const { game_id, user_id } = window.GAME_CONFIG;

const canvas = document.getElementById('game_canvas');
const btn_roll = document.getElementById('btn-roll');
const btn_menu = document.getElementById('btn-menu');
const btn_resume = document.getElementById('btn-resume');
const btn_exit = document.getElementById('btn-exit');
const menu_overlay_element = document.getElementById('menu-overlay');
const menu_settings_camera_toggle_item = document.getElementById('settings-camera-toggle');

const my_player_index = window.GAME_CONFIG.user_player_index;
let old_player_index = null;
let settings_camera_mode = 'fixed';

// Colors
const PLAYER_COLORS = {
    0: '#ff4444', 
    1: '#4444ff', 
    2: '#44ff44', 
    3: '#ffff44' 
};

let current_state = null;
let last_dice_value = null;

setTheme(theme_candy); 
initRenderer(canvas);
animateCameraTo(my_player_index, 2000);
renderLoop();

// --- Initial State Load ---
async function updateState() {
    try {
        const data = await fetchState(game_id);
        if (data.success) {
            current_state = data.state;
            if (old_player_index === null || current_state.current_player_index !== old_player_index) {
                old_player_index = current_state.current_player_index;
            }
            if (settings_camera_mode === 'follow_turn') {
                updateCameraTarget(current_state, user_id);
            }
            updateScene(current_state);
            updateHUD();
            updateDice();
            updateControls();
            if (current_state.current_dice_roll) showAvailableMoves();
        }
    } catch (e) {
        console.error('State fetch failed', e);
    }
}
// --- HUD --- 
// --- Update HUD ---
function updateHUD() {
    const hud_current_player = document.getElementById('current-username');
    if (current_state && current_state.current_player_username) {
        hud_current_player.textContent = current_state.current_player_username; 
        hud_current_player.style.color = isMyTurn() ? PLAYER_COLORS[current_state.current_player_index] : '#ffffff';
    }
}

// --- DICE ---
// --- Update Dice ---
function updateDice() {
    const dice_container = document.getElementById('dice-container');
    const dice_element = document.getElementById('dice');
    const dice_value_element = document.getElementById('dice-value');

    if (!dice_container || !dice_element || !dice_value_element || !current_state) return;

    const dice_value = current_state.current_dice_roll;

    if (dice_value === null) {
        dice_container.style.display = 'none'; 
        dice_container.style.opacity = '0';
        last_dice_value = null;
        return;
    }

    dice_container.style.display = 'flex'; 

    requestAnimationFrame(() => {
        dice_container.style.opacity = '1'; 
    });

    // Animation
    if (dice_value !== last_dice_value) {
        dice_element.classList.add('roll');

        setTimeout(() => {
            dice_element.classList.remove('roll'); 
        }, 400);
    }

    const dice_faces = ['⚀','⚁','⚂','⚃','⚄','⚅']; 
    dice_element.textContent = dice_faces[dice_value - 1];
    dice_value_element.textContent = dice_value;
    last_dice_value = dice_value;
}

// --- Update Controls ---
function updateControls() {
    const my_turn = isMyTurn();
    if (my_turn && !current_state.current_dice_roll) {
        btn_roll.disabled = false;
        btn_roll.classList.remove('inactive');
    } else {
        btn_roll.disabled = true;
        btn_roll.classList.add('inactive');
    }
}

// --- MOVES ---
// --- Available Moves (optional UI for selecting moves) ---
async function showAvailableMoves() {
    if (!isMyTurn()) return;
    const moves_data = await getAvailableMoves(game_id);
    if (!moves_data.success) return;

    // Simple console output for debug
    //console.log('Available moves:', moves_data.moves);

    renderMoves(moves_data.moves);
}

// --- Moves UI ---
function renderMoves(moves) {
    const moves_container = document.getElementById('moves-container');
    if (!moves_container) return;

    moves_container.innerHTML = '';

    if (!moves || moves.length === 0) return;
    if (!isMyTurn()) return;

    moves.forEach(move => {
        if (move.is_pass === true) {} else {}
        const btn = document.createElement('button');
        btn.classList.add('move-item');
        btn.style.background = (move.is_kick === true) ? PLAYER_COLORS[move.kicked_player_index] : '';
        btn.textContent = (move.is_pass) ? 'pass' : `Figure ${move.figure_index}: ${move.from.area}:${move.from.position} → ${move.to.area}:${move.to.position}`;
        btn.addEventListener('click', async () => { 
            await handleMove(move); 
        });
        moves_container.appendChild(btn);
    });
}

function resetMoves() {
    const moves_container = document.getElementById('moves-container');
    while (moves_container.firstChild) {
        moves_container.removeChild(moves_container.lastChild);
    }
}

// --- Move Handling ---
async function handleMove(move) {
    const data = await applyMove(game_id, move);

    if (data.success) {
        current_state = data.state;
        updateScene(current_state);
        updateHUD();
        updateDice();
        resetMoves();

        // Load new moves after executing current move
        await showAvailableMoves();
    }
}

// --- Helper ---
function isMyTurn() {
    if (!current_state) return false;
    return current_state.current_player_id === user_id;
}

function openMenu() {
    menu_overlay_element.classList.add('visible');
}
function closeMenu() {
    menu_overlay_element.classList.remove('visible');
}

// --- EVENT LISTENER ---
// Roll Dice Button 
btn_roll.addEventListener('click', async () => {
    if (!isMyTurn()) return;

    const data = await rollDice(game_id);
    if (data.success) {
        current_state = data.state;
        updateScene(current_state);
        updateHUD();
        updateDice(); 
        await showAvailableMoves();
    }
});

// Open Menu button
btn_menu.addEventListener('click', () => {
   openMenu();
});

// Close Menu button
btn_resume.addEventListener('click', () => {
   closeMenu();
});

// Close the game menu
menu_overlay_element.addEventListener('click', (e) => {
    if (e.target === menu_overlay_element) {
        closeMenu();
    }
});

// Set camera mode
menu_settings_camera_toggle_item.addEventListener('change', () => {
    settings_camera_mode = menu_settings_camera_toggle_item.checked ? 'follow_turn' : 'fixed';
    localStorage.setItem('settings_camera_mode', settings_camera_mode);

    // Update Scene
    setCameraMode(settings_camera_mode);
    if (current_state) {
        updateCameraTarget(current_state, user_id);
    }
});

// Close / Leave the game
btn_exit.addEventListener('click', () => {
    closeMenu();
    setTimeout(function() {
        window.location.href = `../detail/${window.GAME_CONFIG.game_id}`;
    }, 300);
});

// --- Polling / Auto-Update ---
setInterval(updateState, 2000);

// --- First Load ---
updateState();