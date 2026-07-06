async function requestGet(url, json = true) {
    try {
        const res = await fetch(url);
        const text = (json === true) ? await res.json() : await res.text();
        return text;
    } catch (e) {
        console.error('[GET Error]', e);
    }
}

async function requestPost(url,  data = {}, json = true) {
    const CSRF_TOKEN = window.GAME_CONFIG._csrf_token;
    const GAME_ID = window.GAME_CONFIG.game_id;
    try {
        const form_data = new URLSearchParams();
        for (const key in data) {
            form_data.append(key, data[key]);
        }
        form_data.append('_csrf_token', CSRF_TOKEN);
        //form_data.append('game_id', GAME_ID);

        const res = await fetch(url, {
            method: 'POST', 
            body: form_data
        });

        const text = (json === true) ? await res.json() : await res.text();
        return text;
    } catch (e) {
        console.error('[POST Response]', e)
    }
}

function encodeFormData(data) {
    return Object.keys(data).map(key =>encodeURIComponent(key) + '=' + encodeURIComponent(data[key])).join('&');
}

export async function fetchState(game_id) {
    return await requestPost('/api/game-engine/state', { game_id });
}

export async function rollDice(game_id) {
    return await requestPost('/api/game-engine/roll_dice', { game_id });
}

export async function getAvailableMoves(game_id) {
    return await requestPost('/api/game-engine/get_available_moves', { game_id });
}

export async function applyMove(game_id, move) {
    return await requestPost('/api/game-engine/apply_move', {
        game_id, 
        move: asJSON(move)
    });
}

export async function passTurn(game_id) {
    return await requestPost('/api/game-engine/pass_turn', { game_id });
}

// Helper 
function asJSON(data) {
    return JSON.stringify(data);
}