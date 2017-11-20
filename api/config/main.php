<?php
/**
 * zjw
 * Date: 2017/11/16
 */
return [
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
    'response' => [
        'class' => 'api\lib\Response'
    ],
    'request' => [
        'class' => 'api\lib\Request'
    ],
    'router' => [
        'class'=>'api\lib\Router'
    ]
];