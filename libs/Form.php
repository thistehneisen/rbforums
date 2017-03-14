<?php
/**
 * Created by PhpStorm.
 * User: koko
 * Date: 20/04/15
 * Time: 15:08
 */

class Form {

    public static function open($url = '', $method = 'POST', $multipart = false, $attributes = []) {
        $attrs = '';
        foreach ($attributes as $key => $val) {
            $attrs .= ' ' . $key.'="' . $val . '"';
        }
        return sprintf('<form action="%s" method="%s"%s%s>'
            , $url
            , $method
            , ($multipart ? ' enctype="multipart/form-data"' : '')
            , $attrs
        );
    }

    public static function close()
    {
        return "</form>";
    }

    public static function input($name, $value, $attributes = [])
    {
        $attrs = ['name' => $name, 'id' => $name, 'type' => 'text', 'value' => $value];
        if(is_null($value)) unset($attrs['value']);
        $attrs = array_merge($attrs, $attributes);
        $html = '<input';
        foreach ($attrs as $key => $val) {
            $html .= ' ' . $key.'="' . $val . '"';
        }
        $html .= ' />';
        return $html;
    }

    public static function text($name, $value, $attributes = [])
    {
        return self::input($name, $value, array_merge(['type' => 'text'], $attributes));
    }

    public static function submit($name, $value, $attributes = [])
    {
        return self::input($name, $value, array_merge(['type' => 'submit'], $attributes));
    }

    public static function password($name, $value, $attributes = [])
    {
        return self::input($name, $value, array_merge(['type' => 'password'], $attributes));
    }

    public static function hidden($name, $value, $attributes = [])
    {
        return self::input($name, $value, array_merge(['type' => 'hidden'], $attributes));
    }

    public static function file($name, $attributes = [])
    {
        return self::input($name, null, array_merge(['type' => 'file'], $attributes));
    }

    public static function textarea($name, $value, $attributes = [])
    {
        $attrs = ['name' => $name, 'id' => $name, 'rows' => 5, 'cols' => 10];
        $attrs = array_merge($attrs, $attributes);
        $html = '<textarea';
        foreach ($attrs as $key => $val) {
            $html .= ' ' . $key.'="' . $val . '"';
        }
        $html .= '>' . $value . '</textarea>';
        return $html;
    }

    public static function button($name, $value, $attributes = [])
    {
        $attrs = ['name' => $name, 'id' => $name];
        $attrs = array_merge($attrs, $attributes);
        $html = '<button';
        foreach ($attrs as $key => $val) {
            $html .= ' ' . $key.'="' . $val . '"';
        }
        $html .= '>' . $value . '</button>';
        return $html;
    }

    public static function select($name, $data = [], $default = null, $attributes = [])
    {
        $attrs = ['name' => $name, 'id' => $name];
        $attrs = array_merge($attrs, $attributes);
        $html = '<select';
        foreach ($attrs as $key => $value) {
            $html .= ' ' . $key.'="' . $value . '"';
        }
        $html .= '>';
        foreach($data as $val => $title) {
            $html .= '<option value="' . $val . '"' . ($val == $default ? ' selected="true"' : '') . '>' . $title . '</option>';
        }
        $html .= '</select>';

        return $html;
    }

    public static function label($for, $value = null, $attributes = [])
    {
        $attrs = ['for' => $for];
        if(is_null($value)) $value = $for;
        $attrs = array_merge($attrs, $attributes);
        $html = '<label';
        foreach ($attrs as $key => $v) {
            $html .= ' ' . $key.'="' . $v . '"';
        }
        $html .= '>' . $value . '</label>';

        return $html;
    }
}