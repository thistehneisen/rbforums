<?php
//$greenIPS = ['81.198.209.126', 'localhost', '127.0.0.1', '192.168.88.15', '::1'];
//if(isset($_SERVER['REMOTE_ADDR']) && !in_array($_SERVER['REMOTE_ADDR'], $greenIPS) ) {
//	die('www.nosutipastkarti.lv');
//}

if( file_exists('maintenance') and !isset($_GET['debug'])) {
    require('offline.php');
} else {
    define('VERSION', '1.0');
    define('NAME', 'CLOCK');
    require_once('environment.php');
    include_once(APP_PATH.'start.php');
}