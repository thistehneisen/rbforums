<?php return [
    'on' => [
        'table' => 'form4',
        'fields' => [
            'id' => 'increments',
            'first_name' => 'string:100|nullable',
            'last_name' => 'string:100|nullable',
            'email' => 'string:100|nullable',
            'phone' => 'string:100|nullable',
            'message' => 'text|nullable',
            'pubstamp' => 'integer|default:0',
            'ip' => 'string:250|nullable',
            'agent' => 'string:250|nullable',
        ],
    ],

    'down' => [
        'table' => 'form4',
        'drop' => true,
    ]
];