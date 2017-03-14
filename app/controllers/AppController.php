<?php

class AppController extends BaseController {
	public $user = null;

	public function __construct() {
		parent::__construct();
        View::share('shareImg', URL::to('/assets/img/share.png'));
        View::share('shareTitle', Config::get( 'app.title', '' ));
        View::share('shareDesc', Config::get( 'app.description', '' ));
	}

	public function getIndex() {
		return $this->layout->add( 'content', View::make( 'app.intro') );
	}

	public function getExit() {
		Session::destroy();
		redirect( '/' );
	}
}