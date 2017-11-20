<?php
/**
 * zjw
 * Date: 2017/11/16
 */
class App{

    public static $DI;

    /**
     * zjw
     * @param $DI
     */
    public  static function init($DI){
        spl_autoload_register([get_called_class(),"autoLoad"]);
        self::$DI = $DI;
        self::run();
    }


    public static function run(){
        $data =  self::$DI->router->run();
        self::$DI->response->send($data);
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