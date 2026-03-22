export async function fetchState(game_id) {
    const res = await fetch(`/game/state/${game_id}`);
    console.log(res);
    return await res.json();
}

export async function rollDice(game_id) {
    await fetch('/game/roll', {
        method: 'POST', 
        headers: { 'Content_type': 'application/json' }, 
        body: JSON.stringify({ game_id: game_id})
    });
}

export async function sendMove(game_id, move) {
    await fetch('/game/move', {
        method: 'POST', 
        headers: { 'Content-Type': 'application/json' }, 
        body: JSON.stringify({
            game_id: game_id, 
            move: move
        })
    });
}