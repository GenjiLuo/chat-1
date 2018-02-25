<?php

namespace server\http;
use Swoole\Http\Request;
use Swoole\Http\Response;
class Router
{
    public $map = [];

    /**
     * Router constructor.
     */
    public function __construct()
    {
        // 初始化路由
        $router = require BASE_ROOT . '/server/http/config/router.php';
        foreach ($router as $path => $call) {
            $this->set($path, $call);
        }
    }

    /**
     * @param string $path
     * @param $call
     */
    public function set(string $path, $call)
    {
        $this->map[$path] = $call;
    }

    /**
     * @param string $path
     * @return mixed|string
     */
    public function get(string $path)
    {
        return isset($this->map[$path]) ? $this->map[$path] : '';
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public  function dispatch(Request $request,Response $response){
        $path = $request->server['request_uri'];
        //如果是静态资源
        if(substr($path,1,6)=="static"){
            $file = BASE_ROOT.$path;
            echo $file;
            if(is_file($file)){
                $response->sendfile($file);
            }
        }
        //动态请求
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
