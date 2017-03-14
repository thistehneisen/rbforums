<?php
/**
 * Created by PhpStorm.
 * User: koko
 * Date: 01/12/14
 * Time: 10:47
 */

class Social {

    public static $twitterConnection = null;

    public static function twitterConnection($oauthToken = null, $oauthSecret = null) {
        $key = Config::get('social.twt_app_key');
        $secret = Config::get('social.twt_app_secret');

        if(!is_null(self::$twitterConnection) and is_null($oauthToken) and is_null($oauthSecret)) {
            return self::$twitterConnection;
        }

        if(!$oauthToken) {
            $oauthToken = Session::get('oauth_token');
        }

        if(!$oauthSecret) {
            $oauthSecret = Session::get('oauth_token_secret');
        }

        if($key and $secret) {
            self::$twitterConnection = new TwitterOAuth(
                $key,
                $secret,
                $oauthToken,
                $oauthSecret
            );
        }
        return self::$twitterConnection;
    }

    public static function twitterFollow($user, $connection = null) {
        if(!$connection) {
            $connection = self::twitterConnection();
        }

        if(!$user or !$connection) return false;

        $followStatus = $connection->get('users/lookup', array('screen_name' => $user));

        if(isset($followStatus[0]) and isset($followStatus[0]->following) and $followStatus[0]->following == false) {
            return $connection->post('friendships/create', array('screen_name' => $user, 'include_entities' => 1));
        }
        return false;
    }

    public static function twitterTweet($status, $connection = null) {
        if(!$connection) {
            $connection = self::twitterConnection();
        }

        if(!$connection) return false;

        return $connection->post('statuses/update',
            ['status' => $status]
        );
    }

    public static function twitterTweetPhoto($status = '', $image, $connection = null) {
        if(!$connection) {
            $connection = self::twitterConnection();
        }

        if (function_exists('curl_file_create')) {
            $image = curl_file_create($image, 'image/jpeg', 'img');
        } else {
            $image = "@{$image};filename=img";
        }

        return $connection->upload('statuses/update_with_media',
            ['status' => $status, 'media[]' => $image]
        );
    }
}