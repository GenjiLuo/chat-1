<?php
namespace server\ws;
use common\interfaces\ServerInterface;
use Swoole\WebSocket\Server;
use server\classes\Operation;

class WsServer implements ServerInterface {

    public  function run(){

        $server= new Server(SERVER_HOST,WS_SERVER_PORT);

        $server->on("open",function(Server $server,$frame){
            echo "connect";
        });
//            Operation::open($server,$frame);
//        });

        $server->on("message",function(Server $server,$frame){
           Operation::message($server,$frame);
        });

        $server->on("request",function(Server $server,$response){
        });

        $server->on("close",function(Server $server,$fd,$reactorId){
            Operation::close($server,$fd,$reactorId);
        });

        $server->start();
    }



}