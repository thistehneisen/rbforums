<?php
/**
 * Created by PhpStorm.
 * User: koko
 * Date: 15/04/15
 * Time: 16:15
 */

class Language {
    /** all translations **/
    private static $translations = [];

    public static function translate($key, $vars = null)
    {
        $lang = Config::get('app.language');
        if(is_null($lang)) return $key;

        $existing = arrayGet(self::$translations, $lang, []);

        if(stristr($key, '.')) {
            $fileName = explode('.', $key)[0];
        } else {
            $fileName = $key;
        }

        $fileTexts = arrayGet($existing, $fileName);
        if(is_null($fileTexts)) {
            $file = APP_PATH.'lang/'.$lang.'/'.$fileName.'.php';
            if(file_exists($file)) {
                $existing[$lang][$fileName] = require($file);
            } else {
                $existing[$lang][$fileName] = [];
            }
        }

        $text = arrayGet($existing[$lang], $key, $key);
        if(is_array($vars)) {
            foreach($vars as $k => $v) {
                $text = str_replace(':'.$k, $v, $text);
            }
        }
        return $text;
    }
}