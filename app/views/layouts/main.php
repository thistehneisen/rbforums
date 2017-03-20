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
	<!--build:css /assets/css/styles.min.css-->
	<link type="text/css" rel="stylesheet" href="/forum/assets/css/style.css">
	<!--endbuild-->
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

<!--build:js /assets/js/app.min.js-->
<script type="text/javascript" src="/forum/assets/dev/js/edge.6.0.0.min.js"></script>
<script type="text/javascript" src="/forum/assets/js/app.js"></script>
<!--endbuild-->

<?= Asset::GA( Config::get( 'app.ga_code' ) ); ?>
</body>
</html>