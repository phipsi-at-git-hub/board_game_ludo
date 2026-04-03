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

    // Define the colors of the Player
    getPlayerColors() {
        return {
            0: 0xff4444, 
            1: 0x4444ff, 
            2: 0x44ff44, 
            3: 0xffff44 
        };
    }, 

    // Define the Renderer Configuration 
    getRendererConfig() {
        return {
            shadowMapEnabled: true, 
            shadowMapType: "PCFShadowMap", // PCFSoftShadowMap has ben deprecated
            toneMapping: "ACESFilmicToneMapping", 
            toneMappingExposure: 1.3, 
            outputEncoding: "sRGBEncoding" 
        };
    }, 

    // Define offsets for Figures on same Field
    getFieldOffsets() {
        return [
            { x: -0.25, z: -0.25 },
            { x: 0.25, z: -0.25 },
            { x: -0.25, z: 0.25 },
            { x: 0.25, z: 0.25 }
        ];
    }, 

    // Define the Game Board Structure
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

    /**
     * 3D Objects of scene
     */
    // Create Game Board itself
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

    // Assets
    // Assets - Setup all Assets in the scene
    getAssets() {
        const BOARD = this.getBoard();
        const width = BOARD[0].length * CELL_SIZE;
        const height = BOARD.length * CELL_SIZE;
        const offsetX = -width / 2;
        const offsetZ = -height / 2;
        const colors = this. getPlayerColors();
        const base_position = {
            x: offsetX, 
            y: 0, 
            z: offsetZ
        };
        const base_rotations = [
            0, 
            Math.PI / 2, 
            Math.PI, 
            Math.PI * 1.5 
        ];

        return [
            {
                // Trees
                id: "tree_0", 
                mesh: this.createAssetTree(), 
                position: { x: -offsetX - 1.5, y: 0, z: offsetZ + 3 }, 
                rotation: { y: Math.PI / 2 }, 
                scale: 1
            }, 
            {
                id: "tree_1", 
                mesh: this.createAssetTree(), 
                position: { x: -offsetX - 2.5, y: 0, z: offsetZ + 2.75 }, 
                rotation: { y: Math.PI }, 
                scale: 1.4
            }, 
            {
                id: "tree_2", 
                mesh: this.createAssetTree(), 
                position: { x: -offsetX - 3.25, y: 0, z: offsetZ + 1.55 }, 
                rotation: { y: Math.PI }, 
                scale: 1.3
            }, 
            {
                // Rocks
                id: "rock_0", 
                mesh: this.createRockStack(), 
                position: { x: offsetX + 2.5, y: 0.1, z: -offsetZ - 2.5 }, 
                rotation: { y: Math.PI / 1.2 }, 
                scale: 1
            }, 
            {
                id: "rock_1", 
                mesh: this.createRockStack(), 
                position: { x: offsetX + 3.5, y: 0.1, z: -offsetZ - 2.0 }, 
                rotation: { y: Math.PI / 1.9 }, 
                scale: 0.8
            }, 

            ...[0, 1, 2, 3].map(i => {
                const rotation = base_rotations[i];
                const cos = Math.cos(rotation);
                const sin = Math.sin(rotation);

                const rotated_x = base_position.x * cos - base_position.z * sin;
                const rotated_z = base_position.x * sin + base_position.z * cos;

                return {
                    id: `home_${i}`, 
                    mesh: this.createAssetHomeZone(colors[i]), 
                    position: {
                        x: rotated_x , 
                        y: 0, 
                        z: rotated_z
                    }, 
                    rotation: {
                        y: -rotation
                    }, 
                    scale: 1
                };
            }), 
        ];
    }, 

    // Asset - Create Home Zone
    createAssetHomeZone(color) {
        const group = new THREE.Group();

        // Gate
        const gate = this.createAssetGate(color)
        gate.position.set(0.5, 0, 2);
        gate.rotation.y = Math.PI;
        group.add(gate);

        // Fences
        const fence_0 = this.createAssetFence();
        fence_0.position.set(1.5, 0, 2);
        fence_0.rotation.y = Math.PI;
        group.add(fence_0);
        
        const fence_1 = this.createAssetFence(4);
        fence_1.position.set(2, 0, 2);
        fence_1.rotation.y = Math.PI / 2;
        group.add(fence_1);

        const fence_2 = this.createAssetFence(4);
        fence_2.position.set(2, 0, 0);
        fence_2.rotation.y = Math.PI;
        group.add(fence_2);

        const fence_3 = this.createAssetFence(4);
        fence_3.position.set(0, 0.1, 0);
        fence_3.rotation.y = Math.PI * 1.5;
        group.add(fence_3);

        return group;
    }, 

    // Asset - Create Tree
    createAssetTree() {
        const group = new THREE.Group();

        const trunk = new THREE.Mesh(
            new THREE.CylinderGeometry(0.1, 0.1, 0.6, 8), 
            new THREE.MeshStandardMaterial({ color: 0x8b5a2b })
        );
        trunk.position.y = 0.3;

        const crown = new THREE.Mesh(
            new THREE.SphereGeometry(0.4, 16, 16), 
            new THREE.MeshStandardMaterial({ color: 0x66ff99 })
        );
        crown.position.y = 0.9;

        trunk.castShadow = true;
        crown.castShadow = true;

        group.add(trunk, crown);
        return group;
    }, 

    // Asset - Create Lego Stack
    createRockStack() {
        const group = new THREE.Group();

        for (let i = 0; i < 3; i++) {
            const brick = new THREE.Mesh(
                new RoundedBoxGeometry(0.6, 0.2, 0.6, 4, 0.05), 
                new THREE.MeshStandardMaterial({
                    color: [0xff6666, 0x66ccff, 0xffff66][i % 3], 
                    roughness: 0.2
                })
            );
            brick.position.y = i *0.2;
            brick.castShadow = true;
            group.add(brick);
        }
        return group;
    }, 

    // Asset - Create Gate
    createAssetGate(color) {
        const group = new THREE.Group();
        const left = new THREE.Mesh(
            new THREE.BoxGeometry(0.2, 0.8, 0.2), 
            new THREE.MeshStandardMaterial({ color }) 
        );
        left.position.set(-0.4, 0.4, 0);

        const right = left.clone();
        right.position.x = 0.4;

        const top = new THREE.Mesh(
            new THREE.BoxGeometry(1, 0.2, 0.2), 
            new THREE.MeshStandardMaterial({ color }) 
        );
        top.position.y = 0.9;

        [left, right, top].forEach(m => m.castShadow = true); 

        group.add(left, right, top);
        return group;
    }, 

    // Asset - Create Fence
    createAssetFence(length = 2) {
        const group = new THREE.Group();
         for (let i = 0; i < length; i++) {
            const post = new THREE.Mesh(
                new THREE.BoxGeometry(0.1, 0.5, 0.1), 
                new THREE.MeshStandardMaterial({ color: 0xffffff })
            );
            post.position.set(i * 0.5, 0.2, 0);
            post.castShadow = true;
            //post.receiveShadow = true;
            group.add(post);
         }
         return group;
    }, 

    // Create Field Boxes - Where the figures are placed
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

    // Create Figure
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

    // Create Lights 
    createLights() {
        return [
            () => new THREE.AmbientLight(0xffffff, 0.65), 
            () => {
                const dir = new THREE.DirectionalLight(0xffffff, 2.2);
                dir.position.set(10, 18, 10); 
                dir.castShadow = true;

                // Shadows
                dir.shadow.camera.left = -15;
                dir.shadow.camera.right = 15;
                dir.shadow.camera.top = 15;
                dir.shadow.camera.bottom = -15;
                dir.shadow.camera.near = 1;
                dir.shadow.camera.far = 50;
                dir.shadow.mapSize.width = 4096;
                dir.shadow.mapSize.height = 4096;
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