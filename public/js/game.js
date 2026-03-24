import { initRenderer, renderLoop } from "./renderer.js";
import { fetchState, rollDice, getAvailableMoves, applyMove, passTurn } from './api.js';
import { updateScene } from './scene.js';

const { game_id, user_id } = window.GAME_CONFIG;

const btnRoll = document.getElementById('btn-roll');
const btnEnd = document.getElementById('btn-end');

let currentState = null;

const canvas = document.getElementById('game_canvas');

initRenderer(canvas);
renderLoop();

// --- Initial State Load ---
async function updateState() {
    try {
        const data = await fetchState(game_id);
        if (data.success) {
            currentState = data.state;
            updateScene(currentState);
            updateHUD();
        }
    } catch (e) {
        console.error('State fetch failed', e);
    }
}

// --- HUD Update ---
function updateHUD() {
    const hudCurrent = document.querySelector('#hud div:nth-child(2)');
    if (currentState && currentState.current_player) {
        hudCurrent.textContent = `<?= Localization::get('game.play.current_player') ?>: ${currentState.current_player.username}`;
    }
}

// --- Roll Dice Button ---
btnRoll.addEventListener('click', async () => {
    const data = await rollDice(game_id);
    if (data.success) {
        currentState = data.state;
        updateScene(currentState);
        updateHUD();
        await showAvailableMoves();
    }
});

// --- End Turn Button ---
btnEnd.addEventListener('click', async () => {
    const data = await passTurn(game_id);
    if (data.success) {
        currentState = data.state;
        updateScene(currentState);
        updateHUD();
    }
});

// --- Available Moves (optional UI for selecting moves) ---
async function showAvailableMoves() {
    const movesData = await getAvailableMoves(game_id);
    if (!movesData.success) return;

    // Simple console output for debug
    console.log('Available moves:', movesData.moves);

    // TODO: Implement click-to-move on board or UI buttons
}

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