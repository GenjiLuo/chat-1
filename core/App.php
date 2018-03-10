<?php
namespace core;
use core\interfaces\ServerInterface;

/**
 * Class App
 * @package core
 * 主类
 */
class App{

    public static $server;

    public static $comp;

    /**
     * @param ServerInterface $server
     * @param $dependence
     * @param $component
     */
    public static function run(ServerInterface $server,$dependence,$component){
        require_once BASE_ROOT . "/vendor/autoload.php";
        IOC::$dependence = $dependence;
        self::$comp = new Component($component);
        self::$server = $server;
        self::$server->run();
    }

    /**
     * @param $className
     */
    public static function autoLoad($className){
        $fileName = BASE_ROOT.'/'.str_replace('\\','/',$className.'.php');
        if(is_file($fileName)){
            require_once $fileName;
        }
    }

    /**
     * @param string $className
     * @return mixed
     * 创建对象
     */
    public static function createObject(string $className){
        return IOC::getInstance($className);
    }

}