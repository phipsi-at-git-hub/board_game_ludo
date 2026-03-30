<?php
use App\Core\Asset;
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title><?= $page_title ?></title>
        <link rel="stylesheet" href="<?= Asset::asset('css/general.css') ?>">
        <?php foreach ($css_array as $css): ?>
            <link rel="stylesheet" href="<?= Asset::asset("css/$css.css") ?>">
        <?php endforeach; ?>
    </head>
    <body>

        <!-- Navigational Bar -->
        <?php include VIEWS_PATH . '/partials/navbar.php'; ?>

        <!-- Main Content -->
        <div class="content-container <?= $content_css_class ?>">
            <?= $content ?>
        </div>

    </body>
</html>
