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
    themeCreateBoardGround 

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
let board_initialized = false;
let camera_target_position = new THREE.Vector3();
let camera_mode = CAMERA_MODE_FIXED;

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

            if (!figure_meshes[player.player_index + '-' + figure.figure_index]) {
                const mesh = createFigure(PLAYER_COLORS[player.player_index]);
                scene.add(mesh);
                figure_meshes[player.player_index + '-' + figure.figure_index] = mesh;
            }

            const mesh = figure_meshes[player.player_index + '-' + figure.figure_index];

            // Get root position of fields
            let basePos;
            if (figure.area === "home") basePos = homeFields[player.player_index][figure.position].position;
            else if (figure.area === "goal") basePos = goalFields[player.player_index][figure.position].position;
            else basePos = mainFields[figure.position].position;

            // Only use offset if multiple figures occupy the same position
            const offset = group.length > 1 ? FIELD_OFFSETS[i % FIELD_OFFSETS.length] : { x: 0, z: 0 };

            mesh.position.set(
                basePos.x + offset.x,
                0.5,
                basePos.z + offset.z
            );
        });
    });
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