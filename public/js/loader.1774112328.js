/**
 * loader.1774112328.js
 * Loading all javascript files automatically with cache-busting
 */
(function() {
    // list of all modules to load
    const modules = [
        '/js/api.1774206302.js', 
        '/js/scene.1774112480.js', 
        '/js/game.1774211126.js', 
        '/js/renderer.1774106304.js'
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