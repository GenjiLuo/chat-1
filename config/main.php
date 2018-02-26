<?php
return [
    'db' => [
        'class' => 'Medoo\Medoo',
        'database_type' => 'mysql',
        'database_name' => 'test1',
        'server' => 'localhost',
        'username' => 'root',
        'password' => '123456',
        'charset' => 'utf8',
        'port' => 3306,
        'logging' => true
    ],
    "redis" => [
        "class" => "common\lib\MyRedis",
        "host" => "172.16.1.80",
        "port" => "6379"
    ],
    "log" => [
        "class"=>"common\lib\Log"
    ]
];