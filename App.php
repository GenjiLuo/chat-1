<?php
/**
 * zjw
 * 2017/11/6
 */
class App{


    private  $container;


    public function __construct($config)
    {
        $this->container  =$config;
    }


    public function __set($name, $value)
    {
        // TODO: Implement __set() method.
    }

    public function __get($name)
    {
        if (isset( $this->container[$name])){
            $obj = $this->container[$name];
            if( $obj instanceof stdClass){
                return $obj;
            }else if(is_array($obj)){
                $class = $this->createObject($obj);
                $this->container[$name] = $class;
            }
            return  $this->container[$name];
        }
    }

    /**
     * zjw
     * @param $obj
     * @return null
     * 创建对象，cli模式下自动加载无法运行
     */
    public  function createObject($obj){
        $class = $obj['class'];
        $classFile = BASE_ROOT.DIRECTORY_SEPARATOR.$class.".php";
        $classFile = str_replace("\\","/",$classFile);
        if(!is_file($classFile)){
            return null;
        }
        require $classFile;
        unset($obj['class']);
        $class =  new $class($obj);
        return $class;
    }
}