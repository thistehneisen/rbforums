<?php
$codes = [
    'QZKC',
    'CB25',
    '9X9C',
    'CPZ9',
    '8K0I',
    'JXJF',
    'PPCL',
    'Z4PQ',
    '493G',
    'L5LV',
    '2V7F',
    'K7PT',
    'RPBB',
    'NVQC',
    'K3YK',
    '7NBC',
    'WESI',
    'JEDM',
    '9L2U',
    'SRNK',
    'HZV4',
    'UMHF',
    'PFZX',
    '3A90',
    'O2I8',
    'GVUQ',
    'HWL9',
    'O9U5',
    '8PA2',
    'BSH1',
    '8HYB',
    'R7BG',
    'AUOQ',
    'QJH7',
    'G2G4',
    'CAAK',
    '0LNC',
    'D5DL',
    'MBXE',
    'J9VT',
    '3JKT',
    '320J',
    '4HOH',
    'SZ1T',
    'KP5X',
    'UIJH',
    'UHWD',
    'QR7T',
    'BSNE',
    'UOYZ',
    '6NGZ',
    'MHS6',
    '7X41',
    'GOJA',
    '5FOV',
    '7WPI',
    'OCXI',
    '1WH7',
    'JX65',
    'FZCN',
    'XGOD',
    '58OA',
    'OD6V',
    '9VEY',
    '8BG9',
    '7YHR',
    'WOWB',
    'N9YK',
    'QNYV',
    'WN5K',
    '0BGA',
    '7U8F',
    '5PPD',
    'N64J',
    'V1VI',
    'AU30',
    'I2WE',
    'P2ZQ',
    'DF1L',
    '990F',
    'ZQTM',
    'WX6S',
    'Z1AA',
    'WEBE',
    'G7T5',
    '9SWN',
    '8X8I',
    '69X6',
    'ZQSW',
    'OZON',
    '0ZXX',
    'D9CT',
    'G5ZQ',
    'YVD6',
    'SMOZ',
    'WM5V',
    'DYS1',
    'XHPY',
    'GNVU',
    'W7ND'
];
return [
    'on' => [
        'table' => 'codes',

        'posthook' => function () use ( $codes ) {
            foreach ( $codes as $code ) {
                ( new Codes() )->create( [ 'content' => $code ] );
            }
        }
    ],

    'down' => [
        'table' => 'codes',
        'drop' => false,
        'posthook' => function () use ( $codes ) {
            ( new Codes() )->whereIn( 'content', $codes )->delete();
        }
    ]
];