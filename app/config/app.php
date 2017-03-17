<?php
return [
    'debug' => false,

    //------------
    // 32 symb salt
    'salt' => env('SECURE_KEY', md5('RB konference')),
    'language' => 'en',
    'title' => 'Rail Baltica Global Forum 2017, 24-25 April, Riga, Latvia',
    'description' => 'Rail Baltica Global Forum 2017 “Rail Baltica – Building a New Economic Corridor” is the milestone international two-day event of the Rail Baltica project in 2017',
	'ga_code' => 'UA-53029341-27',
    'email' => env('SPAM_MAIL', 'railbaltica@vilands.lv'),
];