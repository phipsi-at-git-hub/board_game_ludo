
// Camera
export const CAMERA_MODE_FIXED = 'fixed';
export const CAMERA_MODE_FOLLOW = 'follow_turn';
export const CAMERA_MODE_DEFAULT = CAMERA_MODE_FIXED;

export const CAMERA_RADIUS = 7;  // Distance to board center
export const CAMERA_HEIGHT = 10; // Height above board
export const CAMERA_TILT = -8 * Math.PI / 180;

// Camera base angels for player view
export const CAMERA_BASE_ANGELS = [
    3 * Math.PI/2 + CAMERA_TILT, // player 0
    Math.PI + CAMERA_TILT, // player 1
    Math.PI / 2 + CAMERA_TILT, // player 2
    0 + CAMERA_TILT, // player 3
];

// Areas
export const AREA_HOME = "home"; 
export const AREA_FIELD = "field"; 
export const AREA_GOAL = "goal"; 

// Animation
export const HOP_TIME = 0.5; // Time to animate one figure hop / jump in ms

// Figure
export const BASE_HEIGHT = 0.5;
export const FIGURE_HEIGHT = 1;
export const BASE_JUMP_HEIGHT = 0.25;