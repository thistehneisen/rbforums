<?php return [
    'on' => [
        'table' => 'form3',
        'fields' => [
            'id' => 'increments',
            'name_surname' => 'string:200|nullable',
            'email' => 'string:100|nullable',
            'phone' => 'string:100|nullable',
            'day_1' => 'integer:4|default:0',
            'day_2' => 'integer:4|default:0',
            'position' => 'string:200|nullable',
            'name_of_media' => 'string:200|nullable',
            'website' => 'string:200|nullable',
            'pubstamp' => 'integer|default:0',
            'approved' => 'integer:4|default:0',
            'ip' => 'string:250|nullable',
            'agent' => 'string:250|nullable',
        ],
    ],

    'down' => [
        'table' => 'form3',
        'drop' => true,
    ]
];