import { initRenderer, renderLoop } from "./renderer.1774106304.js";
import { updateScene } from "./scene.1774112480.js";
import { fetchState, rollDice } from "./api.1774206302.js";

const canvas = document.getElementById('game_canvas');
const { game_id } = window.GAME_CONFIG; 

initRenderer(canvas);
renderLoop();

async function update() {
    try {
        const data = await fetchState(game_id);

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