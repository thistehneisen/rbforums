<?php
/**
 * Created by PhpStorm.
 * User: koko
 * Date: 16/03/2017
 * Time: 21:14
 */

class Mail {

    private $__config = [];
    public $message = null;

    public function __construct($config = []) {
//        $defaults['']

        $this->message = Swift_Message::newInstance();
    }



}