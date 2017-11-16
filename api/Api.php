<?php
use api\lib\Router;
/**
 * zjw
 * Date: 2017/11/16
 */
class Api{

    public static $app;

    /**
     * zjw
     * @param $app
     */
    public  static function init($app){
        spl_autoload_register([get_called_class(),"autoLoad"]);
        self::$app = $app;
        Router::run();
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