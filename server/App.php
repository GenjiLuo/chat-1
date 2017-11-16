<?php
/**
 * zjw
 * 2017/11/6
 */
class App{


    private  $container;


    public function __construct($config)
    {

        $this->container  = $config;
        spl_autoload_register([get_called_class(),"autoLoad"]);

    }


    public function __set($name, $value)
    {
         $this->container[$name] = function($value){
            return new $value['class']($value);
         };
    }



    public function __get($name)
    {
        if (isset( $this->container[$name])){
            $obj = $this->container[$name];
            if( $obj instanceof stdClass){
                return $obj;
            }else if(is_array($obj)){
                $class = new $obj['class']($obj);
                $this->container[$name] = $class;
            }
            return  $this->container[$name];
        }
        return null;
    }


    /**
     * zjw
     * @param $className
     */
    public function autoLoad($className){
        $fileName = BASE_ROOT."/".str_replace("\\","/",$className.".php");
        if(is_file($fileName)){
            require_once $fileName;
        }
    }
}