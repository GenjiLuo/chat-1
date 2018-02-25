<?php

namespace server\http;
use Swoole\Http\Request;
use Swoole\Http\Response;
class Router
{
    public $map;

    public function __construct()
    {
        $router = require BASE_ROOT . '/server/http/config/router.php';
        foreach ($router as $path => $call) {
            $this->set($path, $call);
        }
    }

    public function set(string $path, $call)
    {
        $this->map[$path] = $call;
    }

    public function get(string $path)
    {
        return isset($this->map[$path]) ? $this->map[$path] : '';
    }


    public  function dispatch(Request $request,Response $response){
        $path = $request->server['request_uri'];
        if($callBack = $this->get($path)){
            if(is_callable($callBack)){
                $result = call_user_func($callBack,$request,$response);
            }
            if(is_string($callBack)){
                $result = (new $callBack($request,$response))->run();
            }
            $response->end($result);
        }else{
            $response->status(404);
            $response->end('');
        }

    }

}
