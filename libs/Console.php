<?php
/**
 * Created by PhpStorm.
 * User: koko
 * Date: 08/04/15
 * Time: 13:04
 */

class Console {

    private $foregroundColors = [
        'black' => '0;30',
        'dark_gray' => '1;30',
        'blue' => '0;34',
        'light_blue' => '1;34',
        'green' => '0;32',
        'light_green' => '1;32',
        'cyan' => '0;36',
        'light_cyan' => '1;36',
        'red' => '0;31',
        'light_red' => '1;31',
        'purple' => '0;35',
        'light_purple' => '1;35',
        'brown' => '0;33',
        'yellow' => '1;33',
        'light_gray' => '0;37',
        'white' => '1;37',
    ];

    private $backgroundColors = [
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'light_gray' => '47',
    ];

    private $file = null;
    private $class = null;
    private $command = null;
    private $attributes = [];
    private $parameters = [];

    public function __construct($arguments = []) {
        $this->parseArguments($arguments);
    }

    public function parseArguments($arguments) {
        foreach($arguments as $k => $arg) {
            switch($k) {
                case 0: //self
                    $this->file = $arg;
                    break;
                case 1: //command
                    if(preg_match("/([\w\-]+):(.*)/", $arg, $matches))
                    {
                        $this->class = $matches[1];
                        $this->command = $matches[2];
                    } else {
                        $this->class = $arg;
                    }
                    break;
                default:
                    if(preg_match("/^\-\-(.*)=(.*)/", $arg, $matches)) {
                        $this->attributes[$matches[1]] = $matches[2];
                    } else if(preg_match("/^\-\-([\w\-]+)$/", $arg, $matches)) {
                        $this->attributes[$matches[1]] = true;
                    } else if(preg_match("/^\-([\w\-]+)$/", $arg, $matches)) {
                        $this->attributes[$matches[1]] = true;
                    }  else {
                        $this->parameters[] = $arg;
                    }
                break;
            }
        }
    }

    public function call($class = null) {
        if(is_null($class)) $class = $this->getClass();
        if($class) {
            try {
                new $class($this);
            } catch(Exception $e) {
                $this->error('Class '.$class.' Not found');
            }
        } else {
            $this->info('Please provide more parameters');
        }
    }

    public function getClass() {
        return $this->class;
    }

    public function getCommand() {
        return $this->command;
    }

    public function getParameter($key = null) {
        if(!is_null($key)) return arrayGet($this->parameters, $key);
        return $this->parameters;
    }

    public function getAttribute($key = null) {
        if(!is_null($key)) {
            if(is_array($key)) {
                $v = null;
                foreach($key as $k) {
                    $v = arrayGet($this->attributes, $k);
                    if(!is_null($v)) {
                        return $v;
                    }
                }
                return $v;
            } else {
                return arrayGet($this->attributes, $key);
            }
        }
        return $this->attributes;
    }

    public function info($text, $newLine = true) {
        $this->writeColored($text, 'yellow', null, $newLine);
    }

    public function success($text, $newLine = true) {
        $this->writeColored($text, 'green', null, $newLine);
    }

    public function error($text, $newLine = true) {
        $this->writeColored($this->line(2).' '.$text.' '.$this->line(), 'white', 'red', $newLine);
    }

    public function writeColored($text, $foregroundColor = null, $backgroundColor = null, $newLine = false)
    {
        $newText = '';
        if($foregroundColor) {
            if($fgc = arrayGet($this->foregroundColors, $foregroundColor, false)) {
                $newText .= "\033[".$fgc."m";
            }
        }

        if($backgroundColor) {
            if($bgc = arrayGet($this->backgroundColors, $backgroundColor, false)) {
                $newText .= "\033[".$bgc."m";
            }
        }

        $newText .= $text . "\033[0m";

        if($newLine) {
            $this->writeLine($newText);
        } else {
            $this->write($text);
        }
    }

    public function write($text) {
        echo $text;
    }

    public function writeLine($text) {
        $this->write($text);
        echo $this->line();
    }

    public function line($ct = 1) {
        $line = "\n";
        $lines = '';
        while($ct > 0) {
            $lines .= $line;
            $ct--;
        }
        return $lines;
    }


}