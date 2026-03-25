import { initRenderer, renderLoop } from "./renderer.js";
import { fetchState, rollDice, getAvailableMoves, applyMove, passTurn } from './api.js';
import { updateScene } from './scene.js';

const { game_id, user_id } = window.GAME_CONFIG;

const btnRoll = document.getElementById('btn-roll');
const btnEnd = document.getElementById('btn-end');

let current_state = null;

const canvas = document.getElementById('game_canvas');

initRenderer(canvas);
renderLoop();

// --- Initial State Load ---
async function updateState() {
    try {
        const data = await fetchState(game_id);
        if (data.success) {
            current_state = data.state;
            updateScene(current_state);
            updateHUD();
            updateDice();
            showAvailableMoves();
        }
    } catch (e) {
        console.error('State fetch failed', e);
    }
}
// --- HUD --- 
// --- Update HUD ---
function updateHUD() {
    const hudCurrent = document.querySelector('#hud div:nth-child(2)');
    if (current_state && current_state.current_player) {
        hudCurrent.textContent = `<?= Localization::get('game.play.current_player') ?>: ${current_state.current_player.username}`;
    }
}

// --- DICE ---
// --- Update Dice ---
function updateDice() {
    const dice_element = document.getElementById('dice');
    const dice_value_element = document.getElementById('dice-value');

    if (!dice_element || !dice_value_element || !current_state) return;

    const dice_value = current_state.current_dice_roll;

    if (dice_value === null) {
        dice_value_element.textContent = '-';
        dice_element.textContent = '';
        return;
    }

    // Animation
    dice_element.classList.add('roll');

    setTimeout(() => {
        dice_element.classList.remove('roll'); 

        const dice_faces = ['⚀','⚁','⚂','⚃','⚄','⚅']; 
        dice_element.textContent = dice_faces[dice_value - 1];
        dice_value_element.textContent = dice_value;
    }, 400);
}

// --- MOVES ---
// --- Available Moves (optional UI for selecting moves) ---
async function showAvailableMoves() {
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

    moves.forEach(move => {
        const btn = document.createElement('button');
        btn.classList.add('move-item');
        btn.textContent = `Figure ${move.figure_index}  → ${move.to.area}:${move.to.position}`;
        btn.addEventListener('click', async () => { 
            //console.log('move clicked');
            //console.log(move);
            await handleMove(move); 
        });
        moves_container.appendChild(btn);
    });
}

// --- Move Handling ---
async function handleMove(move) {
    const data = await applyMove(game_id, move);
    //console.log(data);

    if (data.success) {
        current_state = data.state;
        updateScene(current_state);
        updateHUD();
        updateDice();

        // Load new moves after executing current move
        await showAvailableMoves();
    }
}

// --- EVENT LISTENER ---
// --- Roll Dice Button ---
btnRoll.addEventListener('click', async () => {
    const data = await rollDice(game_id);
    //console.log(data);
    if (data.success) {
        current_state = data.state;
        updateScene(current_state);
        updateHUD();
        updateDice(); 
        await showAvailableMoves();
    }
});

/*
// --- End Turn Button ---
btnEnd.addEventListener('click', async () => {
    const data = await passTurn(game_id);
    if (data.success) {
        current_state = data.state;
        updateScene(current_state);
        updateHUD();
    }
});
*/

// --- Polling / Auto-Update ---
//setInterval(updateState, 2000);

// --- First Load ---
updateState();

/*
import { initRenderer, renderLoop } from "./renderer.js";
import { updateScene } from "./scene.js";
import { fetchState, rollDice } from "./api.js";

const canvas = document.getElementById('game_canvas');
const { game_id } = window.GAME_CONFIG; 

initRenderer(canvas);
renderLoop();

async function update() {
    try {
        const data = await fetchState(game_id);

        console.log(data);

        if (data.success) {
            updateScene(data.state);
        }
    } catch (e) {
        console.error('State fetch failed', e);
    }
}

// Polling 
//setInterval(update, 2000);

// First Load
update(); 

// DEBUG Button - temporary
window.rollDice = () => rollDice(game_id);
*/