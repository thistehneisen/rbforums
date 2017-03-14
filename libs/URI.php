<?php

/**
 * class for parsing uri strings. It finds $_GET['data'] variable and splits into segments...
 * need to set rewrite rule in web server config ^(.*)$ index.php?data=$1
 */
class URI {
    /**
     * array of url segments
     */
    public static $segments = array();
    /**
     * plain URI
     */
    private static $rawUri = null;

    private static $requestUri = null;

    /**
     * gets and parses request uri
     * @param null|string $key
     * @return array
     */
    public static function segments($key = null) {
        if(empty(self::$segments)) {
            $url = '/';
            if(isset($_SERVER['REQUEST_URI'])) $url = $_SERVER['REQUEST_URI'];
            else if (isset($_GET['_url'])) $url = $_GET['_url'];

            self::$requestUri = $url;

            self::$rawUri = '/'.trim($url, '/');
            $data = explode('?', self::$rawUri);
            $data = array_shift($data);
            self::$rawUri = $data;
            $rewriteBase = Router::rewriteBase();
            if(!is_null($rewriteBase)) {
                $data = preg_replace($rewriteBase, '', $data);
                $data = trim($data, '/');
            }
            if(stristr($data, '/')) {
                self::$segments = explode('/', trim(htmlspecialchars($data), '/'));
            } else {
                self::$segments = array(htmlspecialchars($data));
            }
        }
        if($key !== null and isset(self::$segments[$key])) {
            return self::$segments[$key];
        }
        return self::$segments;
    }

    /**
     * plain uri
     * @return string
     */
    public static function raw()
    {
        if(self::$rawUri === null) {
            self::segments();
        }
        return self::$rawUri;
    }

    /**
     * plain uri
     * @return string
     */
    public static function requestUri()
    {
        if(self::$requestUri === null) {
            self::segments();
        }
        return self::$requestUri;
    }

    /**
     * uri segment by key
     * @param integer $key
     * @return string
     */
    public static function segment($key) {
        if(isset(self::$segments[$key])) {
            return self::$segments[$key];
        }
        return '/';
    }

    public static function base() {
        if(defined('BASE_URL')) {
            return BASE_URL;
        }

        $ssl = (arrayGet($_SERVER, 'HTTPS', false) === 'on') or (arrayGet($_SERVER, 'SERVER_PORT', 80) === '443');
//        dd($_SERVER, Router::rewriteBase());
        $tail = '/';
        $rewriteBase = Router::rewriteBase();
        if($rewriteBase) {
            $reqUri = arrayGet($_SERVER, 'REQUEST_URI', false);
            if($reqUri) {
                foreach($rewriteBase as $b) {
                    if(preg_match($b, $reqUri, $m)) {
                        $tail = $m[0].'/';
                        break;
                    }
                }

            }
        }
        if(isset($_SERVER['HTTP_HOST'])) {
            define('BASE_URL', 'http'.(!$ssl ? '' : 's').'://'.$_SERVER['HTTP_HOST'].$tail);
            return BASE_URL;
        }

        return 'localhost';
    }
}