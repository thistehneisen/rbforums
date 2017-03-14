<?php
class MySessionHandler implements SessionHandlerInterface
{
    private $savePath;

    public function open($savePath, $sessionName)
    {
        if($savePath == '') {
            session_save_path('/tmp');
            $savePath = '/tmp';
        }
        $this->savePath = $savePath;
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }

        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        return (string)@file_get_contents("$this->savePath/clock_sess_$id");
    }

    public function write($id, $data)
    {
        return file_put_contents("$this->savePath/clock_sess_$id", $data) === false ? false : true;
    }

    public function destroy($id)
    {
        $file = "$this->savePath/clock_sess_$id";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    public function gc($maxLifeTime)
    {
        foreach (glob("$this->savePath/clock_sess_*") as $file) {
            if (filemtime($file) + $maxLifeTime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }
}


class Session {

    private $sess;
    protected static $_instance = null;

    private $sessionLifetime = null; //in secconds

    function __construct($sesId = null, $maxLifeTime = null)
    {
        $this->maxLifeTime($maxLifeTime);
        $this->start($sesId);
        $this->sess = $_SESSION;
    }

    public function maxLifeTime($maxLifeTime)
    {
        if(is_null($maxLifeTime)) {
            $this->sessionLifetime = (60*60*24*365); //setting for one year
        } else {
            $this->sessionLifetime = $maxLifeTime;
        }
    }

    public static function init($sesId = null)
    {
        self::getInstance($sesId);
    }

    public static function getInstance($sesId = null)
    {
        if(is_null(self::$_instance)) {
            self::$_instance = new self($sesId);
        }
        return self::$_instance;
    }

    private function start($sessionId = null)
    {
        if(session_id() == ''){ //If no session exists, start new
            ini_set('session.gc_maxlifetime', $this->sessionLifetime);
            ini_set('session.cookie_lifetime', $this->sessionLifetime);
            session_set_cookie_params($this->sessionLifetime);

            $handler = new MySessionHandler();
            session_set_save_handler($handler, true);

            if($sessionId) session_id($sessionId);
            session_start();
        }
    }

    public static function get($key, $default = null)
    {
        $o = self::getInstance();
        return arrayGet($o->sess, $key, $default);
    }

    public static function set($key, $data)
    {
        $o = self::getInstance();
        arraySet($o->sess, $key, $data);
        arraySet($_SESSION, $key, $data);
    }

    public static function remove($key)
    {
        arrayForget($_SESSION, $key);
        $o = self::getInstance();
        arrayForget($o->ses, $key);
    }

    public static function all()
    {
        return $_SESSION;
    }

    public static function setFlash($key, $value = null)
    {
        $flash = self::get('flash', []);
        arraySet($flash, $key, $value);
        self::set('flash', $flash);
    }

    public static function flash($key, $alter = null) {
        $flash = self::get('flash', []);
        $data = arrayGet($flash, $key, $alter);
        self::remove('flash.'.$key);
        return $data;
    }

    public static function destroy() {
        session_destroy();
    }
}
?>