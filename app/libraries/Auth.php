<?php
Class Auth {
    protected static $user = null;

    public static function check()
    {
        return self::user() !== false;
    }

    public static function user()
    {
        if(is_null(self::$user)) {
            return self::authenticate();
        }
        return self::$user;
    }

    public static function authenticate($email = null, $password = null)
    {
        $userId = Session::get('user.id');
        if(is_null($userId)) {
            if(is_null($email) || is_null($password)) {
                self::$user = false;
            } else {
                self::$user = (new User())->where(['email' => $email, 'password' => self::salt([$password])])->get()->row();
                if(self::$user->isEmpty()) {
                    self::$user = false;
                    return false;
                }
                Session::set('user.id', self::$user->id);
                Session::set('user.salt', self::salt([self::$user->id]));
                return self::$user;
            }
        } else {
            if(self::$user) {
                return self::$user;
            } else {
                $userSalt = Session::get('user.salt');
                $localSalt = self::salt([$userId]);
                if($userSalt === $localSalt) {
                    self::$user = (new User())->where(['status' => 1, 'password !=' => ''])->find($userId);
                    return self::$user;
                }
            }
        }

        return self::$user;
    }

    public static function destroy()
    {
        Session::remove('user');
        self::$user = null;
    }

    public static function salt($attrs = []) {
        $salt = Config::get('app.salt', md5("I'll be back!"));
        if(!is_array($attrs)) $attrs = [$attrs];
        sort($attrs);
        return md5(implode('-', $attrs).$salt);
    }
}