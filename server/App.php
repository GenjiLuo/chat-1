<?php
use server\Di;
use common\interfaces\ServerInterface;
/**
 * zjw
 * 2017/11/6
 */
class App{

    public static  $DI;

    public static function run(ServerInterface $server,array $main){
        require_once BASE_ROOT."/vendor/autoload.php";
        self::$DI = new Di($main);
        $server->run();
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

    public static function notice(string $str){
        self::$DI->log->notice($str);
    }
}