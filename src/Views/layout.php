<?php
use App\Core\Localization;

$cssPath = __DIR__ . '/../../public/css/general.css';
$cssVersion = file_exists($cssPath) ? filemtime($cssPath) : time();
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title><?= Localization::get('application.general.title') ?></title>
        <link rel="stylesheet" href="<?= asset('css/general.css') ?>">
    </head>
    <body>

        <?= $content ?>

    </body>
</html>
