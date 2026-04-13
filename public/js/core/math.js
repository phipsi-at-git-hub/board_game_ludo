import { BASE_JUMP_HEIGHT } from "./consts.js";

// Helper - Calculate the hopping height for the figure
export function calculateHopHeight(y_start, y_next = null) {
    if (!y_next) {
        return {
            y_start, 
            y_end: y_start, 
            y_top: y_start + BASE_JUMP_HEIGHT 
        };
    }

    const y_end = y_next;
    const y_top = Math.max(y_start, y_end) + BASE_JUMP_HEIGHT;

    return { y_start, y_end, y_top };
}

// Helper - calculate height within the animation at given progress / time
export function calculateHopHeightAtMoment(y_start, y_end, y_top, progress) {
    const a = 2 * (y_start + y_end - 2 * y_top); 
    const b = y_end - y_start - a;
    const c = y_start;

    return a * progress * progress + b * progress + c;
}