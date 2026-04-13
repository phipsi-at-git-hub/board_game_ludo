import * as THREE from 'https://unpkg.com/three/build/three.module.js';
import { getCameraTarget, getInitialCameraTarget } from './systems/camera.js';
import { updateAnimations } from './scene.js';
import { themeGetRendererConfig, themeCreateLights } from './theme_manager.js';

export let scene, camera, renderer;
let render_loop_started = false; 
let game_canvas;
let camera_target = null;
let is_camera_animating = false;
let last_time = performance.now(); 

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
    camera.lookAt(0, -1, 0);

    // Initiate renderer
    renderer = new THREE.WebGLRenderer({
        canvas, 
        antialias: true
    });

    // Get Renderer config from theme
    const renderer_config = themeGetRendererConfig();
    for (const key in renderer_config) {
        const value  = renderer_config[key]; 

        if (key === "shadowMapType" && typeof value === "string") {
            renderer.shadowMap.type = THREE[value];
        } else if ((key === "shadowMapEnabled" || key == "shadowMap.enabled") && typeof value === "boolean") {
            renderer.shadowMap.enabled = value;
        } else if (key === "toneMapping" && typeof value === "string") {
            renderer.toneMapping = THREE[value];
        } else if (key === "outputEncoding" && typeof value === "string") {
            renderer.outputEncoding = THREE[value]; 
        } else if (key in renderer) {
            renderer[key] = value;
        } else {
            console.warn(`Renderer has not the following property: '${key}'`);
        }
    }

    const  game_wrapper = canvas.parentElement;
    renderer.setSize(game_wrapper.clientWidth, game_wrapper.clientHeight);

    const theme_lights_factory = themeCreateLights();
    if (theme_lights_factory) {
        theme_lights_factory.forEach(createLight => {
            scene.add(createLight());
        });
    }

    // Grid for Debug
    //const grid = new THREE.GridHelper(13, 13); 
    //scene.add(grid);

    resizeRenderer();
    window.addEventListener('resize', resizeRenderer);
}

export function renderLoop() {
    requestAnimationFrame(renderLoop);
    
    const now = performance.now(); 
    const delta_time = (now - last_time) / 1000;
    last_time = now; 
    
    if (camera && getCameraTarget()) {
        // Smooth interpolation
        camera.position.lerp(
            getCameraTarget(), 
            0.08
        );
        camera.lookAt(0,-1,0);
    }
    
    updateAnimations(delta_time);
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

export function animateCameraTo(player_index, duration = 2000) {
    // Calculate target position
    camera_target = getInitialCameraTarget(player_index);
    is_camera_animating = true;

    // Keep start time in mind
    const start = performance.now();
    const start_position = camera.position.clone();

    function step(now) {
        const t = Math.min((now - start) / duration, 1); // 0 .. 1
        camera.position.lerpVectors(start_position, camera_target, t);
        camera.lookAt(0,-1,0);

        if (t < 1) requestAnimationFrame(step);
        else is_camera_animating = false;
    }
    requestAnimationFrame(step);
}
