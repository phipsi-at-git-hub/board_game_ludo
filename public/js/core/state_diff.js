import { buildFigureId } from "./helpers.js";
import { DIFF_FIGURE_SPAWNED, DIFF_FIGURE_MOVED } from "./consts.js";

// 
export function computeDiff(game_state, scene_state) {
    const diff = []; 
    const game_figures = extractFiguresFromGameState(game_state); 
    const scene_figures = extractFiguresFromSceneState(scene_state); 

    game_figures.forEach(game_figure => {
        const scene_figure = scene_figures.find(fig => fig.figure_id === game_figure.figure_id); 

        // Figure spawned
        if (!scene_figure) {
            diff.push({
                type: DIFF_FIGURE_SPAWNED, 
                figure_id: game_figure.figure_id, 
                to: {
                    area: game_figure.area, 
                    position: game_figure.position 
                } 
            });
        }

        // Figure moved 
        if (scene_figure.area !== game_figure.area || scene_figure.position !== game_figure.position) {
            diff.push({
                type: DIFF_FIGURE_MOVED, 
                figure_id: game_figure.figure_id, 
                from: {
                    area: scene_figure.area, 
                    position: scene_figure.position 
                }, 
                to: {
                    area: game_figure.area, 
                    position: game_figure.position 
                } 
            });
        }
    });

    return diff; 
}

// Helper - Extract Figures from GameState
function extractFiguresFromGameState(game_state) {
    const figures = []; 

    game_state.players.forEach(player => { 
        player.figures.forEach(figure => {
            figures.push({
                figure_id: buildFigureId(player.player_index, figure.figure_index), 
                player_index: player.player_index, 
                figure_index: figure.figure_index, 
                area: figure.area, 
                position: figure.position
            });
        });
    });

    return figures; 
} 

// Helper - Extract Figures from SceneState
function extractFiguresFromSceneState(scene_state) {
    const figures = [];

    Object.values(scene_state.figures).forEach(figure => {
        const figure_id = figure.figure_id; 

        figures.push({
            figure_id: figure_id, 
            figure_index: figure.figure_index, 
            player_index: figure.player_index, 
            area: figure.area, 
            position: figure.position 
        }); 
    });

    return figures;
} 