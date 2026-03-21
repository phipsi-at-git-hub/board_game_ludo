/**
 * loader.js
 * Loading all javascript files automatically with cache-busting
 */
(function() {
    // list of all modules to load
    const modules = [
        '/js/api.js', 
        '/js/scene.js', 
        '/js/game.js', 
        '/js/renderer.js'
    ];

    // loading a js-module dynamically with cache-busting
    function loadModule(path) {
        const script = document.createElement('script');
        script.type = 'module';
        script.src = `${path}?v=${Date.now()}`; 
        document.head.appendChild(script);
        //console.log(`Module loaded: ${path}`);
    }

    // load all modules
    modules.forEach(loadModule);
})();