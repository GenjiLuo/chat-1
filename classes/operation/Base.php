<?php
namespace classes\operation;
use ReflectionClass;
/**
 * zjw
 * 2017/11/10
 */
class Base{
    public static function className(){
        return get_called_class();
    }
}