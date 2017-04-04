<?php

Router::rewriteBase('forum');

Router::detect('app', 'AppController');

Router::detect('admin', 'AdminController');
Router::post('validate-code', 'AppController@validate');
Router::post('register-form-1', 'AppController@registerOne');
Router::post('register-form-2', 'AppController@registerTwo');
Router::post('register-form-3', 'AppController@registerThree');
Router::post('register-form-4', 'AppController@registerContacts');
Router::post('get-suppliers', 'AppController@getSuppliers');
Router::get('/(.*)', 'AppController@getIndex');
Router::get('/', 'AppController@getIndex');

//Router::missing('AppController@getHuman');


//Router::get('/{perm}', function($perm) {
//    (new AppController())->getHuman($perm);
//});

//Router::post('/{perm}', function($perm) {
//	(new AppController())->postHuman($perm);
//});

//Router::get('/{perm}/done', function($perm) {
//	(new AppController())->getHuman($perm, true);
//});
//
//Router::get('/{perm}/{other}', function($data) {
//    var_dump($data);
//    die('Tthis is double');
//});