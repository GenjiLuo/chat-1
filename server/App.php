<?php
use server\lib\Container;

class App{

    public $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this ->init();
        $this->run();
    }

    public function run(){
        Server::$container = new Container($this->config['component']);
    }

    public function init(){
        spl_autoload_register([get_called_class(),"autoLoad"]);
        require_once '../vendor/autoload.php';
        require_once "lib/Server.php";
    }

    public static function autoLoad($className){
        $fileName = BASE_ROOT."/".str_replace("\\","/",$className.".php");
        if(is_file($fileName)){
            require_once $fileName;
        }
    }
}