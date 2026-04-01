import * as THREE from 'https://unpkg.com/three/build/three.module.js';

export const theme_candy = {
    getBackground() {
        //return 0xfff0f6; // soft pink
        return 0x87ceeb;
    }, 

    getPlayerColors() {
        return {
            0: 0xff4444, 
            1: 0x4444ff, 
            2: 0x44ff44, 
            3: 0xffff44 
        };
    }, 

    getCellSize() {
        return 1;
    }, 

    getFieldOffsets() {
        return [
            { x: -0.25, z: -0.25 },
            { x: 0.25, z: -0.25 },
            { x: -0.25, z: 0.25 },
            { x: 0.25, z: 0.25 }
        ];
    }, 

    getBoard() {
        return [
            ["H0-1","H0-2","-","-","F-8","F-9","F-10","-","-","H1-0","H1-1"],
            ["H0-0","H0-3","-","-","F-7","G1-0","F-11","-","-","H1-3","H1-2"],
            ["-","-","-","-","F-6","G1-1","F-12","-","-","-","-"],
            ["-","-","-","-","F-5","G1-2","F-13","-","-","-","-"],
            ["F-0","F-1","F-2","F-3","F-4","G1-3","F-14","F-15","F-16","F-17","F-18"],
            ["F-39","G0-0","G0-1","G0-2","G0-3","-","G2-3","G2-2","G2-1","G2-0","F-19"],
            ["F-38","F-37","F-36","F-35","F-34","G3-3","F-24","F-23","F-22","F-21","F-20"],
            ["-","-","-","-","F-33","G3-2","F-25","-","-","-","-"],
            ["-","-","-","-","F-32","G3-1","F-26","-","-","-","-"],
            ["H3-2","H3-3","-","-","F-31","G3-0","F-27","-","-","H2-3","H2-0"],
            ["H3-1","H3-0","-","-","F-30","F-29","F-28","-","-","H2-2","H2-1"]
        ];
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