<?php

namespace server\http;

use Swoole\Http\Request;
use Swoole\Http\Response;

class Router
{
    public $map = [];
    public $contentType = [];

    /**
     * Router constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        // 初始化路由
        $routerFile = BASE_ROOT . '/server/http/config/router.php';
        if(is_file($routerFile)){
            $router = require_once $routerFile;
            foreach ($router as $path => $call) {
                $this->set($path, $call);
            }
        }else{
            throw new \Exception('router file not exist');
        }
        // contentType
        $contentTypeFile  =  BASE_ROOT . '/server/http/config/contentType.php';
        if(is_file($contentTypeFile)){
            $this->contentType = require_once $contentTypeFile;
        }else{
            throw new \Exception('content-type file not exist');
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
     * @return bool
     */
    public function dispatch(Request $request, Response $response)
    {
        $path = $request->server['request_uri'];

        // 如果是静态资源
        if (substr($path, 1, 6) === 'static') {
            $file = BASE_ROOT . $path;
            $ext = strtolower(pathinfo($file,PATHINFO_EXTENSION));
            if (is_file($file)) {
                if(isset($this->contentType[$ext])){
                    $response->header("Content-Type",$this->contentType[$ext]);
                }
                $response->sendfile($file);
                return true;
            }
        }
        // 动态请求
        if ($callBack = $this->get($path)) {
            if (is_callable($callBack)) {
                $result = call_user_func($callBack, $request, $response);
            }
            if (is_string($callBack)) {
                $result = (new $callBack($request, $response))->run();
            }
            $response->end($result);
            return true;
        }
        $response->status(404);
        $response->end('');
    }

}
