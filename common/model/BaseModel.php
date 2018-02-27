<?php
namespace common\model;
/**
 * zjw
 * 2017/11/20
 */
class BaseModel{

    public static $DB;


    public static function tableName(){
        $class = new \ReflectionClass(get_called_class());
        return  strtolower(substr($class->getShortName(),0,-5));
    }

    public static function getDB(){
        if(!self::$DB){
            self::$DB = \App::$DI->db;
        }
        return self::$DB;
    }


}