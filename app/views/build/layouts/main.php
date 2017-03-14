<?php
/**
 * @var $content string
 */

?><!DOCTYPE html>
<html>
<head>
	<title><?= Config::get( 'app.title', '' ); ?></title>
	<meta charset="utf-8"/>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <link rel="stylesheet" href="/assets/js/fancybox/jquery.fancybox.css?v=2.1.6" type="text/css" media="screen" />
	<link rel="stylesheet" href="/assets/css/styles-b583b0b8c3.min.css">
	<meta content="<?= Config::get( 'app.description', '' ) ?>" name="description"/>
	<script type="text/javascript">
		var BASE_URL = '<?= URI::base(); ?>';
	</script>
	<link rel="shortcut icon" href="<?= URL::to('favicon2.ico'); ?>">

    <meta property="og:url"           content="<?=URL::to(URL::current());?>" />
    <meta property="og:type"          content="website" />
    <meta property="og:title"         content="<?= $shareTitle; ?>" />
    <meta property="og:description"   content="<?= $shareDesc; ?>" />
    <meta property="og:image"         content="<?= $shareImg; ?>" />
</head>
<body class="<?= $class; ?>">

<?= $content; ?>

<script src="/assets/js/app-d325354a5c.min.js"></script>
<script type="text/javascript" src="/assets/js/fancybox/jquery.fancybox.pack.js?v=2.1.6"></script>

<?= Asset::GA( Config::get( 'app.ga_code' ) ); ?>
</body>
</html>