<?php
namespace api\lib;
/**
 * zjw
 * 2017/11/16
 */
class Router {

    public  function run(){
        $requestUrl = $_SERVER['REQUEST_URI'];
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $routeStr = str_replace(dirname($scriptName)."/","",$requestUrl);
        $routeStr =  str_replace(strstr($routeStr,"?"),"",$routeStr);
        $route = explode("/",$routeStr);
        $controller = $route[0];

        if(isset($route[1])){
            $method = $route[1];
        }else{
            $method = "index";
        }
        $class = CONTROLLER_NAMESPACE.$controller;
        $controller = new $class;
        if($controller->beforeAction()){
             return $controller->afterAction($controller->$method());
        }
    }

}