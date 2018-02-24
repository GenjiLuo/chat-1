<?php
namespace server;
use Swoole\WebSocket\Server;
use server\classes\Operation;
/**
 * zjw
 * 2017/11/6
 */
class WsServer{

    public static $server;

    public static function init(){

        self::$server= new Server(SERVER_HOST,SERVER_PORT);

        self::$server->on("open",function(Server $server,$frame){
            echo "connect";
        });
//            Operation::open($server,$frame);
//        });

        self::$server->on("message",function(Server $server,$frame){
           Operation::message($server,$frame);
        });

        self::$server->on("request",function(Server $server,$response){

        });

        self::$server->on("close",function(Server $server,$fd,$reactorId){
            Operation::close($server,$fd,$reactorId);
        });

        self::$server->start();
    }



}