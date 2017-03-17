<?php
/**
 * Created by PhpStorm.
 * User: koko
 * Date: 16/03/2017
 * Time: 18:23
 */

class Validator {

    private $__config = [];
    private $__errors = [];

    public function __construct($config) {
        $this->__config = $config;
    }

    public function execute($data) {
        $passed = true;
        if(!is_array($data)) $data = [$data];
        if(count($this->__config) > 0) {
            foreach ( $this->__config as $field => $rules ) {
                $rules = $this->prepareRules($rules);
                $value = arrayGet($data, $field, '');
                foreach ($rules as $rule) {
                    if('trim' == $rule[0]) {
                        $value = trim($value);
                        $_GET[$field] = $value;
                        $_REQUEST[$field] = $value;
                        $_POST[$field] = $value;
                    }
                }
                foreach ($rules as $rule) {
                    switch($rule[0]) {
                        case 'required':
                            if(!self::required($value)) {
                                $this->setError($field, trans('validator.required', ['field' => $field]));
                                $passed = false;
                            }
                            break;

                        case 'email':
                            if(!self::isEmail($value)) {
                                $this->setError($field, trans('validator.required', ['field' => $field]));
                                $passed = false;
                            }
                            break;

                        case 'min':
                            if(!self::min($value, arrayGet($rule, 1, 3))) {
                                $this->setError($field, trans('validator.required', ['field' => $field]));
                                $passed = false;
                            }
                            break;
                    }
                }
            }
        }

        return $passed;
    }

    private function prepareRules($rule) {
        $rule = explode("|", $rule);
        foreach ($rule as $k => $r) {
            $rule[$k] = explode(':', $r);
        }

        return $rule;
    }

    private function setError($field, $message) {
        if(!is_array($this->__errors[$field])) $this->__errors[$field] = [];
        $this->__errors[$field][] = $message;
    }

    public function getErrors($field = null) {
        if(!$field) return $this->__errors;
        return arrayGet($this->__errors, $field);
    }

    public static function required($value) {
        if(empty($value)) {
            return false;
        }
        return true;
    }

    public static function isEmail($email) {
        return filter_var( $email, FILTER_VALIDATE_EMAIL );
    }


    public static function min($text, $symbols) {
        return mb_strlen($text) >= $symbols;
    }


}