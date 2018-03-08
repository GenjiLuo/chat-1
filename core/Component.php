<?php
namespace core;
/**
 * Class Component
 * @package server
 * 组件容器
 * 单例管理，懒加载
 */
class Component
{
    private $_definition;

    private $_container;

    public function __construct(array $component)
    {
        foreach ($component as $name => $def) {
            var_dump($def);
            if(is_array($def)){
                $className = $def['class'];
                unset($def['class']);
                $this->_definition[$name] = $className;
                IOC::set($className,$def);
            }
            if(is_string($def)){
                $className = $def;
                $this->_definition[$name] = $className;
            }
        }

    }
    /**
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        if(!isset($this->_container[$key])){
            $className = $this->_definition[$key];
            $this->_container[$key] = App::createObject($className);
        }
        return $this->_container[$key];
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        if(!isset($this->_container[$key])){
            $className = $this->_definition[$key];
            $this->_container[$key] = App::createObject($className);
        }
        return $this->_container[$key];
    }

    /**
     * @param string $name
     * @param $value
     * 动态注册
     */
    public function set(string $name, $value)
    {
        if (isset($this->_container[$name])) {
            unset($this->_container[$name]);
        }
        $this->_definition[$name] = $value;
    }

    /**
     * @param string $name
     * @param $value
     * 动态注册
     */
    public function __set(string $name, $value)
    {
        if (isset($this->_container[$name])) {
            unset($this->_container[$name]);
        }
        $this->_definition[$name] = $value;
    }

}