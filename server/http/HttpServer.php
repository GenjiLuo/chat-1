<?php
namespace server\http;
use server\http\controller\Controller;
use Swoole\Http\Server;
use App;
class HttpServer{

    public static function  run(){
        $server =  new Server('127.0.0.1',8081);
        $server->on('request',function($request,$response){
            $response->end(self::dispatch($request));
        });
        $server->start();
    }

    public static function dispatch($request){
        $path = $request->server['request_uri'];
        $callBack = App::$DI->router->get($path);
        if(is_callable($callBack)){
            return call_user_func($callBack,$request);
        }
        if($callBack instanceof Controller){
            return $callBack->run($request);
        }
        if(is_string($callBack)){
            return (new $callBack)->run($request);
        }
    }


}