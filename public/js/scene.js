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
        const mesh = createBox(PLAYER_COLORS[player], 0.8, "goal");
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
            if (figure.area === "home") key = `home_${player.player_index}_${figure.position}`;
            else if (figure.area === "goal") key = `goal_${player.player_index}_${figure.position}`;
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
            }

            const mesh = figure_meshes[mesh_key];

            // Get root position of fields
            let basePos;
            if (figure.area === "home") basePos = homeFields[player.player_index][figure.position].position;
            else if (figure.area === "goal") basePos = goalFields[player.player_index][figure.position].position;
            else basePos = mainFields[figure.position].position;

            // Only use offset if multiple figures occupy the same position
            const offset = group.length > 1 ? FIELD_OFFSETS[i % FIELD_OFFSETS.length] : { x: 0, z: 0 };

            const target_position = new THREE.Vector3(
                basePos.x + offset.x, 
                0.5, 
                basePos.z + offset.z 
            );

            // Calculate path
            const path = getPathPositions(mesh, figure, player, offset);

            // Animation
            if (!mesh.initialized) {
                mesh.position.copy(target_position);
                mesh.initialized = true;

                mesh.userData.last_position = figure.position;
                mesh.userData.last_area = figure.area;
            } else if (path.length > 0 && (mesh.userData.last_position !== figure.position || mesh.userData.last_area !== figure.area)) {
                figure_animations[mesh_key] = {
                    path, 
                    current_step: 0, 
                    progress: 0, 
                    step_duration: 0.18 
                };
            } else {
                mesh.position.copy(target_position); 
                mesh.userData.last_position = figure.position;
                mesh.userData.last_area = figure.area;
            }
        });
    });
}

// Update Figure animations
export function updateAnimations(delta_time) {
    Object.entries(figure_animations).forEach(([key, animation]) => {
        const mesh = figure_meshes[key];
        if (!mesh) return;

        if (!animation.path || animation.path.length === 0) {
            delete figure_animations[key]; 
            return;
        }

        const current = animation.path[animation.current_step]; 
        const previous = animation.current_step === 0 ? { position: mesh.position.clone() } : animation.path[animation.current_step -1];

        animation.progress += delta_time / animation.step_duration; 

        // Hopping effect
        const height = Math.sin(animation.progress * Math.PI) * 0.25;

        if (animation.progress >= 1) {
            animation.progress = 0;
            animation.current_step++;

            if (animation.current_step >= animation.path.length) {
                mesh.position.copy(current.position);

                // Update state
                mesh.userData.last_position = current.index;
                mesh.userData.last_area = current.area;

                delete figure_animations[key];
                return;
            }
        }
        mesh.position.lerpVectors(previous.position, current.position, animation.progress);
        mesh.position.y = 0.5 + height;
    });
}

// Helper - Get path positions for figure animation
function getPathPositions(mesh, figure, player, offset) {
    const path = [];
    const last_area = mesh.userData.last_area;
    const last_position = mesh.userData.last_position; 

    // Field -> Field
    if (last_area === "field" && figure.area === "field") {
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
                area: "field"
            });
        }
    }

    // Home -> Field
    else if (last_area === "home" && figure.area === "field") {
        const position = mainFields[figure.position].position.clone();
        path.push({
            position: new THREE.Vector3(
                position.x + offset.x, 
                0.5, 
                position.z + offset.z 
            ), 
            index: figure.position, 
            area: "field" 
        });
    }

    // Field -> Goal
    else if (figure.area === "goal") {
        const goal_field = goalFields[player.player_index][figure.position]; 
        if (goal_field) {
            const position = goal_field.position.clone();
            path.push({
                position: new THREE.Vector3(
                    position.x + offset.x, 
                    0.5, 
                    position.z + offset.z 
                ), 
                index: figure.position, 
                area: "goal" 
            }); 
        }
    }
    return path;
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