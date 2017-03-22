<?php
/**
 * @var $content string
 */
?><!DOCTYPE html>
<html>
<head>
    <title><?=Config::get('app.title', '');?></title>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <link href="https://unpkg.com/basscss@7.1.1/css/basscss.min.css" rel="stylesheet">
	<!--build:css /assets/css/admin.min.css-->
	<link type="text/css" rel="stylesheet" href="/forum/node_modules/sweetalert/dist/sweetalert.css">
	<link type="text/css" rel="stylesheet" href="/forum/assets/css/admin.css">
	<!--endbuild-->
    <meta content="<?=Config::get('app.description', '')?>" name="description" />
    <meta property="og:title" content="<?=Config::get('app.title', '')?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?=URI::base();?><?=URI::raw();?>" />
    <meta property="og:image" content="<?=URI::base();?>img/share_img.png" />
    <meta property="og:site_name" content="<?=Config::get('app.title', '');?>" />
    <meta property="fb:admins" content="100000821969100" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript">
        var BASE_URL = '<?=URI::base();?>';
    </script>
</head>
<body>
<div class="wrapper">
    <?= Menu::top(); ?>
    <?= $content; ?>
	<!--build:js /assets/js/admin.min.js-->
    <script src="/forum/assets/js/admin.js"></script>
	<!--endbuild-->
    <?= Asset::GA(null); ?>
</body>
</html>