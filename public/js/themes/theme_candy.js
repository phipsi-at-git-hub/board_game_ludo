//import * as THREE from 'https://unpkg.com/three/build/three.module.js';
import * as THREE from "three";
import { RoundedBoxGeometry } from "three/RoundedBoxGeometry";

const CELL_SIZE = 1;
const PADDING = 2;
const BOX_HEIGHT = 0.2;
const BOX_RADIUS = 0.1;
const BOARD_GROUND_HEIGHT = 0.2;
const BOARD_GROUND_COLOR = 0xfff0f6; // soft pinkt
//const BOARD_GROUND_COLOR = 0xe6f7ff; // soft blue
//const BOARD_GROUND_COLOR = 0xfff5e6; // vanilla creme 
//const BOARD_GROUND_COLOR = 0x2d1b4e; // dark purple

export const theme_candy = {
    getBackground: () => 0x87ceeb,  
    getCellSize: () => CELL_SIZE, 

    getPlayerColors() {
        return {
            0: 0xff4444, 
            1: 0x4444ff, 
            2: 0x44ff44, 
            3: 0xffff44 
        };
    }, 

    getRendererConfig() {
        return {
            shadowMapEnabled: true, 
            shadowMapType: "PCFShadowMap", // PCFSoftShadowMap has ben deprecated
            toneMapping: "ACESFilmicToneMapping", 
            toneMappingExposure: 1.3, 
            outputEncoding: "sRGBEncoding" 
        };
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

    createBoardGround() {
        const BOARD = this.getBoard(); 
        const width = (BOARD[0].length * CELL_SIZE) + PADDING;
        const height = (BOARD.length * CELL_SIZE) + PADDING;

        const geometry = new RoundedBoxGeometry(
            width, 
            BOARD_GROUND_HEIGHT, 
            height, 
            0.3, 
            6
        );
        const material = new THREE.MeshStandardMaterial({
            color: BOARD_GROUND_COLOR, 
            roughness: 0.2, 
            metalness: 0.1, 
            emissive: new THREE.Color(BOARD_GROUND_COLOR).multiplyScalar(0.1), 
            emissiveIntensity: 0.2
        });

        const plane = new THREE.Mesh(geometry, material);
        plane.position.y = 0.05;

        plane.receiveShadow = true;

        return plane;
    }, 

    createBox(color, scale = 1, type = null) {
        let height = BOX_HEIGHT;
        let radius = BOX_RADIUS;

        if (type === "start" || type === "goal") {
            height = BOX_HEIGHT / 2;
            radius = BOX_RADIUS * 1.5;
        }

        //const geometry = new THREE.BoxGeometry(scale, height, scale);
        const geometry = new RoundedBoxGeometry(scale, height, scale, 4, radius);
        geometry.translate(0, height / 2, 0);
        const material = new THREE.MeshStandardMaterial({
            color, 
            roughness: 0.15, 
            metalness: 0.25, 
            emissive: new THREE.Color(color).multiplyScalar(0.1), 
            emissiveIntensity: 0.2
        });

        const box = new THREE.Mesh(geometry, material);
        
        box.castShadow = true;
        box.receiveShadow = true;
        
        return box;
    }, 

    createFigure(color) {
        const geometry = new THREE.SphereGeometry(0.45, 24, 24);
        const material = new THREE.MeshStandardMaterial({
            color, 
            roughness: 0.15, 
            metalness: 0.3, 
            emissive: new THREE.Color(color).multiplyScalar(0.2), 
            emissiveIntensity: 0.3
        });
        const mesh = new THREE.Mesh( geometry, material); 

        // Candy detail
        const top = new THREE.Mesh(
            new THREE.SphereGeometry(0.2, 16, 16), 
            new THREE.MeshStandardMaterial({ color: 0xffffff })
        );

        top.position.y = 0.35;
        mesh.add(top);

        mesh.castShadow = true;
        mesh.receiveShadow = true;

        return mesh;
    }, 

    createLights() {
        return [
            () => new THREE.AmbientLight(0xffffff, 0.65), 
            () => {
                const dir = new THREE.DirectionalLight(0xffffff, 2.2);
                dir.position.set(10, 18, 10); 
                dir.castShadow = true;

                // Shadows
                dir.shadow.mapSize.width = 2048;
                dir.shadow.mapSize.height = 2048;
                dir.shadow.radius = 4;

                return dir;
            }, 
            () => {
                const point_1 = new THREE.PointLight(0xff66cc, 0.9, 30);
                point_1.position.set(-5, 6, 5);
                return point_1;
            }, 
            () => {
                const point_2 = new THREE.PointLight(0x66ccff, 1, 30); 
                point_2.position.set(5, 6, -5); 
                return point_2;
            }
        ];
    }
};