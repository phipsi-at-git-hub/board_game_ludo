<?php
use App\Core\Asset;

/** 
 * @var String $page_title
 * @var array $css_array
 * @var array $js_array 
 * @var String $content 
 * @var String $content_css_class 
 */
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title><?= $page_title ?></title>
        <link rel="stylesheet" href="<?= Asset::css('variables.css') ?>">
        <link rel="stylesheet" href="<?= Asset::css('general.css') ?>">
        <link rel="stylesheet" href="<?= Asset::css('components.css') ?>">
        <link rel="stylesheet" href="<?= Asset::css('forms.css') ?>">
        <link rel="stylesheet" href="<?= Asset::css('buttons.css') ?>">
        <?php foreach ($css_array as $css): ?>
            <link rel="stylesheet" href="<?= Asset::css("$css.css") ?>">
        <?php endforeach; ?>
    </head>
    <body>

        <!-- Navigational Bar -->
        <?php include VIEWS_PATH . '/partials/navbar.php'; ?>

        <!-- Main Content -->
        <div class="content-container <?= $content_css_class ?? '' ?>">
            <?= $content ?>
        </div>

        <!-- Modal Window --> 
        <?php include VIEWS_PATH . '/partials/modal.php' ?>

        <script src="<?= Asset::js('app/forms.js') ?>"></script>
        <script src="<?= Asset::js('app/modal.js') ?>"></script>
        <script src="<?= Asset::js('app/actions.js') ?>"></script>
        <script src="<?= Asset::js('app/main.js') ?>"></script>
        <?php foreach ($js_array as $js): ?>
                <script src="<?= Asset::js("$js.js") ?>"></script>
        <?php endforeach; ?>

    </body>
</html>
