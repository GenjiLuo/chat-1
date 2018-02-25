<?php

namespace server;
/**
 * zjw
 * Date: 2017/11/16
 */
class Di
{
    private $_definition;

    private $_container;

    public function __construct(array $main)
    {
        foreach ($main as $key => $val) {
            $this->_definition[$key] = $val;
        }
    }


    public function __get(string $name)
    {
        if (!isset($this->_container[$name])) {
            if ($this->_definition[$name] instanceof \Closure) {
                $this->_container[$name] = call_user_func($this->_definition[$name]);
            }
            if (is_array($def = $this->_definition[$name])) {
                $this->_container[$name] = new $def['class']($def);
            }
            if (is_string($this->_definition[$name])) {
                $this->_container[$name] = new $this->_definition[$name]();
            }
            if (is_object($this->_definition[$name])) {
                $this->_container[$name] = $this->_definition[$name];
            }
        }
        return $this->_container[$name];
    }


    public function __set(string $name, $value)
    {
        if (isset($this->_container[$name])) {
            unset($this->_container[$name]);
        }
        $this->_definition[$name] = $value;
    }


    public function get(string $name)
    {
        if (!isset($this->_container[$name])) {
            if ($this->_definition[$name] instanceof \Closure) {
                $this->_container[$name] = call_user_func($this->_definition[$name]);
            }
            if (is_array($def = $this->_definition[$name])) {
                $this->_container[$name] = new $def['class']($def);
            }
            if (is_string($this->_definition[$name])) {
                $this->_container[$name] = new $this->_definition[$name]();
            }
            if (is_object($this->_definition[$name])) {
                $this->_container[$name] = $this->_definition[$name];
            }
        }
        return $this->_container[$name];
    }

    public function set(string $name, $value)
    {
        if (isset($this->_container[$name])) {
            unset($this->_container[$name]);
        }
        $this->_definition[$name] = $value;
    }
}