<?php
class Config {
    public static $configs = [];

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public static function get($key, $default = null) {
        $key = explode('.', $key);

        if($key) {
            $curConfig = null;
            if(isset(self::$configs[$key[0]])) {
                $curConfig = self::$configs[$key[0]];
            } else {
                $curConfig = requireByEnv(ENVIRONMENT, $key[0].'.php');
                static::$configs[$key[0]] = $curConfig;
            }

            if($curConfig !== null and is_array($curConfig)) {
                for($i = 1; $i < count($key); $i++) {
                    if(isset($curConfig[$key[$i]])) {
                        $curConfig = $curConfig[$key[$i]];
                    } else {
                        $curConfig = $default;
                        break;
                    }
                }
                return $curConfig;
            }
        }

        return $default;
    }

    /**
     * @param $key
     * @param $val
     * @return bool
     */
    public static function set($key, $val) {
        $default = self::get($key);
        if($default == $val) return true;
        $key = explode('.', $key);
        if($key) {
            if(!isset(self::$configs[$key[0]])) {
                self::$configs[$key[0]] = [];
            }
            $done = $val;
            for($i = (count($key) -1); $i >= 0; $i--) {
                $done = [$key[$i] => $done];
            }
            self::$configs = array_merge_recursive(self::$configs, $done);

        }
        return true;
    }
}