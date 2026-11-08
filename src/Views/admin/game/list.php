<?php

use App\Core\Renderer\ViewRenderer;

/**
 * @var array $games
 * @var Object $current_user
 * 
 * @var ViewRenderer $viewRenderer
 */

$is_admin_view = true; 

// ToDo: Use ViewRender->renderPartialString()
$viewRenderer->renderView(
    'game', 
    'list', 
    [
        'is_admin_view' => $is_admin_view
    ]
); 
//include VIEWS_PATH . '/game/list.php';

?>