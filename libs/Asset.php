<?php
/**
 * Created by PhpStorm.
 * User: koko
 * Date: 25/11/14
 * Time: 13:39
 */

class Asset {
    private static $scripts = [];
    private static $css = [];

    public static function scripts()
    {
        $scripts = [];
        foreach(self::$scripts as $script) {
            $scripts[] = '<script type="text/javascript" src="'.$script.'"></script>';
        }
        return implode(PHP_EOL, $scripts).PHP_EOL;
    }

    public static function styles()
    {
        $styles = [];
        foreach(self::$css as $style) {
            $styles[] = '<link rel="stylesheet" type="text/css" href="'.$style.'" media="screen" />';
        }
        return implode(PHP_EOL, $styles).PHP_EOL;
    }

    public static function add($type = 'script', $name, $url, $onTop = false)
    {
        switch($type) {
            case 'script' :
                if(isset(self::$scripts[$name])) unset(self::$scripts[$name]);
                if($onTop) {
                    $arr = array_reverse(self::$scripts);
                    $arr[$name] = $url;
                    self::$scripts = array_reverse($arr);
                } else {
                    self::$scripts[$name] = $url;
                }
                break;
            case 'style' :
                if(isset(self::$css[$name])) unset(self::$css[$name]);
                if($onTop) {
                    $arr = array_reverse(self::$css);
                    $arr[$name] = $url;
                    self::$css = array_reverse($arr);
                } else {
                    self::$css[$name] = $url;
                }
                break;
        }
    }

    public static function addScript($name, $url, $top = false, $vendored = false)
    {
        if(!preg_match('/^(http|\/\/)+/', $url)) {
            if($vendored) {
                $url = 'vendor/'.$url;
                $phisicalUrl = BASE_PATH.$url;
            } else {
                $url = 'assets/'.$url;
                $phisicalUrl = BASE_PATH.$url;
            }
            if(file_exists($phisicalUrl) and ENVIRONMENT === 'production') {
                $url .= '?v='.filemtime($phisicalUrl);
            }
            $url = URI::base().$url;
        }
        self::add('script', $name, $url, $top);
    }

    public static function addStyle($name, $url, $onTop = false)
    {
        if(!preg_match('/^http/', $url)) {
            $phisicalUrl = BASE_PATH.'assets/'.$url;
            if(file_exists($phisicalUrl) and ENVIRONMENT === 'production') {
                $url .= '?v='.filemtime($phisicalUrl);
            }
            $url = URI::base().'assets/'.$url;
        }
        self::add('style', $name, $url, $onTop);
    }

    public static function GA($code) {
        if(empty($code)) return '';
        return sprintf("<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');ga('create', '%s', 'auto');ga('send', 'pageview');</script>"
            , $code);
    }
}