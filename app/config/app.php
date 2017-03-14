<?php
return [
    'debug' => false,

    //------------
    // 32 symb salt
    'salt' => env('SECURE_KEY', md5('RB konference')),
    'language' => 'en',

    'title' => 'RB Nosaukums',
    'description' => 'Aprakts',
	'ga_code' => null,
];