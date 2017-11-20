<?php
namespace api\lib;
/**
 * zjw
 * Date: 2017/11/20
 */
class Request{
    /**
     * zjw
     * @param null $key
     * @param string $default
     * @return string
     */
   public function get($key=null,$default=""){
       if($key===null){
           return $_GET;
       }
       if(isset($_GET[$key])){
           return trim($_GET[$key]);
       }
       return $default;
   }

    /**
     * zjw
     * @param null $key
     * @param string $default
     * @return string
     */
    public function post($key=null,$default=""){
        if($key===null){
            return $_POST;
        }
        if(isset($_POST[$key])){
            return trim($_POST[$key]);
        }
        return $default;
    }

    /**
     * zjw
     * @return mixed
     */
    public function getIp(){
        return  $_SERVER['REMOTE_ADDR'];
    }

}