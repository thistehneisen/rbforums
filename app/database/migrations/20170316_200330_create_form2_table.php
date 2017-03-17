<?php return [
    'on' => [
        'table' => 'form2',
        'fields' => [
            'id' => 'increments',
            'first_name' => 'string:100|nullable',
            'last_name' => 'string:100|nullable',
            'salutation' => 'string:100|nullable',
            'title' => 'string:100|nullable',
            'email' => 'string:100|nullable',
            'phone' => 'string:100|nullable',
            'need_visa_invite' => 'integer:4|default:0',
            'info_stand' => 'integer:4|default:0',
            'full_suppliers_list' => 'integer:4|default:0',
            'cs_visit' => 'integer:4|default:0',
            'company' => 'string:200|nullable',
            'industry' => 'string:200|nullable',
            'position' => 'string:200|nullable',
            'country' => 'string:100|nullable',
            'city' => 'string:100|nullable',
            'pubstamp' => 'integer|default:0',
            'approved' => 'integer:4|default:0',
            'ip' => 'string:250|nullable',
            'agent' => 'string:250|nullable',
        ],
    ],

    'down' => [
        'table' => 'form2',
        'drop' => true,
    ]
];