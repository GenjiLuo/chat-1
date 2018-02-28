<?php
namespace server\ws;
class Router{

    public $map = [];

    public function __construct()
    {
        $routerFile  = BASE_ROOT."/server/ws/config/router.php";
        if(is_file($routerFile)){
            $this->map = require_once $routerFile;
        }else{
            throw new \Exception("router file not exist");
        }

    }

    public function dispatch(array $param,string $type){
        $action = $this->map[$type];
        (new $action($param))->handle();
    }

    /**
     * @param string $path
     * @param $call
     * @param $type
     */
    public function set(string $path, $call,string $type)
    {
        $this->map[$type][$path] = $call;
    }

    /**
     * @param string $path
     * @return mixed|string
     */
    public function get(string $path,$type)
    {
        return isset($this->map[$type][$path]) ? $this->map[$type][$path] : '';
    }
}