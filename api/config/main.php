<?php
/**
 * zjw
 * Date: 2017/11/16
 */
return [
    'db' => [
        'class' => 'Medoo\Medoo',
        'database_type' => 'mysql',
        'database_name' => 'test',
        'server' => '192.168.1.196',
        'username' => 'root',
        'password' => '123456'
    ],
    'response' => [
        'class' => 'api\lib\Response'
    ],
    'router' => [
        'class'=>'api\lib\Router'
    ]
];