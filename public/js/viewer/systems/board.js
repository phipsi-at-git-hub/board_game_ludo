//import * as THREE from "three"; 
import { AREA_HOME, AREA_GOAL, AREA_FIELD } from "../core/consts.js";
import { 
    themeGetBoard, 
    themeGetCellSize, 
    themeGetPlayerColors, 
    themeGetAssets, 
    themeCreateBox, 
    themeCreateBoardGround
} from "../theme_manager.js";

// Field Storage
export const board_state = {
    mainFields: new Array(40), 
    homeFields: {}, 
    goalFields: {} 
}

// Initialize Board
export function initBoard(scene) { 
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

            createCell(cell, worldX, worldZ, scene);
        });
    });

    initAssets(scene);
}

// Create Cells
function createCell(cell, x, z, scene) {
    const PLAYER_COLORS = themeGetPlayerColors();

    // HOME
    if (cell.startsWith("H")) {
        const [player, index] = parsePlayerIndex(cell);
        const mesh = createBox(PLAYER_COLORS[player], 0.8, 'home');
        mesh.position.set(x, 0.05, z);
        scene.add(mesh);

        board_state.homeFields[player] ??= [];
        board_state.homeFields[player][index] = mesh;
        return;
    }

    // GOAL
    if (cell.startsWith("G")) {
        const [player, index] = parsePlayerIndex(cell);
        const mesh = createBox(PLAYER_COLORS[player], 0.8, AREA_GOAL);
        mesh.position.set(x, 0.05, z);
        scene.add(mesh);

        board_state.goalFields[player] ??= [];
        board_state.goalFields[player][index] = mesh;
        return;
    }

    // FIELD
    if (cell.startsWith("F")) {
        let pos = parseInt(cell.split('-')[1]);
        const index = parseInt(cell.split("-")[1]);
        const mesh = (pos === 0 || pos === 10 || pos === 20 || pos === 30) ? createBox(PLAYER_COLORS[pos/10], 1, "start") : createBox(0xdddddd, 0.9);
        mesh.position.set(x, 0.05, z);
        scene.add(mesh);

        board_state.mainFields[index] = mesh;
        return;
    }
}

// Create Assets
function initAssets(scene) {
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

// Helper - Parse player_index
function parsePlayerIndex(str) {
    const match = str.match(/[HG](\d+)-(\d+)/);
    return [parseInt(match[1]), parseInt(match[2])];
}

// Helper - Create Box
function createBox(color, scale = 1, type = null) {
   return themeCreateBox(color, scale, type);
}

