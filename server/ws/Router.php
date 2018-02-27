<?php
namespace server\ws;

class Router{

    public $map = [];

    public function dispatch(array $param,string $type){

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
    public function get(string $path,$type )
    {
        return isset($this->map[$type][$path]) ? $this->map[$type][$path] : '';
    }
}