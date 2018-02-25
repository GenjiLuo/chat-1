<?php
use server\Di;
/**
 * zjw
 * 2017/11/6
 */
class App{

    public static  $DI;

    public static function run($server,$main){
        spl_autoload_register([get_called_class(),"autoLoad"]);
        self::$DI = new Di($main);
        $server::run();
    }

    /**
     * zjw
     * @param $className
     */
    public static function autoLoad($className){
        $fileName = BASE_ROOT."/".str_replace("\\","/",$className.".php");
        if(is_file($fileName)){
            require_once $fileName;
        }
    }

    public static function notice(string $str){
        self::$DI->log->notice($str);
    }
}