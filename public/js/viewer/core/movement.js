import { 
    BASE_HEIGHT, 
    FIGURE_HEIGHT 
} from "./consts.js";

// Helper - Get next position on path
export function getNextPathStep(animation, current_step) {
    if (!animation || !animation.path) return null;
    if (current_step + 1 >= animation.path.length) return null;
    return animation.path[current_step + 1];
}

// Helper - Get top height of given position
export function getPositionBaseHeight(area, position, figure_meshes) {
    const is_occupied = isPositionOccupied(area, position, figure_meshes);
    if (!is_occupied) return BASE_HEIGHT;

    return BASE_HEIGHT + FIGURE_HEIGHT;
}

// Helper - Check if given position is occupied by any mesh
export function isPositionOccupied(area, position, figure_meshes) {
    let is_occupied = Object.values(figure_meshes).some(mesh =>
        mesh.userData.last_area === area && 
        mesh.userData.last_position === position 
    );
    return is_occupied;
}

// Helper - Check if given position is occupied by any mesh but the given mesh
export function isPositionOccupiedByOtherMesh(current_mesh, area, position, figure_meshes) {
    let is_occupied = Object.values(figure_meshes).some(mesh => 
        mesh !== current_mesh && 
        mesh.userData.last_area === area && 
        mesh.userData.last_position === position 
    );
    return is_occupied;
}

// Helper - Get last field index for given player before entering the goal area
export function getLastPositionBeforeGoal(player_index) {
    const last_field_before_goal_map = [39, 9, 19, 29];
    return last_field_before_goal_map[player_index] ?? 0;
}