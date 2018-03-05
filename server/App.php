<?php
use server\Di;
use common\interfaces\ServerInterface;
/**
 * zjw
 * 2017/11/6
 */
class App{

    public static  $DI;

    public static $server;

    public static function run(ServerInterface $server,$DI){
        require_once BASE_ROOT."/vendor/autoload.php";
        self::$DI = $DI;
        self::$server = $server;
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
     * @param string $str
     */
    public static function notice(string $str){
        self::$DI->log->notice($str);
    }
}