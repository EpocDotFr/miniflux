<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <link rel="icon" type="image/png" href="./assets/img/favicon.png">
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="apple-touch-icon" href="./assets/img/touch-icon-iphone.png">
        <link rel="apple-touch-icon" sizes="72x72" href="./assets/img/touch-icon-ipad.png">
        <link rel="apple-touch-icon" sizes="114x114" href="./assets/img/touch-icon-iphone-retina.png">
        <link rel="apple-touch-icon" sizes="144x144" href="./assets/img/touch-icon-ipad-retina.png">
        <title><?= isset($title) ? Helper\escape($title) : 'miniflux' ?></title>
        <link href="<?= Helper\css() ?>" rel="stylesheet" media="screen">
        <script type="text/javascript" src="./assets/js/app.js?v<?= filemtime('assets/js/app.js') ?>" defer></script>
    </head>
    <body>
        <header>
            <nav>
                <a class="logo" href="?">mini<span>flux</span></a>
                <?= Plugin::buildMenu(
                    isset($menu) ? $menu : null,
                    isset($nb_unread_items) ? $nb_unread_items : null) ?>
            </nav>
        </header>
        <section class="page">
            <?= Helper\flash('<div class="alert alert-success">%s</div>') ?>
            <?= Helper\flash_error('<div class="alert alert-error">%s</div>') ?>
