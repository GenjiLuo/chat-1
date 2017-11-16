<?php
namespace api\lib;
/**
 * zjw
 * 2017/11/16
 */
class Router {

    public static function run(){
        $requestUrl = $_SERVER['REQUEST_URI'];
        $requestArr = explode("/",$requestUrl);
        $source =  ucfirst($requestArr[sizeof($requestArr)-1]);
        $fullSource = CONTROLLER_NAMESPACE.$source;
        $controller = new $fullSource();
        $method = self::method();
        return $controller->$method();
    }

    public static function method(){
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method){
            case 'POST' :
                return 'create';
                break;
            case 'PUT':
                return 'update';
                break;
            case 'GET':
                return 'index';
                break;
        }
    }
}