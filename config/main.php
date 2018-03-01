<?php
return [
    'db' => [
        'class' => 'Medoo\Medoo',
        'database_type' => DB_TYPE,
        'database_name' => DB_NAME,
        'server' => DB_HOST,
        'username' => DB_USER,
        'password' => DB_PWD,
        'charset' => DB_CHARSET,
        'port' => DB_PORT,
        'logging' => true
    ],
    "redis" => [
        "class" => "common\lib\MyRedis",
        "host" => REDIS_HOST,
        "port" => REDIS_PORT
    ],
    "log" => [
        "class"=>"common\lib\Log"
    ]
];