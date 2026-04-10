//import * as THREE from 'https://unpkg.com/three/build/three.module.js';
import * as THREE from "three";
import { scene, camera } from './renderer.js';
import { 
    themeCreateBox, 
    themeCreateFigure, 
    themeGetBackground, 
    themeGetPlayerColors, 
    themeGetCellSize, 
    themeGetFieldOffsets, 
    themeGetBoard, 
    themeCreateBoardGround, 
    themeGetAssets, 
    themeGetWinAssets, 
    themeStartWinAnimation

} from './theme_manager.js';

const AREA_HOME = "home"; 
const AREA_FIELD = "field"; 
const AREA_GOAL = "goal"; 

const CAMERA_MODE_FIXED = 'fixed';
const CAMERA_MODE_FOLLOW = 'follow_turn';
const CAMERA_RADIUS = 7;  // Distance to board center
const CAMERA_HEIGHT = 10; // Height above board
const CAMERA_TILT = -8 * Math.PI / 180;
const BASE_ANGLES = [
    3 * Math.PI/2 + CAMERA_TILT, // player 0
    Math.PI + CAMERA_TILT, // player 1
    Math.PI / 2 + CAMERA_TILT, // player 2
    0 + CAMERA_TILT, // player 3
];
const HOP_TIME = 0.5; // Time to animate one figure hop / jump in ms
const BASE_HEIGHT = 0.5;
const FIGURE_HEIGHT = 1;
const BASE_JUMP_HEIGHT = 0.25;

const figure_meshes = {};
const figure_animations = {}; 
let board_initialized = false;
let camera_target_position = new THREE.Vector3();
let camera_mode = CAMERA_MODE_FIXED;

let win_assets_loaded = false;
let win_assets = null; 

// Field Storage
const mainFields = new Array(40);
const homeFields = {};
const goalFields = {};

// Update Scene
export function updateScene(state) {
    if (!board_initialized) {
        initBoard();
        board_initialized = true;
    }

    placeFigures(state);
}

// Update Camera target
export function updateCameraTarget(state, user_id) {
    if (!state || !state.players) return;

    let player_index; 
    if (camera_mode === CAMERA_MODE_FOLLOW) {
        // Active player
        player_index = state.current_player_index;
    } else {
        // Fixed player is the user itself 
        const player_me = state.players.find(p => p.user_id === user_id);
        if (!player_me) return;
        player_index = player_me.player_index;
    }

    const angle = BASE_ANGLES[player_index] ?? 0;
    
    camera_target_position.set(
        Math.sin(angle) * CAMERA_RADIUS, 
        CAMERA_HEIGHT, 
        Math.cos(angle) * CAMERA_RADIUS
    );
}

// Initialize camera target after entering the scene
export function getInitialCameraTarget(player_index) {
    const angle = BASE_ANGLES[player_index]?? 0;

    camera_target_position.set(
        Math.sin(angle) * CAMERA_RADIUS, 
        CAMERA_HEIGHT, 
        Math.cos(angle) * CAMERA_RADIUS
    );

    return camera_target_position.clone();
}

// Initialize Board
function initBoard() {
    // Set background color of scene
    scene.background = new THREE.Color(themeGetBackground());

    // Set background color of window
    document.body.style.backgroundColor = "#" + themeGetBackground().toString(16).padStart(6, "0"); 

    // Create Board Ground
    scene.add(themeCreateBoardGround());


    // Get Board properties from theme
    const CELL_SIZE = themeGetCellSize();
    const BOARD = themeGetBoard();

    if (!BOARD) {
        console.error("No board defined in theme!");
    }

    // Calculate offsets
    const offsetX = (BOARD[0].length - 1) / 2;
    const offsetZ = (BOARD.length -1 ) / 2;

    // Add game cells on board
    BOARD.forEach((row, zIndex) => {
        row.forEach((cell, xIndex) => {
            if (cell === "-") return;

            const worldX = (xIndex - offsetX) * CELL_SIZE;
            const worldZ = (zIndex - offsetZ) * CELL_SIZE;

            createCell(cell, worldX, worldZ);
        });
    });

    // Add assets on board
    const theme_assets = themeGetAssets();
    theme_assets.forEach(asset => {
        const mesh = asset.mesh;

        if (!mesh) return;

        // Set assets position
        mesh.position.set(
            asset.position.x, 
            asset.position.y || 0, 
            asset.position.z 
        );

        // Set assets rotation
        if (asset.rotation) {
            mesh.rotation.y = asset.rotation.y;
        }

        // Set assets scale
        if (asset.scale) {
            mesh.scale.setScalar(asset.scale);
        }

        scene.add(mesh);
    });
}

// Create Cells
function createCell(cell, x, z) {
    const PLAYER_COLORS = themeGetPlayerColors();

    // HOME
    if (cell.startsWith("H")) {
        const [player, index] = parsePlayerIndex(cell);
        const mesh = createBox(PLAYER_COLORS[player], 0.8, 'home');
        mesh.position.set(x, 0.05, z);
        scene.add(mesh);

        homeFields[player] ??= [];
        homeFields[player][index] = mesh;
        return;
    }

    // GOAL
    if (cell.startsWith("G")) {
        const [player, index] = parsePlayerIndex(cell);
        const mesh = createBox(PLAYER_COLORS[player], 0.8, AREA_GOAL);
        mesh.position.set(x, 0.05, z);
        scene.add(mesh);

        goalFields[player] ??= [];
        goalFields[player][index] = mesh;
        return;
    }

    // FIELD
    if (cell.startsWith("F")) {
        let pos = parseInt(cell.split('-')[1]);
        const index = parseInt(cell.split("-")[1]);
        const mesh = (pos === 0 || pos === 10 || pos === 20 || pos === 30) ? createBox(PLAYER_COLORS[pos/10], 1, "start") : createBox(0xdddddd, 0.9);
        mesh.position.set(x, 0.05, z);
        scene.add(mesh);

        mainFields[index] = mesh;
        return;
    }
}

// Helper - Create Box
function createBox(color, scale = 1, type = null) {
   return themeCreateBox(color, scale, type);
}

// Parse player_index
function parsePlayerIndex(str) {
    const match = str.match(/[HG](\d+)-(\d+)/);
    return [parseInt(match[1]), parseInt(match[2])];
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
            if (figure.area === AREA_HOME) basePos = homeFields[player.player_index][figure.position].position;
            else if (figure.area === AREA_GOAL) basePos = goalFields[player.player_index][figure.position].position;
            else basePos = mainFields[figure.position].position;

            // Only use offset if multiple figures occupy the same position
            const offset = group.length > 1 ? FIELD_OFFSETS[i % FIELD_OFFSETS.length] : { x: 0, z: 0 };

            const target_position = new THREE.Vector3(
                basePos.x + offset.x, 
                0.5, 
                basePos.z + offset.z 
            );

            // Calculate path
            //const path = getPathPositions(mesh, figure, player, offset);

            // Animate
            if (!mesh.initialized) {
                // Initiate
                mesh.position.copy(target_position);
                mesh.initialized = true;
                mesh.userData.last_position = figure.position;
                mesh.userData.last_area = figure.area;
            } else if (!mesh.userData.is_animating && (mesh.userData.last_position !== figure.position || mesh.userData.last_area !== figure.area)) {
                // Only start animation if no animation is already running
                const path = getPathPositions(mesh, figure, player, offset);
                if (path.length > 0) {
                    path.unshift({
                        position: mesh.position.clone(), 
                        index: mesh.userData.last_position, 
                        area: mesh.userData.last_area, 
                        jump_over: false
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

// Update Figure animations
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

        animation.progress += delta_time / animation.step_duration; 
        if (animation.progress > 1) animation.progress = 1;

        mesh.position.lerpVectors(previous.position, current.position, animation.progress); 

        // Hopping effect
        //const height = (!previous.position.equals(current.position)) ? Math.sin(animation.progress * Math.PI) * 0.25 : 0;
        //const height = Math.sin(animation.progress * Math.PI) * 0.25;
        //mesh.position.y = 0.5 + height;

        //const y_start = previous.position.y;
        //const y_end = current.position.y;
        //const y_top = Math.max(y_start, y_end) + 0.25;
        //const y_heights = calculateHopHeight(current, getNextPathStep(animation, animation.current_step));
        const y_heights = calculateHopHeight(previous, current);
        //console.log(y_heights);
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
function getPathPositions(mesh, figure, player, offset) {
    const path = [];
    const last_area = mesh.userData.last_area;
    const last_position = mesh.userData.last_position; 
    const player_index = player.player_index;

    // Field -> Field
    if (last_area === AREA_FIELD && figure.area === AREA_FIELD) {
        let current = last_position;

        while (current !== figure.position) {
            current = (current + 1) % mainFields.length;

            const position = mainFields[current].position.clone();
            path.push({
                position: new THREE.Vector3(
                    position.x + offset.x, 
                    0.5, 
                    position.z + offset.z
                ),
                index: current, 
                area: AREA_FIELD, 
                jump_over: isPositionOccupiedByOtherMesh(mesh, AREA_FIELD, current) 
            });
        }
    }

    // Field -> Goal
    else if (last_area === AREA_FIELD && figure.area === AREA_GOAL) {
        let current = last_position;
        let last_position_before_goal_entry = getLastPositionBeforeGoal(player_index);

        // Walking to end of field
        while (current !== last_position_before_goal_entry) {
            current = (current + 1) % mainFields.length;

            const position = mainFields[current].position.clone();
            path.push({
                position: new THREE.Vector3(
                    position.x + offset.x, 
                    0.5, 
                    position.z + offset.z 
                ), 
                index: current, 
                area: AREA_FIELD 
            }); 
        }

        // Jump into goal area
        const goal_entry = goalFields[player_index][0];
        if (goal_entry) {
            const position = goal_entry.position.clone();
            path.push({
                position: new THREE.Vector3(
                    position.x + offset.x, 
                    0.5, 
                    position.z + offset.z 
                ), 
                index: 0, 
                area: AREA_GOAL 
            });
        }

        // Walking in goal area
        for (let i = 0; i <= figure.position; i++) {
            const goal_field = goalFields[player_index][i];
            if (!goal_field) continue;

            const position = goal_field.position.clone();
            path.push({
                position: new THREE.Vector3(
                    position.x + offset.x, 
                    0.5, 
                    position.z + offset.z 
                ), 
                index: i, 
                area: AREA_GOAL 
            });
        }
    }

    // Goal -> Goal 
    else if (last_area === AREA_GOAL && figure.area === AREA_GOAL) {
        let current = last_position;
        for (let i = current + 1; i <= figure.position; i++) {
            const goal_field = goalFields[player_index][i]; 
            if (!goal_field) continue;

            const position = goal_field.position.clone(); 
            path.push({
                position: new THREE.Vector3(
                    position.x + offset.x, 
                    0.5, 
                    position.z + offset.z 
                ), 
                index: i, 
                area: AREA_GOAL 
            });
        }
    }

    // Home -> Field
    else if (last_area === AREA_HOME && figure.area === AREA_FIELD) {
        const position = mainFields[figure.position].position.clone();
        path.push({
            position: new THREE.Vector3(
                position.x + offset.x, 
                0.5, 
                position.z + offset.z 
            ), 
            index: figure.position, 
            area: AREA_FIELD 
        });
    }
    return path;
}

// Helper - Get mesh at given position in area
function getFieldMeshAt(area, position) {
    if (area === AREA_HOME) return homeFields[position];
    if (area === AREA_FIELD) return mainFields[position];
    if (area === AREA_GOAL) return goalFields[position];
    return null;
}

// Helper - Get Figure mesh at given position in area
function getFigureMeshAt(area, position) {}

// Helper - Calculate the hopping height for the figure
function calculateHopHeight(current_mesh, next_mesh = null) {
    const y_start = current_mesh.position.y;

    if (!next_mesh) {
        return {
            y_start, 
            y_end: y_start, 
            y_top: y_start + BASE_JUMP_HEIGHT 
        };
    }

    //console.log('current_mesh(' + current_mesh.area + '/' + current_mesh.index + ') to next_mesh(' + next_mesh.area + '/' + next_mesh.index + ')');

    const y_end = getPositionBaseHeight(next_mesh.area, next_mesh.index);
    const y_top = Math.max(y_start, y_end) + BASE_JUMP_HEIGHT;

    return { y_start, y_end, y_top };
}

// Helper - calculate height within the animation at given progress / time
function calculateHopHeightAtMoment(y_start, y_end, y_top, progress) {
    const a = 2 * (y_start + y_end - 2 * y_top); 
    const b = y_end - y_start - a;
    const c = y_start;

    return a * progress * progress + b * progress + c;
}

// Helper - Get top height of given position
function getPositionBaseHeight(area, position) {
    //console.log('getPositionBaseHeight(' + area + ', ' + position + ')');
    const is_occupied = isPositionOccupied(area, position);
    if (!is_occupied) return BASE_HEIGHT;

    return BASE_HEIGHT + FIGURE_HEIGHT;
}

// Helper - Get next position on path
function getNextPathStep(animation, current_step) {
    if (!animation || !animation.path) return null;
    if (current_step + 1 >= animation.path.length) return null;
    return animation.path[current_step + 1];
}

// Helper - Check if given position is occupied by any mesh
function isPositionOccupied(area, position) {
    let is_occupied = Object.values(figure_meshes).some(mesh =>
        mesh.userData.last_area === area && 
        mesh.userData.last_position === position 
    );
    //console.log('Params fo function: area = ' + area + ', position = ' + position);
    //console.log('How many Figures on Board: ' + Object.values(figure_meshes).length);
    //console.log('Is occupied: ' + is_occupied);
    //console.log(figure_meshes);
    return is_occupied;
}

// Helper - Check if given position is occupied by any mesh but the given mesh
function isPositionOccupiedByOtherMesh(current_mesh, area, position) {
    let is_occupied = Object.values(figure_meshes).some(mesh => 
        mesh !== current_mesh && 
        mesh.userData.last_area === area && 
        mesh.userData.last_position === position 
    );
    //console.log("Area: " + area + " / Position: " + position + " is occupied? " + is_occupied);
    return is_occupied;
}

// Helper - Get last field index for given player before entering the goal area
function getLastPositionBeforeGoal(player_index) {
    const last_field_before_goal = [39, 9, 19, 29];
    return last_field_before_goal[player_index] ?? 0;
}

// Helper - Create Figure
function createFigure(color) {
   return themeCreateFigure(color);
}

// Getter - Get camera_target_position
export function getCameraTarget() {
    return camera_target_position;
}

// Setter - Set camera_mode
export function setCameraMode(mode) {
    camera_mode = mode;
}

// Make sure to have win assets in scene
function ensureWinAssets() {
    if (win_assets_loaded) return;

    win_assets = themeGetWinAssets(); 
    if (!win_assets) return; 

    Object.values(win_assets).flat().forEach(asset => {
        if (asset.mesh) {
            scene.add(asset.mesh);
        }
    }); 
    win_assets_loaded = true;
}

// Start the animation after game is won
export function startWinAnimation(winner_index) {
    ensureWinAssets(); 

    const PLAYER_COLORS = themeGetPlayerColors(); 
    const winner_color = PLAYER_COLORS[winner_index] || 0xffffff; 

    const context = {
        scene, 
        assets: win_assets, 
        winner_color
    };

    themeStartWinAnimation(context);
}