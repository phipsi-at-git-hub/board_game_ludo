<?php

use App\Core\Renderer\ViewRenderer;

/**
 * @var Object $game
 * @var Object $current_user 
 * 
 * @var ViewRenderer $viewRenderer
 */

$is_admin_view = true; 

$show_history = true; 
$show_metadata = true; 

$viewRenderer->RenderView(
    'game', 
    'show', 
    [
        'is_admin_view' => $is_admin_view, 
        'show_history' => $show_history, 
        'show_metadata' => $show_metadata
    ]
)

?>