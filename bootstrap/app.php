<?php
// bootstrap/app.php

/**
 * Create and bootstrap Application
 * 
 * The Application class creates the request container, 
 * registers core bindings and application services, 
 * and boots the application lifecycle withing the 
 * boot() method. 
 */

use App\Core\Application\App; 

$app = new App(); 
$app->boot(); 

return $app; 
