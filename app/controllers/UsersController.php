<?php
/**
 * Created by PhpStorm.
 * User: koko
 * Date: 26/11/14
 * Time: 15:59
 */

class UsersController extends BaseController {

    public function getLoginTwitter() {
        $this->layout->layout = null;
        Session::set('callback_referer', Request::referer());
        $connection = Social::twitterConnection();
        $request_token = $connection->getRequestToken(URL::to('users/twittercallback'));

        $oauthToken = arrayGet($request_token, 'oauth_token', null);
        $oauthSecret = arrayGet($request_token, 'oauth_token_secret', null);
        if($oauthToken and $oauthSecret) {
            Session::set('oauth_token', $oauthToken);
            Session::set('oauth_token_secret', $oauthSecret);
        }
        switch ($connection->http_code) {
            case 200:
                $url = $connection->getAuthorizeURL($request_token);
                URL::redirect($url);
                break;
            default:
                URL::redirect('/');
        }
    }

    public function getTwitterCallback() {
        $oauthToken = Input::get('oauth_token', false);
        $sesOauthToken = Session::get('oauth_token', false);
        if($oauthToken and $oauthToken !== $sesOauthToken) {
            Session::destroy();
            URL::redirect('/');
        } else if($oauthToken and !$sesOauthToken) {
            Session::set('oauth_token', $oauthToken);
        }

        $connection = Social::twitterConnection();

        $accessToken = $connection->getAccessToken(Input::get('oauth_verifier'));
        Session::set('access_token', $accessToken);
        Session::set('oauth_token', $accessToken['oauth_token']);
        Session::set('oauth_token_secret', $accessToken['oauth_token_secret']);
        if ($connection->http_code == 200) {
            Session::set('status', 'verified');

            $connection = Social::twitterConnection($accessToken['oauth_token'], $accessToken['oauth_token_secret']);

            $userData = $connection->get('account/verify_credentials');
            if(isset($userData->id)) {
                $this->user = new User($userData, 'twitter', $connection);
                $referer = Session::get('callback_referer', '/');
                if($referer) {
                    Session::remove('callback_referer');
                }
                Social::twitterFollow(Config::get('social.twt_user'), $connection);
                User::login($this->user->id, $userData, 'twitter', $referer);
            }
        } else {
            Session::destroy();
            URL::redirect('/');
        }
    }

    public function getLogout() {
        User::logout();
    }

    public function getReloadData() {
        $this->user->soc_id = 0;
        $this->user->save();
        $this->getLogout();
    }
}