import { DIFF_FIGURE_MOVED, DIFF_FIGURE_SPAWNED } from "../core/consts.js";
import { buildFigureId } from "../core/helpers.js";

// SceneState - Create GameState
export function createSceneState(game_state) {
    const scene_state = {
        figures: [], 
        board: {
            main_fields: [], 
            home_fields: {}, 
            goal_fields: {} 
        }, 
        world: {
            theme: null, 
            settings: {} 
        }, 
        visual: {
            meshes: {}, 
            initialized: false 
        }, 
        animations: {
            active: {} 
        } 
    }; 

    game_state.players.forEach(player => { 
        player.figures.forEach(figure => {
            scene_state.figures.push({
                figure_id: buildFigureId(player.player_index, figure.figure_index), 
                player_index: player.player_index, 
                figure_index: figure.figure_index, 
                area: figure.area, 
                position: figure.position, 
                visual: {
                    mesh_id: null, 
                } 
            });
        });
    });

    return scene_state; 
}

// SceneState - Apply diffs between GameState and SceneState to SceneState
export function applyDiffFromGameState(diff, scene_state, systems) {
    for (const event of diff) {
        if (event.type === DIFF_FIGURE_SPAWNED) {
            handleFigureSpawned(event, scene_state, systems);
        } 
        if (event.type === DIFF_FIGURE_MOVED) {
            handleFigureMoved(event, scene_state, systems); 
        }
    }

    return scene_state; 
}

// Helper - Type handler figure spawned
function handleFigureSpawned(event, scene_state, systems) {
    const { figure_id, to } = event; 
    const figure = scene_state.figures.find(fig => fig.figure_id === figure_id); 

    if (!figure) return; 

    // 1. Update SceneState
    figure.area = to.area; 
    figure.position = to.position; 

    // 2. Ensure visuals
    systems.figure_system.createFigure(figure_id, figure, scene_state); 
}

// Helper - Type handler figure moved
function handleFigureMoved(event, scene_state, systems) {
    const { figure_id, from, to } = event; 
    const figure = scene_state.figures.find(fig => fig.figure_id === figure_id);

    if (!figure) return; 

    // 1. Update SceneState
    figure.area = to.area; 
    figure.position = to.position; 

    // 2. Trigger animation
    systems.animation_system.enqueueMove({
        figure_id, 
        from, 
        to 
    });
}