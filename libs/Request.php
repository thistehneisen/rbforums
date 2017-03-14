<?php
/**
 * Created by PhpStorm.
 * User: koko
 * Date: 25/11/14
 * Time: 10:16
 */

class Request {
    public static function method() {
        $allowedMethods = ['get', 'post'];
        $method = 'GET';
        if(isset($_SERVER['REQUEST_METHOD'])) $method = $_SERVER['REQUEST_METHOD'];
        if(!in_array(strtolower($method), $allowedMethods)) $method = 'GET';
        return $method;
    }

    public static function referer() {
        return arrayGet($_SERVER, 'HTTP_REFERER', '/');
    }

    public static function ip() {
        return arrayGet($_SERVER, 'REMOTE_ADDR', 'terminal');
    }

    public static function agent() {
        return arrayGet($_SERVER, 'HTTP_USER_AGENT', 'terminal');
    }
}