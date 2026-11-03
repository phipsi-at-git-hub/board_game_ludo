<?php

/**
 * @var Object $game
 * @var Object $current_user 
 */

$is_admin_view = true; 

$show_history = true; 
$show_metadata = true; 

// ToDo: Use ViewRender->renderPartialString()
include VIEWS_PATH . '/game/show.php'; 

?>