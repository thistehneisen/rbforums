<?php
/**
 * Created by PhpStorm.
 * User: koko
 * Date: 27/11/14
 * Time: 11:38
 */

class User extends Models {
    protected $table = "users";

    public function __construct($socData = null, $socNetwork = 'twitter', $socObject = null, $accessToken = null) {
        parent::__construct();
        if(!is_null($socData)) {
            $data = userData($socData, $socNetwork, $socObject, $accessToken);
            $this->where('soc_id', $data['soc_id'])->get()->first();
            if(!$this->_items) {
                $this->create($data);
            }
        }
        return $this;
    }

    public static function login($userId, $socData, $network, $referrer = '/') {
        Session::set('user_id', $userId);
        Session::set('soc_data', $socData);
        Session::set('soc_network', $network);
        URL::redirect($referrer);
    }

    public static function logout() {
        Session::destroy();
        URL::redirect();
    }

    public function check() {
        if(Session::get('user_id') and Session::get('soc_network') and isset($this->_items->id)) {
            return true;
        }
        return false;
    }

    public static function init() {
        $userId = Session::get('user_id');
        $userSocData = Session::get('soc_data');
        $userSocNetwork = Session::get('soc_network');
        $user = new self;

        if($userId) {
            $user->find($userId);
            if($user->isEmpty() and $userSocData) {
                $user = new self($userSocData, $userSocNetwork);
                Session::set('user_id', $user->id);
            }
        }
        return $user;
    }
}