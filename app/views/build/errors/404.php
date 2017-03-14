<!doctype html>
<html>
<head>
	<meta charset="utf-8"/>
	<title>404</title>
	<style>
		h1 {
			font-family: verdana, sans-serif;
			font-size: 32px;
			color: #3b3b3b;
		}

		h2 {
			font-family: verdana, sans-serif;
			font-size: 18px;
			font-weight: normal;
			color: #3b3b3b;
		}
		strong {
			color: #7a0000;
		}

		a {
			font-family: verdana, sans-serif;
			color: #bf1f24;
			font-size: 14px;
		}
	</style>
</head>
<body>
	<h1>404 :(</h1>
	<h2>Neatradu: <strong><?= URL::to(URL::current()); ?></strong></h2>
	<a href="<?= URL::to( '/' ); ?>">Atgriezties uz lapu</a>
</body>
</html>