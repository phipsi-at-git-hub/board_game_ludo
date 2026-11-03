<?php

/**
 * @var array $games
 * @var Object $current_user
 */

$is_admin_view = true; 

// ToDo: Use ViewRender->renderPartialString()
include VIEWS_PATH . '/game/list.php';

?>