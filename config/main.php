<?php
return [
    'db' => [
        'class' => 'Medoo\Medoo',
        'database_type' => 'mysql',
        'database_name' => 'test',
        'server' => '127.0.0.1',
        'username' => 'root',
        'password' => 'zjw',
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