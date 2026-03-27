import * as THREE from 'https://unpkg.com/three/build/three.module.js';
import { scene, camera } from './renderer.js';

const figure_meshes = {};
let board_initialized = false;
let camera_target_position = new THREE.Vector3();

// Field Storage
const mainFields = new Array(40);
const homeFields = {};
const goalFields = {};

// Colors
const PLAYER_COLORS = {
    0: 0xff4444, 
    1: 0x4444ff, 
    2: 0x44ff44, 
    3: 0xffff44 
};

// Offsets for multiple figures on same position
const FIELD_OFFSETS = [
    { x: -0.25, z: -0.25 },
    { x: 0.25, z: -0.25 },
    { x: -0.25, z: 0.25 },
    { x: 0.25, z: 0.25 }
];

const CELL_SIZE = 1;

// Board Matrix
const BOARD = [
    ["H0-1","H0-2","-","-","F-8","F-9","F-10","-","-","H1-0","H1-1"],
    ["H0-0","H0-3","-","-","F-7","G1-0","F-11","-","-","H1-3","H1-2"],
    ["-","-","-","-","F-6","G1-1","F-12","-","-","-","-"],
    ["-","-","-","-","F-5","G1-2","F-13","-","-","-","-"],
    ["F-0","F-1","F-2","F-3","F-4","G1-3","F-14","F-15","F-16","F-17","F-18"],
    ["F-39","G0-0","G0-1","G0-2","G0-3","-","G2-3","G2-2","G2-1","G2-0","F-19"],
    ["F-38","F-37","F-36","F-35","F-34","G3-3","F-24","F-23","F-22","F-21","F-20"],
    ["-","-","-","-","F-33","G3-2","F-25","-","-","-","-"],
    ["-","-","-","-","F-32","G3-1","F-26","-","-","-","-"],
    ["H3-2","H3-3","-","-","F-31","G3-0","F-27","-","-","H2-3","H2-0"],
    ["H3-1","H3-0","-","-","F-30","F-29","F-28","-","-","H2-2","H2-1"]
];

// Update Scene
export function updateScene(state) {
    if (!board_initialized) {
        initBoard();
        board_initialized = true;
    }

    placeFigures(state);
}

// Initialize camera target after entering the scene
export function getInitialCameraTarget(player_index) {
    const radius = 12;  // Distance to board center
    const height = 10; // Height above board
    const tilt = -8 * Math.PI / 180;

    // angle offset to own corner and board border
    const base_angles = [
        3 * Math.PI/2 + tilt, // player 0
        Math.PI + tilt, // player 1
        Math.PI / 2 + tilt, // player 2
        0 + tilt, // player 3
    ];

    const angle = base_angles[player_index]?? 0;

    camera_target_position.set(
        Math.sin(angle) * radius, 
        height, 
        Math.cos(angle) * radius
    );

    return camera_target_position.clone();
}

// Initialize Board
function initBoard() {
    scene.background = new THREE.Color(0x87CEEB);

    const offsetX = (BOARD[0].length - 1) / 2;
    const offsetZ = (BOARD.length - 1) / 2;

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
    // HOME
    if (cell.startsWith("H")) {
        const [player, index] = parsePlayerIndex(cell);
        const mesh = createBox(PLAYER_COLORS[player], 0.8);
        mesh.position.set(x, 0.05, z);
        scene.add(mesh);

        homeFields[player] ??= [];
        homeFields[player][index] = mesh;
        return;
    }

    // GOAL
    if (cell.startsWith("G")) {
        const [player, index] = parsePlayerIndex(cell);
        const mesh = createBox(PLAYER_COLORS[player], 0.8);
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
        const mesh = (pos === 0 || pos === 10 || pos === 20 || pos === 30) ? createBox(PLAYER_COLORS[pos/10], 1) : createBox(0xdddddd, 0.9);
        mesh.position.set(x, 0.05, z);
        scene.add(mesh);

        mainFields[index] = mesh;
        return;
    }
}

// Helper - Create Box
function createBox(color, scale = 1) {
    return new THREE.Mesh(
        new THREE.BoxGeometry(scale, 0.1, scale),
        new THREE.MeshStandardMaterial({ color })
    );
}

// Parse player_index
function parsePlayerIndex(str) {
    const match = str.match(/[HG](\d+)-(\d+)/);
    return [parseInt(match[1]), parseInt(match[2])];
}

// Position Figures
function placeFigures(state) {
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
    return new THREE.Mesh(
        new THREE.SphereGeometry(0.4, 20, 20),
        new THREE.MeshStandardMaterial({ color })
    );
}