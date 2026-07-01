import * as THREE from "three"; 
import { 
    CAMERA_MODE_FOLLOW, 
    CAMERA_MODE_DEFAULT, 
    CAMERA_RADIUS, 
    CAMERA_HEIGHT, 
    CAMERA_BASE_ANGELS
} from "../core/consts.js"; 

let camera_target_position = new THREE.Vector3(); 
let camera_mode = CAMERA_MODE_DEFAULT;

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

    const angle = CAMERA_BASE_ANGELS[player_index] ?? 0;
    
    camera_target_position.set(
        Math.sin(angle) * CAMERA_RADIUS, 
        CAMERA_HEIGHT, 
        Math.cos(angle) * CAMERA_RADIUS
    );
}

// Initialize camera target after entering the scene
export function getInitialCameraTarget(player_index) {
    const angle = CAMERA_BASE_ANGELS[player_index]?? 0;

    camera_target_position.set(
        Math.sin(angle) * CAMERA_RADIUS, 
        CAMERA_HEIGHT, 
        Math.cos(angle) * CAMERA_RADIUS
    );

    return camera_target_position.clone();
}

// Getter - Get camera_target_position
export function getCameraTarget() {
    return camera_target_position;
}

// Setter - Set camera_mode
export function setCameraMode(mode) {
    camera_mode = mode;
}