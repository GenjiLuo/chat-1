<?php
namespace server\http;
class Router{

    public  $map;

    public function __construct()
    {
        $router = require BASE_ROOT."/server/http/config/router.php";
        foreach ($router as $path =>$call ){
            $this->set($path,$call);
        }
    }

    public  function set($path,$call){
        $this->map[$path] = $call;
    }

    public function get($path){
        $this->map[$path];
    }
}
