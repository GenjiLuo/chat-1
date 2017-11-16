<?php
use Swoole\WebSocket\Server;
use classes\Operation;
/**
 * zjw
 * Date: 2017/11/6
 */
class ChatServer{

    public static $server;

    public  static $app;

    public  static function init(Api $app){

        self::$app = $app;

        self::$server= new Server(SERVER_HOST,SERVER_PORT);

        self::$server->on("open",function(Server $server,$frame){
            Operation::open($server,$frame);
        });


        self::$server->on("message",function(Server $server,$frame){
           Operation::message($server,$frame);
        });


        self::$server->on("request",function(Server $server,$response){

        });


        self::$server->on("close",function(Server $server,$fd,$reactorId){
            self::$app->operation->close($server,$fd,$reactorId);
        });

        self::$server->start();
    }



}