<?php

return [
    'column_types' => [
        'float' => 'numeric',
        'double' => 'numeric',
        'decimal' => 'numeric',

        'bigint' => 'integer',
        'int' => 'integer',
        'integer' => 'integer',
        'tinyint' => 'integer',
        'smallint' => 'integer',

        'date' => 'date',
        'datetime' => 'date',
        'timestamp' => 'date',

        'boolean' => 'boolean',

        'string' => 'string',
        'text' => 'string',
        'varchar' => 'string',
        'enum' => 'string',

        'array' => 'array',

        'json' => 'json',

        'geometry' => 'geometry',
    ],
    'responseDontWrap' => [
        '/api/app/version/check',
        '/api/weapp/wechat_pay/notify'
    ]
];
