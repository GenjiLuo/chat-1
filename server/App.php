<?php
use common\interfaces\ServerInterface;
/**
 * zjw
 * 2017/11/6
 */
class App{

    public static  $DI;


    public static $server;

    public static $router;

    public static $dependence;

    public static function run(ServerInterface $server,$router,$dependence){
        require_once BASE_ROOT."/vendor/autoload.php";
        self::$router = $router;
        self::$server = $server;
        self::$dependence = $dependence;

        self::$server->run();
    }

    /**
     * zjw
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
        if (isset(self::$dependence[$className])){
            $dep = self::$dependence[$className];
            if(is_string($dep)){
                return new $className(self::createObject($dep));
            }
            if(is_array($dep)){
                return new $className($dep);
            }
        }else{
            return new $className;
        }
    }

    /**
     * @param string $str
     */
    public static function notice(string $str){
        self::$DI->log->notice($str);
    }
}