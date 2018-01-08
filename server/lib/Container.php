<?php

namespace server\lib;
/**
 * zjw
 * 2018/1/8
 */
class Container
{
    private $_definition;

    private $_container;


    public function __construct($component)
    {
        $this->_definition = $component;
    }

    public function get($name)
    {
        if (!isset($this->_container[$name])) {
            if (isset($this->_definition[$name])) {
                if ($this->_definition[$name] instanceof \Closure) {
                    $this->_container[$name] = $this->_definition[$name];
                }
                if (is_array($this->_definition[$name])) {
                    $class = $this->_definition[$name]['class'];
                    $this->_container[$name] = new $class($this->_definition[$name]);
                }
            }
            throw new \Exception("$name 未定义");
        }
        return $this->_container[$name];
    }


    public function set($name, $value)
    {
        $this->_definition[$name] = $value;
        if (isset($this->_container[$name])) unset($this->_container[$name]);
    }


    public function __get($name)
    {
        if (!isset($this->_container[$name])) {
            if (isset($this->_definition[$name])) {
                if ($this->_definition[$name] instanceof \Closure) {
                    $this->_container[$name] = $this->_definition[$name];
                }
                if (is_array($this->_definition[$name])) {
                    $class = $this->_definition[$name]['class'];
                    $this->_container[$name] = new $class($this->_definition[$name]);
                }
            } else {
                throw new \Exception("$name 未定义");
            }

        }
        return $this->_container[$name];
    }

    public function __set($name, $value)
    {
        $this->_definition[$name] = $value;
    }
}