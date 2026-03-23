import * as THREE from 'https://unpkg.com/three/build/three.module.js';

export let scene, camera, renderer;
let game_canvas;

export function initRenderer(canvas) {
    game_canvas = canvas;

    scene = new THREE.Scene();

    camera = new THREE.PerspectiveCamera(
        75, 
        window.innerWidth / window.innerHeight, 
        0.1, 
        1000
    );

    camera.position.set(0, 15, 10); 
    camera.lookAt(0, 0, 0);

    renderer = new THREE.WebGLRenderer({
        canvas, 
        antialias: true
    });

    const  game_wrapper = canvas.parentElement;
    renderer.setSize(game_wrapper.clientWidth, game_wrapper.clientHeight);

    const light = new THREE.DirectionalLight(0xffffff, 1);
    light.position.set(5, 10, 5);
    scene.add(light);

    const grid = new THREE.GridHelper(13, 13); 
    scene.add(grid);

    resizeRenderer();
    window.addEventListener('resize', resizeRenderer);
}

export function renderLoop() {
    requestAnimationFrame(renderLoop);
    renderer.render(scene, camera);
}

function resizeRenderer() {
    const game_wrapper = game_canvas.parentElement;

    const game_wrapper_width = game_wrapper.clientWidth;
    const game_wrapper_height = game_wrapper.clientHeight;

    renderer.setSize(game_wrapper_width, game_wrapper_height);

    camera.aspect = game_wrapper_width / game_wrapper_height;
    camera.updateProjectionMatrix();
}