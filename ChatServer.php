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

    public  static function init(){

        require_once "./App.php";
        require_once "./config/config.php";
        require_once "./config/key.php";
        $main = require "./config/main.php";

        self::$app = new App($main);

        self::$server= new Server(SERVER_HOST,SERVER_PORT);

        spl_autoload_register([get_called_class(),"autoLoad"]);

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


    /**
     * zjw
     * @param $className
     */
    public static function autoLoad($className){
        $fileName = BASE_ROOT."/".str_replace("\\","/",$className.".php");
        if(is_file($fileName)){
            require_once $fileName;
        }
    }

}