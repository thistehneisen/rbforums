<?php

//Router::rewriteBase('messenger-bot');

Router::detect('app', 'AppController');

Router::detect('admin', 'AdminController');
Router::get('/start', 'AppController@getStart');
Router::get('/rules', 'AppController@getRules');
Router::get('/{perm}', 'AppController@getTest');
Router::post('/{perm}', 'AppController@postTest');
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