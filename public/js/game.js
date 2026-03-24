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