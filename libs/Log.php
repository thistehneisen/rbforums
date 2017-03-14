<?php

/**
 * Created by PhpStorm.
 * User: koko
 * Date: 03/11/2016
 * Time: 10:06
 */
class Log {

	public static function put($data)
	{
		$logDir = BASE_PATH.'storage/log/';
		$file = $logDir.'app-log-'.date('d-m-Y');
		if(!file_exists($logDir)) {
			mkdir($logDir, 0777, true);
		}
		$time = '['.date('d-m-Y G:i:s').' | '. Request::ip() .'] ';
		file_put_contents($file, $time.$data.PHP_EOL, FILE_APPEND);
	}
}