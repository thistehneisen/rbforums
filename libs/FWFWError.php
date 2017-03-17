<?php
/**
 * Created by PhpStorm.
 * User: koko
 * Date: 27/11/14
 * Time: 15:17
 */

class FWError {
    public static function raise($code = 404)
    {
        URL::setHeaders($code);
        switch($code) {
            case 404:
            default:
                echo View::make('errors.404');
                break;
        }
        exit;

    }
}