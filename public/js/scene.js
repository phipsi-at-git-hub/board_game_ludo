import * as THREE from 'https://unpkg.com/three/build/three.module.js';
import { scene } from './renderer.js';

const figure_meshes = {};
let board_initialized = false;

// Field Storage
const mainFields = new Array(40);
const homeFields = {};
const goalFields = {};
const startFields = {};

const CELL_SIZE = 1;

// Board Matrix
const BOARD = [
["H1-1","H1-2","-","-","F-8","F-9","F-10","-","-","H2-0","H2-1"],
["H1-0","H1-3","-","-","F-7","G2-0","F-11","-","-","H2-3","H2-2"],
["-","-","-","-","F-6","G2-1","F-12","-","-","-","-"],
["-","-","-","-","F-5","G2-2","F-13","-","-","-","-"],
["F-0","F-1","F-2","F-3","F-4","G2-3","F-14","F-15","F-16","F-17","F-18"],
["F-39","G1-0","G1-1","G1-2","G1-3","-","G3-3","G3-2","G3-1","G3-0","F-19"],
["F-38","F-37","F-36","F-35","F-34","G0-3","F-24","F-23","F-22","F-21","F-20"],
["-","-","-","-","F-33","G0-2","F-25","-","-","-","-"],
["-","-","-","-","F-32","G0-1","F-26","-","-","-","-"],
["H0-2","H0-3","-","-","F-31","G0-0","F-27","-","-","H3-3","H3-0"],
["H0-1","H0-0","-","-","F-30","F-29","F-28","-","-","H3-2","H3-1"]
];

// Colors
const PLAYER_COLORS = {
    0: 0xff4444,
    1: 0x4444ff,
    2: 0x44ff44,
    3: 0xffff44
};

export function updateScene(state) {
    if (!board_initialized) {
        initBoard();
        board_initialized = true;
    }

    updateFigures(state);
}

// Create Board
function initBoard() {
    scene.background = new THREE.Color(0x87CEEB);

    const offset = (BOARD.length - 1) / 2;

    BOARD.forEach((row, z) => {
        row.forEach((cell, x) => {

            if (cell === "-") return;

            const worldX = (x - offset) * CELL_SIZE;
            const worldZ = (z - offset) * CELL_SIZE;

            createCell(cell, worldX, worldZ);
        });
    });
}

// Create Cells
function createCell(cell, x, z) {

    // HOME
    if (cell.startsWith("H")) {
        const [player, index] = parsePlayerIndex(cell);

        //const mesh = createBox(PLAYER_COLORS[player], 0.8);
        const mesh = createBox(getPlayerColor(player), 0.8);
        mesh.position.set(x, 0.05, z);
        scene.add(mesh);

        homeFields[player] ??= [];
        homeFields[player][index] = mesh;
        return;
    }

    // GOAL
    if (cell.startsWith("G")) {
        const [player, index] = parsePlayerIndex(cell);

        //const mesh = createBox(PLAYER_COLORS[player], 0.8);
        const mesh = createBox(getPlayerColor(player), 0.8);
        mesh.position.set(x, 0.05, z);
        scene.add(mesh);

        goalFields[player] ??= [];
        goalFields[player][index] = mesh;
        return;
    }

    // FIELD 
    if (cell.startsWith("F")) {
        const index = parseInt(cell.split("-")[1]);

        const mesh = createBox(0xffffff, 1);
        mesh.position.set(x, 0.05, z);
        scene.add(mesh);

        mainFields[index] = mesh;

        // Define start fields
        if (index === 0) startFields[1] = mesh;
        if (index === 10) startFields[2] = mesh;
        if (index === 20) startFields[3] = mesh;
        if (index === 30) startFields[4] = mesh;

        return;
    }
}

// Helper
function createBox(color, scale = 1) {
    return new THREE.Mesh(
        new THREE.BoxGeometry(scale, 0.1, scale),
        new THREE.MeshStandardMaterial({ color })
    );
}

function getPlayerColor(player) {
    console.log(player + ': ' + PLAYER_COLORS[player]);
    return parseInt(PLAYER_COLORS[player]) ?? 0xffffff;
}

function parsePlayerIndex(str) {
    const match = str.match(/[HG](\d+)-(\d+)/);
    return [parseInt(match[1]), parseInt(match[2])];
}

// Figures
function updateFigures(state) {
    state.players.forEach(player => {
        player.figures.forEach(figure => {

            if (!figure_meshes[figure.id]) {
                const mesh = createFigure(player.color || 0xff0000);
                scene.add(mesh);
                figure_meshes[figure.id] = mesh;
            }

            const mesh = figure_meshes[figure.id];
            const target = mapToBoardPosition(figure, player);

            mesh.position.lerp(
                new THREE.Vector3(target.x, 0.5, target.z),
                0.2
            );
        });
    });
}

function createFigure(color) {
    return new THREE.Mesh(
        new THREE.SphereGeometry(0.35, 16, 16),
        new THREE.MeshStandardMaterial({ color })
    );
}

// Mapping matrix to board
function mapToBoardPosition(figure, player) {

    if (figure.area === 'home') {
        return homeFields[player.index][figure.position].position;
    }

    if (figure.area === 'goal') {
        return goalFields[player.index][figure.position].position;
    }

    // Main fields via index
    return mainFields[figure.position].position;
}