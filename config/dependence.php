<?php
return [
    "common\lib\MyRedis" => [
        "host" => REDIS_HOST,
        "port" => REDIS_PORT
    ],
    "Medoo\Medoo"=> [
        "options"=>[
        'database_type' => 'mysql',
        'database_name' => DB_NAME,
        'server' => DB_HOST,
        'username' => DB_USER,
        'password' => DB_PWD,
        // [optional]
        'charset' => 'utf8mb4',
        'port' => DB_PORT,
    ]]
];