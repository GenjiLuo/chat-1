<?php
namespace common\model;
use App;
/**
 * zjw
 * 2017/11/20
 */
class UserModel extends BaseModel {


    public static $defaultPath = "/static/avatar/";
    public static $defaultAvatar = "default.jpg";

    /**
     * zjw
     * @param $where
     * @param array $field
     * @return mixed
     */
    public static  function findOne($where,$field=['username','id','avatar','nickname']){
        $data = App::$DI->db->select(self::tableName(),$field,$where);
        if (!empty($data)){
            $data[0]['avatar'] =BASE_URL. $data[0]['avatar'];
            return $data[0];
        }
        return $data;
    }

    /**
     * zjw
     * @param $data
     * @param $where
     * @return mixed
     */
    public static function update($data,$where){
        return  App::$DI->db->update(self::tableName(),$data,$where);
    }

    /**
     * zjw
     * @param $data
     * @return bool
     */
    public static function add($data){
        $data['avatar'] =self::$defaultPath.self::$defaultAvatar;
        $data['nickname'] = $data['username'];
        App::$DI->db->insert(self::tableName(),$data);
        if( App::$DI->db->id() ){
            return App::$DI->db->id();
        }
        return false;
    }

    public static function findAll($where=[]){
        $users = App::$DI->db->select(self::tableName(),['username','id','avatar','nickname'],$where);
        array_walk($users,function(&$v,$k){
            $v['avatar'] = BASE_URL.$v['avatar'];
        });
        return $users;
    }
}