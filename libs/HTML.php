<?php
/**
 * Created by PhpStorm.
 * User: koko
 * Date: 20/04/15
 * Time: 17:00
 */

class HTML {
    public static function link($url = '#', $value = null, $attributes = [])
    {
        $html = '<a href="' . $url . '"';
        $value = (is_null($value) ? $url : $value);
        foreach ($attributes as $key => $val) {
            $html .= ' ' . $key.'="' . $val . '"';
        }
        $html .= '>' . $value . '</a>';
        return $html;
    }
}