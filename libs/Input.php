<?php
/**
 * Created by PhpStorm.
 * User: koko
 * Date: 27/11/14
 * Time: 11:19
 */

class Input {
    public static function get($key, $default = null, $type = 'BOTH') {
        $type = strtoupper($type);
        switch($type) {
            case 'GET':
                return arrayGet($_GET, $key, $default);
            break;
            case 'POST':
                return arrayGet($_POST, $key, $default);
                break;
            default:
                return arrayGet($_REQUEST, $key, $default);
        }
    }

    public static function all($type = 'BOTH') {
        $type = strtoupper($type);
        switch($type) {
            case 'GET':
                return $_GET;
                break;
            case 'POST':
                return $_POST;
                break;
            default:
                return $_REQUEST;
        }
    }
}