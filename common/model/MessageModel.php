<?php
namespace common\model;

class MessageModel extends BaseModel{

    public  static  function add($message){
        $db = App::$DI->db;
        $db->insert(self::tableName(),$message);
        if($db->id()){
            return false;
        }
        return true;
    }

    public static function find($where){
        return   App::$DI->db->select(self::tableName(),"*",$where);
    }

}