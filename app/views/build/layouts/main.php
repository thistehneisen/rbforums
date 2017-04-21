<?php
/**
 * @var $content string
 * @var $shareTitle string
 * @var $shareDesc string
 * @var $shareImg string
 */

?><!DOCTYPE html>
<html>
<head>
	<title><?= Config::get( 'app.title', '' ); ?></title>
	<meta charset="utf-8"/>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<link rel="stylesheet" href="/forum/assets/css/styles-51f51f5843.min.css">
	<meta content="<?= Config::get( 'app.description', '' ) ?>" name="description"/>
	<script type="text/javascript">
		var BASE_URL = '<?= URI::base(); ?>';
	</script>
	<link rel="shortcut icon" href="<?= URL::to('favicon.ico'); ?>">

    <meta property="og:url"           content="<?=URL::to(URL::current());?>" />
    <meta property="og:type"          content="website" />
    <meta property="og:title"         content="<?= $shareTitle; ?>" />
    <meta property="og:description"   content="<?= $shareDesc; ?>" />
    <meta property="og:image"         content="<?= $shareImg; ?>" />
</head>
<body>

<?= $content; ?>

<script src="/forum/assets/js/app-505d458e57.min.js"></script>

<?= Asset::GA( Config::get( 'app.ga_code' ) ); ?>
</body>
</html>