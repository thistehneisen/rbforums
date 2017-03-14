<?php

Class Migrate extends DB {
    private $cli = null;

    private $migrationSchema = [
        'table' => 'migrations',
        'fields' => [
            'migration' => 'string:255|nullable',
            'batch'     => 'integer:11|default:0',
        ],

    ];

    public function __construct(Console $cliObject) {
        parent::__construct();
        $this->cli = $cliObject;
        switch($this->cli->getCommand()) {
            case "rollback":
                $this->rollback();
                break;
            case "make":
                $this->make();
                break;
            default:
                $this->migrate();
                break;
        }
    }

    private function migrate() {
        if(!$this->tableExists('migrations')) {
            if($this->createTable(arrayGet($this->migrationSchema, 'table'), arrayGet($this->migrationSchema, 'fields'))) {
                $this->cli->success('Migrations configuration installed!!!');
            } else {
                $this->cli->error('Wrong configuration!'); exit(1);
            }
        }
        $this->from('migrations');
        $migrated = $this->select('migration')->get()->scalarArray();
        $newBatch = (int)$this->max('batch') + 1;

        $migrationPath = APP_PATH.'database/migrations';
        chdir($migrationPath);
        $migrations = glob('*.php');
        foreach ($migrations as $k => $v) {
            $migrations[$k] = preg_replace('/^(.*)\.php$/', '\\1', $v);
        }

        sort($migrations);
        $done = 0;
        foreach($migrations as $m) {
            if(!in_array($m, $migrated)) { //execute migration
                $sch = require_once($migrationPath.'/'.$m.'.php');
                $on = arrayGet($sch, 'on', null);
                if(!is_null($on)) {
                    $preHook = arrayGet($on, 'prehook', null);
                    $postHook = arrayGet($on, 'posthook', null);
                    // running pre hook
                    if(is_callable($preHook)) {
                        $preHook();
                    }
                    // running schema
                    $table = arrayGet($on, 'table');
                    $fields = arrayGet($on, 'fields', []);
                    $index = arrayGet($on, 'index', []);
                    if(!empty($fields)) {
                        $this->createTable($table, $fields, $index);
                    }

                    $alterFields = arrayGet($on, 'addFields', []);
                    $dropFields = arrayGet($on, 'dropFields', []);
                    if(!empty($alterFields)) {
                        $this->addFields($table, $alterFields);
                    }
                    if(!empty($dropFields)) {
                        $this->dropFields($table, $dropFields);
                    }

	                if(empty($fields)) {
		                if(!empty($index)) {
			                $keyIndex = arrayGet($index, 'key', false);
			                if($keyIndex) {
				                foreach($keyIndex as $ki) {
					                $this->index($table, $ki);
				                }
			                }
		                }
	                }

                    // running post hook
                    if(is_callable($postHook)) {
                        $postHook();
                    }

                    $this->cli->success('Migration: '.$m.' installed');
                    $this->table('migrations')->insert(['migration' => $m, 'batch' => $newBatch]);
                }
                $done++;
            }
        }
        if($done === 0) {
            $this->cli->info('Nothing to do!');
        }


    }

    private function rollback() {
        if(!$this->tableExists('migrations')) {
            $this->cli->error('Nothing to do!'); exit(1);
        }

        $this->from('migrations');
        $maxBatch = (int)$this->max('batch');
        $rollbackBatches = $this->select('migration')->where('batch', $maxBatch)->get()->scalarArray();
        rsort($rollbackBatches);
        $migrationPath = APP_PATH.'database/migrations';
        $done = 0;
        foreach($rollbackBatches as $m) {
            $file = $migrationPath.'/'.$m.'.php';
            if(file_exists($file)) { //execute rollback
                $sch = require_once($migrationPath.'/'.$m.'.php');
                $down = arrayGet($sch, 'down', null);
                $preHook = arrayGet($down, 'prehook', null);
                $postHook = arrayGet($down, 'posthook', null);
                // running pre hook
                if(is_callable($preHook)) {
                    $preHook();
                }
                // running schema
                $table = arrayGet($down, 'table');
                $drop = arrayGet($down, 'drop', false);
                $index = arrayGet($down, 'index');
                if(!is_null($index) and !is_null($table)) {
                    if(!is_array($index)) $index = [$index];
                    foreach($index as $ind) {
	                    $this->dropIndex($table, $ind);
                    }
                }

                $alterFields = arrayGet($down, 'addFields', []);
                $dropFields = arrayGet($down, 'dropFields', []);
                if(!empty($alterFields)) {
                    $this->addFields($table, $alterFields);
                }
                if(!empty($dropFields)) {
                    $this->dropFields($table, $dropFields);
                }

                if(!is_null($table) and $drop) {
                    $this->drop($table);
                }
                // running post hook
                if(is_callable($postHook)) {
                    $postHook();
                }
                $this->table('migrations')->where('migration', $m)->delete();
                $done++;
                $this->cli->success('Migration: '.$m.' rolled back');
            }
        }
        if($done === 0) {
            $this->cli->info('Nothing to do!');
        }

    }

    private function make() {
        $name = $this->cli->getParameter(0);
        if(!$name) {
            $this->cli->error('Please provide migration name!');
            exit(1);
        }

        $name = date('Ymd_Gis').'_'.$name.'.php';
        $path = APP_PATH.'database/migrations/'.$name;
        if(file_exists($path)) {
            $this->cli->error('File already exists');
            exit(1);
        }

        $text = <<<END
<?php return [
    'on' => [
        'table' => '',
        'fields' => [

        ],
        'addFields' => [

        ],
        'dropFields' => [

        ],
        'index' => [
            'key' => [

            ],
        ],
        'prehook' => function() {

        },
        'posthook' => function() {

        }
    ],

    'down' => [
        'table' => '',
        'drop' => false,
        'addFields' => [

        ],
        'dropFields' => [

        ],
        'index' => [

        ],
        'prehook' => function() {

        },
        'posthook' => function() {

        }
    ]
];
END;
        $f = fopen($path, 'w+');
        fputs($f, $text, 4096);
        if(fclose($f)) {
            $this->cli->success('Migration file "'.$name.'" created successfully!');
        } else {
            $this->cli->error('Something went wrong! Check permissions!');
        }

    }
}