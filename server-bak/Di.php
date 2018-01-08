<?php
/**
 * zjw
 * Date: 2017/11/16
 */
class Di{

    private $container;

    public function __construct($main)
    {
        foreach ($main as $key=>$val){
            $this->$key = $val;
        }
    }


    public function __get($name)
    {
        if( $this->container[$name] instanceof Closure){
            $this->container[$name] = $this->container[$name]();
            return $this->container[$name];
        }else{
            return  $this->container[$name];
        }
    }


    public function __set($name, $value)
    {
        if($value instanceof Closure){
            $this->container[$name] = $value;
        }else if(is_array($value)){
            $this->container[$name] = function() use ($value){
                return new $value['class']($value);
            };
        }
    }

    public function get($name){
        if( $this->container[$name] instanceof Closure){
            $this->container[$name] = $this->container[$name]();
            return $this->container[$name];
        }else{
            return  $this->container[$name];
        }
    }



    public function set($name,$value){
        if($value instanceof Closure){
            $this->container[$name] = $value;
        }else if(is_array($value)){
            $this->container[$name] = function() use ($value){
                return new $value['class']($value);
            };
        }
    }
}