<?php
use App\Core\Localization;
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title><?= Localization::get('application.general.title') ?></title>
        <link rel="stylesheet" href="/css/general.css">
    </head>
    <body>

        <?= $content ?>

    </body>
</html>
