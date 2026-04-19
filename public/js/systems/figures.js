//import * as THREE from "three"; 
import { addFigureMesh, getFigureMesh, hasFigureMesh } from "../entities/figure_store.js";
import { themeGetPlayerColors, themeGetFieldOffsets, themeCreateFigure } from "../theme_manager.js";
import { AREA_HOME, AREA_GOAL, BASE_HEIGHT } from "../core/consts.js";

export function syncFigures(scene_state, board_state, scene) {
    const PLAYER_COLORS = themeGetPlayerColors(); 
    const FIELD_OFFSETS = themeGetFieldOffsets(); 
    const occupancy = {}; 

    // Group Figures by area
    scene_state.figures.forEach(figure => {
        let key; 

        if (figure.area === AREA_HOME) {
            key = `home_${figure.player_index}_${figure.position}`; 
        } else if (figure.area === AREA_GOAL) {
            key = `goal_${figure.player_index}_${figure.position}`; 
        } else {
            key = `field_${figure.position}`; 
        }

        occupancy[key] ??= [];
        //occupancy[key].push({ player, figure });

        occupancy[key].push(figure); 
    });

    // Position Figures
    Object.keys(occupancy).forEach(key => {
        const group = occupancy[key]; 

        group.forEach((figure, index) => {
            if (!hasFigureMesh(figure.figure_id)) {
                const mesh = themeCreateFigure(PLAYER_COLORS[figure.player_index]);
                scene.add(mesh); 
                addFigureMesh(figure.figure_id, mesh); 
            }

            const mesh = getFigureMesh(figure.figure_id); 

            // Get root position of fields
            let base_position; 
            if (figure.area === AREA_HOME) {
                base_position = board_state.homeFields[figure.player_index][figure.position].position; 
            } else if (figure.area === AREA_GOAL) {
                base_position = board_state.goalFields[figure.player_index][figure.position].position; 
            } else {
                base_position = board_state.mainFields[figure.position].position; 
            }
            
            // Only use offset if multiple figures occupy the same position
            const offset = group.length > 1 ? FIELD_OFFSETS[i % FIELD_OFFSETS.length] : { x: 0, z: 0 };

            mesh.position.set(
                base_position.x + offset.x, 
                BASE_HEIGHT, 
                base_position.z + offset.z 
            );
        });
    }); 
}