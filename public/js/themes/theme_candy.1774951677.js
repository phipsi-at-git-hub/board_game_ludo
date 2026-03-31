import * as THREE from 'https://unpkg.com/three/build/three.module.js';

export const theme_candy = {
    getBackground() {
        //return 0xfff0f6; // soft pink
        return 0x87ceeb;
    }, 

    createBox(color, scale = 1) {
        return new THREE.Mesh(
            new THREE.BoxGeometry(scale, 0.2, scale), 
            new THREE.MeshStandardMaterial({
                color, 
                roughness: 0.3, 
                metalness: 0.1
            })
        );
    }, 

    createFigure(color) {
        const geometry = new THREE.SphereGeometry(0.45, 24, 24);
        const material = new THREE.MeshStandardMaterial({
            color, 
            roughness: 0.2, 
            metalness: 0.3
        });
        const mesh = new THREE.Mesh( geometry, material); 

        // Candy detail
        const top = new THREE.Mesh(
            new THREE.SphereGeometry(0.2, 16, 16), 
            new THREE.MeshStandardMaterial({ color: 0xffffff })
        );

        top.position.y = 0.35;
        mesh.add(top);

        return mesh;
    } 
};