<?php
return [
    "redis" => [
        "class" => "lib\MyRedis",
        "host" => "192.168.1.196",
        "port" => "6379"
    ],
    'db' => [
        'class' => 'Medoo\Medoo',
        'database_type' => 'mysql',
        'database_name' => 'test1',
        'server' => '192.168.1.196',
        'username' => 'root',
        'password' => '123456',
        'charset' => 'utf8',
        'port' => 3306,
        'logging' => true
    ],
];