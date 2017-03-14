<?php

require_once('environment.php');

Class CLI extends Console{
    private $registered = ['migrate' => 'Migrate'];
    // create command: php do command:make <name>
    // set maintenance: php do maintenance:on
    // unset maintenance: php do maintenance:off

    public function __construct($arguments)
    {
        parent::__construct($arguments);
        $this->gatherCommands();
        if(isset($this->registered[$this->getClass()])) {
            $this->call($this->registered[$this->getClass()]);
        } elseif($this->getClass() === 'command' and $this->getCommand() === 'make') {
            $this->makeCommand();
        } elseif($this->getClass() === 'maintenance') {
            if($this->getCommand() === 'off') {
                $this->maintenance(true);
            } else {
                $this->maintenance();
            }
        } else {
            $this->error('Command is not registered!'); exit(1);
        }
    }

    public function gatherCommands()
    {
        $commandRegister = APP_PATH.'commands.php';
        if(file_exists($commandRegister)) {
            $commands = require($commandRegister);
            if(is_array($commands) and !empty($commands)) {
                $this->registered = array_merge($this->registered, $commands);
            }
        }
    }

    public function makeCommand()
    {
        $name = $this->getParameter(0);
        if(!$name) {
            $this->error('Please provide command name!');
            exit(1);
        }

        $fileName = $name.'.php';
        $path = APP_PATH.'commands/'.$fileName;
        if(file_exists($path)) {
            $this->error('File already exists');
            exit(1);
        }

        $name = ucfirst($name);

        $text = <<<END
<?php
Class $name {
    private \$cli = null;

    public function __construct(Console \$cliObject) {
        \$this->cli = \$cliObject;
        switch(\$this->cli->getCommand()) {
            default:
                echo \$this->cli->getCommand();
                break;
        }
    }
}
END;
        $f = fopen($path, 'w+');
        fputs($f, $text, 4096);
        if(fclose($f)) {
            $this->success('Command file "'.$fileName.'" created successfully!');
            $this->info('Do not forget to register new command in app/commands.php file');
        } else {
            $this->error('Something went wrong! Check permissions!');
        }
        chdir(BASE_PATH);
        exec('composer dump-autoload');
    }

    public function maintenance($off = false)
    {
        if($off) {
            if(file_exists(BASE_PATH.'maintenance')) {
                unlink(BASE_PATH.'maintenance');
            }
            $this->success('Maintenance mode is off!');
        } else {
            touch(BASE_PATH.'maintenance');
            $this->success('Maintenance mode is now on!');
        }
    }
}

(new CLI($argv));