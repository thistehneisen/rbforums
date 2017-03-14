<?php
use Dotenv\Dotenv;

ini_set('gd.jpeg_ignore_warning', 1);

require ("vendor/autoload.php");
$dotEnv = new Dotenv(__DIR__);
$dotEnv->load();

//paths
define('BASE_PATH', dirname(__FILE__).'/');
define('APP_PATH', BASE_PATH.'app/');
define('LIB_PATH', BASE_PATH.'libs/');

require_once(LIB_PATH.'helpers.php');

$environments = [
    'local' => ['local', 'dev.koko.lv', '127.0.0.1', '^ekni.*', 'proxima', '192.168.88.15'],
    'test' => ['test.clicks.lv'],
    'production' => []
];

$defaultEnvironment = 'production';

//--- DO NOT EDIT FURTHER IF YOU DO NOT KNOW WHAT ARE YOU DOING!!! ---- //

date_default_timezone_set('Europe/Riga');

$environment = env('ENVIRONMENT', false);

if($environment === false) { //fallback to host method
	$hostHard = strtolower(gethostname());
	$hostName = (isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : null);

// getting environment
	$environment = $defaultEnvironment;
	foreach($environments as $env => $hosts) {
		if(in_array($hostHard, $hosts) || in_array($hostName, $hosts)) {
			$environment = $env;
			break;
		} else {
			foreach ($hosts as $h) {
				if(preg_match('/'.$h.'/i', $hostHard)) {
					$environment = $env;
					break;
				}
			}
		}
	}

	if(isset($argv) and count($argv)) {
		foreach($argv as $a) {
			if(preg_match('/\-\-env=(.*)/', $a, $m)) {
				$environment = trim($m[1]);
			}
		}
	}
}

define('ENVIRONMENT', $environment);

if(ENVIRONMENT != 'production') {
	error_reporting(E_ALL);
} else {
	error_reporting(0);
}