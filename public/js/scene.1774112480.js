//import * as THREE from 'https://unpkg.com/three@0.158.0/build/three.module.js'; 
import * as THREE from 'https://unpkg.com/three/build/three.module.js';
import { scene } from './renderer.1774106304.js';

const figure_meshes = {};

export function updateScene(state) {
    state.players.forEach(player => {
        player.figures.forEach(figure => {
            if (!figure_meshes[figure.id]) {
                const mesh = createFigure(player.color || 0xff0000);
                scene.add(mesh);
                figure_meshes[figure.id] = mesh;
            }

            const mesh = figure_meshes[figure.id];
            const position = mapToBoardPosition(figure.position, figure.area);

            mesh.position.lerp(
                new THREE.Vector3(position.x, 0.5, position.z), 
                0.2 
            );
        });
    });
}

function createFigure(color) {
    const geometry = new THREE.SphereGeometry(0.4, 16, 16);
    const material = new THREE.MeshStandardMaterial({ color });

    return new THREE.Mesh(geometry, material);
}

function mapToBoardPosition(position, area) {
    if (area === 'home') {
        return {
            x: Math.cos(position * 0.3) * 5, 
            z: Math.sin(position * 0.3) * 5
        }; 
    }

    if (area === 'goal') {
        return { x: 5, z: position };
    }

    return { x: 0, z: 0 };
}