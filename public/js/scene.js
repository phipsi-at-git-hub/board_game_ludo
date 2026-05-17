//import * as THREE from 'https://unpkg.com/three/build/three.module.js';
import * as THREE from "three";
import { scene } from './renderer.js';
import { 
    themeCreateFigure, 
    themeGetPlayerColors, 
    themeGetFieldOffsets 

} from './theme_manager.js';
import { initBoard, board_state } from "./systems/board.js";
import { calculateHopHeight, calculateHopHeightAtMoment } from "./core/math.js";
import { 
    getPositionBaseHeight, 
    getLastPositionBeforeGoal

} from "./core/movement.js";
import { 
    AREA_HOME, 
    AREA_FIELD, 
    AREA_GOAL, 
    HOP_TIME, 
    BASE_HEIGHT
} from "./core/consts.js";
import { applyDiffFromGameState, createSceneState } from "./state/scene_state.js";
import { computeDiff } from "./core/state_diff.js";

let scene_state_initialized = false;
let scene_state;
let state_diff;

const figure_meshes = {};
const figure_animations = {}; 
let board_initialized = false; 

// Update Scene
export function updateScene(state) {
    // Initialize Board
    if (!board_initialized) {
        initBoard(scene);
        board_initialized = true;
    }

    // Initialize SceneState
    if (!scene_state_initialized) {
        scene_state_initialized = true;
        scene_state = createSceneState(state);
    }

    // Check for diff between GameState and SceneState
    state_diff = computeDiff(state, scene_state);
    //console.log(state_diff);
    scene_state = applyDiffFromGameState(state_diff, scene_state, null);
    //console.log(scene_state);

    // Place Figures and handle Figure animations
    placeFigures(state);
}

// Position Figures
function placeFigures(state) {
    const PLAYER_COLORS = themeGetPlayerColors();
    const FIELD_OFFSETS = themeGetFieldOffsets();
    const occupancy = {};

    // Group Figures by area
    state.players.forEach(player => {
        player.figures.forEach(figure => {
            let key;
            if (figure.area === AREA_HOME) key = `home_${player.player_index}_${figure.position}`;
            else if (figure.area === AREA_GOAL) key = `goal_${player.player_index}_${figure.position}`;
            else key = `field_${figure.position}`;

            occupancy[key] ??= [];
            occupancy[key].push({ player, figure });
        });
    });

    // Detect visual transitions (field vacated and occupied in the same frame)
    const transition_map = {};
    Object.values(figure_meshes).forEach(mesh => {
        if (!mesh.userData) return;

        const key = `${mesh.userData.last_area}_${mesh.userData.last_position}`;
        transition_map[key] = true;
    });

    // Position Figures
    Object.values(occupancy).forEach(group => {
        group.forEach((entry, i) => {
            const { player, figure } = entry;
            const mesh_key = player.player_index + '-' + figure.figure_index;

            if (!figure_meshes[mesh_key]) {
                const mesh = createFigure(PLAYER_COLORS[player.player_index]);
                scene.add(mesh);
                figure_meshes[mesh_key] = mesh;
                mesh.position.set(0, 0.5, 0);

                // Initial state
                mesh.userData.last_position = figure.position;
                mesh.userData.last_area = figure.area;
                mesh.userData.is_animating = false;
            }

            const mesh = figure_meshes[mesh_key];

            // Get root position of fields
            let basePos;
            if (figure.area === AREA_HOME) basePos = board_state.homeFields[player.player_index][figure.position].position;
            else if (figure.area === AREA_GOAL) basePos = board_state.goalFields[player.player_index][figure.position].position;
            else basePos = board_state.mainFields[figure.position].position;

            // Only use offset if multiple figures occupy the same position
            const offset = group.length > 1 ? FIELD_OFFSETS[i % FIELD_OFFSETS.length] : { x: 0, z: 0 };

            const target_position = new THREE.Vector3(
                basePos.x + offset.x, 
                BASE_HEIGHT, 
                basePos.z + offset.z 
            );

            // Animate
            if (!mesh.initialized) {
                // Initiate
                mesh.position.copy(target_position);
                mesh.initialized = true;
                mesh.userData.last_position = figure.position;
                mesh.userData.last_area = figure.area;
            } else if (!mesh.userData.is_animating && (mesh.userData.last_position !== figure.position || mesh.userData.last_area !== figure.area)) {
                // Only start animation if no animation is already running
                const path = getPathPositions(mesh, figure, player, offset, transition_map);
                if (path.length > 0) {
                    path.unshift({
                        position: mesh.position.clone(), 
                        index: mesh.userData.last_position, 
                        area: mesh.userData.last_area, 
                        // jump_over: false
                    });
                    mesh.userData.is_animating = true; 
                    figure_animations[mesh_key] = {
                        path, 
                        current_step: 0, 
                        progress: 0, 
                        step_duration: HOP_TIME 
                    };
                }
            }
        });
    });
}

// Animations - Update Figure animations
export function updateAnimations(delta_time) {
    Object.entries(figure_animations).forEach(([key, animation]) => {
        const mesh = figure_meshes[key];
        //if (!mesh || !animation.path || animation.path.length === 0) {
        if (!mesh || !animation.path || animation.path.length < 2) {
            delete figure_animations[key]; 
            if (mesh) mesh.userData.is_animating = false;
            return;
        }

        // First step in path is always the position the mesh is at -> no animations needed
        if (animation.current_step === 0) {
            mesh.position.copy(animation.path[0].position);
            animation.current_step++;
            return;
        }

        const current = animation.path[animation.current_step]; 
        const previous = animation.current_step === 0 ? current : animation.path[animation.current_step -1];

        // Prevent overlap on transition fields
        if (current.has_transition_conflict && animation.progress === 0) {
            // short delay
            animation.progress -= 0.05; 
        }

        animation.progress += delta_time / animation.step_duration; 
        if (animation.progress > 1) animation.progress = 1;

        mesh.position.lerpVectors(previous.position, current.position, animation.progress); 
        const y_heights = calculateHopHeight(previous.position.y, getPositionBaseHeight(current.area, current.index, figure_meshes));
        mesh.position.y = calculateHopHeightAtMoment(y_heights.y_start, y_heights.y_end, y_heights. y_top, animation.progress);

        // Animation done
        if (animation.progress >= 1) {
            animation.progress = 0;
            animation.current_step++;

            if (animation.current_step >= animation.path.length) {
                mesh.position.copy(current.position);
                mesh.position.y = 0.5; // Reset

                // Update state
                mesh.userData.last_position = current.index;
                mesh.userData.last_area = current.area;
                mesh.userData.is_animating = false;

                delete figure_animations[key];
            }
        }
    });
}

// Helper - Get path positions for figure animation
function getPathPositions(mesh, figure, player, offset, transition_map) {
    const path = [];
    const last_area = mesh.userData.last_area;
    const last_position = mesh.userData.last_position; 
    const player_index = player.player_index;

    // Helper - push step to path
    const pushStep = (position, index, area = AREA_FIELD) => {
        path.push({
            position: new THREE.Vector3(
                position.x + offset.x, 
                getPositionBaseHeight(area, index, figure_meshes), 
                position.z + offset.z
            ), 
            index, 
            area 
        });
    };

    // Field -> Field
    if (last_area === AREA_FIELD && figure.area === AREA_FIELD) {
        let current = last_position;

        while (current !== figure.position) {
            current = (current + 1) % board_state.mainFields.length;
            const position = board_state.mainFields[current].position.clone();
            pushStep(position, current);
        }
    }

    // Field -> Goal
    else if (last_area === AREA_FIELD && figure.area === AREA_GOAL) {
        let current = last_position;
        const last_position_before_goal_entry = getLastPositionBeforeGoal(player_index);
        const goal_start_index = 0;

        // Walking to end of field
        while (current !== last_position_before_goal_entry) {
            current = (current + 1) % board_state.mainFields.length;
            const position = board_state.mainFields[current].position.clone();
            pushStep(position, current);
        }

        // Walking in goal area
        for (let i = goal_start_index; i <= figure.position; i++) {
            const goal_field = board_state.goalFields[player_index][i];
            if (!goal_field) continue;

            const position = goal_field.position.clone();
            pushStep(position, i, AREA_GOAL);
        }
    }

    // Goal -> Goal 
    else if (last_area === AREA_GOAL && figure.area === AREA_GOAL) {
        let current = last_position;
        for (let i = current + 1; i <= figure.position; i++) {
            const goal_field = board_state.goalFields[player_index][i]; 
            if (!goal_field) continue;

            const position = goal_field.position.clone(); 
            pushStep(position, i, AREA_GOAL);
        }
    }

    // Home -> Field
    else if (last_area === AREA_HOME && figure.area === AREA_FIELD) {
        const position = board_state.mainFields[figure.position].position.clone();
        pushStep(position, figure.position);
    }

    // Field -> Home
    else if (last_area === AREA_FIELD && figure.area === AREA_HOME) {
        const position = board_state.homeFields[player.player_index]?.[figure.position]?.position;
        if (position) {
            pushStep(position, figure.position, AREA_HOME);
        }
    }

    // Mark potential transition targets
    for (let i = 0; i < path.length; i++) {
        const step = path[i];
        const key = `${step.area}_${step.index}`;

        if (transition_map?.[key]) {
            step.has_transition_conflict = true;
        }
    }

    return path;
}

// Helper - Get mesh at given position in area
function getFieldMeshAt(area, position) {
    if (area === AREA_HOME) return board_state.homeFields[position];
    if (area === AREA_FIELD) return board_state.mainFields[position];
    if (area === AREA_GOAL) return board_state.goalFields[position];
    return null;
}

// Helper - Get Figure mesh at given position in area
function getFigureMeshAt(area, position) {}

// Helper - Create Figure
function createFigure(color) {
   return themeCreateFigure(color);
} 