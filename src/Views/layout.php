<?php
use App\Core\Localization;
use App\Core\Asset;
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title><?= Localization::get('application.general.title') ?></title>
        <link rel="stylesheet" href="<?= Asset::asset('css/general.css') ?>">
    </head>
    <body>

        <?= $content ?>

    </body>
</html>
