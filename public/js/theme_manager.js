import * as THREE from 'https://unpkg.com/three/build/three.module.js';

let current_theme = null; 

export function setTheme(theme) {
    current_theme = theme;
}

export function getTheme() {
    return current_theme;
}

export function themeCreateBox(color, scale = 1) {
    if (current_theme && current_theme.createBox) {
        return current_theme.createBox(color, scale);
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