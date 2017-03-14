<?php return [
    'on' => [
        'table' => 'users',
        'fields' => [
            'id' => 'increments',
            'soc_id' => 'bigInteger|default:0',
            'name' => 'string:100|nullable',
            'surname' => 'string:100|nullable',
            'name_surname' => 'string:200|nullable',
            'nick' => 'string:50|nullable',
            'email' => 'string:200|nullable',
            'city' => 'string:200|nullable',
            'age' => 'integer|default:0',
            'adult' => 'integer:4|default:0',
            'pubstamp' => 'integer:4|default:0',
            'img_url' => 'string:250|nullable',
            'ip' => 'string:250|nullable',
            'agent' => 'string:250|nullable',
            'user_key' => 'string:250|nullable',
            'gender' => 'string:20|nullable',
            'network_type' => 'string:20|nullable',
            'friend' => 'integer|default:0',
            'friend_count' => 'integer|default:0',
            'user_type' => 'integer:4|default:0',
            'shared' => 'integer:4|default:0',
            'invited' => 'integer|default:0',
            'status' => 'integer:4|default:0',
            'is_admin' => 'integer:4|default:0',
            'password' => 'string:50|nullable',
            'access_token' => 'string:250|nullable',
        ],
        'index' => [
            'key' => [
                'name',
                ['name', 'surname']
            ],
        ],
        'prehook' => function() {

        },
        'posthook' => function() {
            (new User())->create([
                'name' => 'Admin',
                'surname' => 'Admin',
                'name_surname' => 'Admin Admin',
                'email' => 'sys@sys.click',
                'status' => 1,
                'is_admin' => 1,
                'password' => Auth::salt('test123'),
            ]);
        }
    ],

    'down' => [
        'table' => 'users',
	    'drop' => true,
        'index' => [
            'name',
            ['name', 'surname']
        ],
    ]
];