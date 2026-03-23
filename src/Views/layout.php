<?php
use App\Core\Localization;
use App\Core\Asset;

$cssPath = __DIR__ . '/../../public/css/general.css';
$cssVersion = file_exists($cssPath) ? filemtime($cssPath) : time();
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title><?= Localization::get('application.general.title') ?></title>
        <link rel="stylesheet" href="<?= Asset::asset('css/general.css') ?>">
        <link rel="stylesheet" href="<?= Asset::asset('/css/game.css') ?>">
    </head>
    <body>

        <?= $content ?>

    </body>
</html>
