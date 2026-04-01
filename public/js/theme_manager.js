//import * as THREE from 'https://unpkg.com/three/build/three.module.js';
import * as THREE from "three";

let current_theme = null; 

export function setTheme(theme) {
    current_theme = theme;
}

export function getTheme() {
    return current_theme;
}

export function themeCreateBox(color, scale = 1, type = null) {
    if (current_theme && current_theme.createBox) {
        return current_theme.createBox(color, scale, type);
    }

    // Fallback Game Board
    return new THREE.Mesh(
        new THREE.BoxGeometry(scale, 0.1, scale), 
        new THREE.MeshStandardMaterial({ color })
    );
}

export function themeCreateFigure(color) {
    if (current_theme && current_theme.createFigure) {
        return current_theme.createFigure(color);
    }

    // Fallback Figure
    return new THREE.Mesh(
        new THREE.SphereGeometry(0.4, 20, 20), 
        new THREE.MeshStandardMaterial({ color })
    );
}

export function themeGetBackground() {
    if (current_theme && current_theme.getBackground) {
        return current_theme.getBackground();
    }

    // Fallback Background
    return 0x87CEEB
}

export function themeGetPlayerColors() {
    if (current_theme && current_theme.GetPlayerColors) {
        return current_theme.GetPlayerColors();
    }

    return {
        0: 0xff4444, 
        1: 0x4444ff, 
        2: 0x44ff44, 
        3: 0xffff44 
    };
}

export function themeGetCellSize() {
    if (current_theme && current_theme.getCellSize) {
        return current_theme.getCellSize();
    }

    return 1;
}

export function themeGetFieldOffsets() {
    if (current_theme && current_theme.getFieldOffsets) {
        return current_theme.getFieldOffsets();
    }

    return [
        { x: -0.25, z: -0.25 },
        { x: 0.25, z: -0.25 },
        { x: -0.25, z: 0.25 },
        { x: 0.25, z: 0.25 }
    ];
}

export function themeGetBoard() {
    if (current_theme && current_theme.getBoard) {
        return current_theme.getBoard();
    }

    return null;
}