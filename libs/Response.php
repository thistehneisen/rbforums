<?php
/**
 * Created by PhpStorm.
 * User: koko
 * Date: 21/09/16
 * Time: 13:12
 */

class Response {

	/**
	 * @param $data mixed
	 * @param int $status
	 * @param array $headers
	 *
	 * @return null|string
	 */
	public static function json($data, $status = 200, $headers = [])
	{
		if(!is_array($data) && !is_object($data)) {
			$data = [$data];
		}
		URL::setHeaders($status, array_merge($headers, ['Content-Type: application/json']));
		return View::json($data);
	}
}