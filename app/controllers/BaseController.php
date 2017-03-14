<?php

class BaseController {
    protected $layoutTemplate = "layouts.main";
    protected $user = null;

    function __construct()
    {
        Session::init(Input::get('session_hash', null));

	    // init layout
        $this->layout = View::initLayout($this->layoutTemplate);
    }

    function __destruct()
    {

    }
}