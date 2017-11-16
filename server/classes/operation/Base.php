<?php
namespace server\classes\operation;
/**
 * zjw
 * 2017/11/10
 */
class Base{
    public static function className(){
        return get_called_class();
    }
}