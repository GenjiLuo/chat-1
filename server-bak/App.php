<?php
/**
 * zjw
 * 2017/11/6
 */
class App{

    public  static  $DI;

    public static function run($DI){
        spl_autoload_register([get_called_class(),"autoLoad"]);
        self::$DI = $DI;
        ChatServer::init();
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
}