<?php
namespace server\ws;
use common\lib\exception\FileNotExistException;
use server\ws\action\Action;

class Router{

    public $map = [];

    /**
     * Router constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $routerFile  = BASE_ROOT."/server/ws/config/router.php";
        if(is_file($routerFile)){
            $this->map = require_once $routerFile;
        }else{
            throw new FileNotExistException("router file");
        }

    }

    /**
     * @param array $param
     * @param string $type
     */
    public function dispatch(array $param,string $type){
        $action = $this->map[$type];
        $action  = new $action($param);
        if ($action instanceof Action){
            if($action->beforeAction()){
                $action->handle();
                $action->afterAction();
            }

        }
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