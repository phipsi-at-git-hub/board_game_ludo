<?php

use App\Core\Renderer\ViewRenderer;

/**
 * @var array $games
 * @var Object $current_user
 * 
 * @var ViewRenderer $viewRenderer
 */

$is_admin_view = true; 

$viewRenderer->renderView(
    'game', 
    'list', 
    [
        'is_admin_view' => $is_admin_view
    ]
); 

?>