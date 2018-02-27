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
     * @param $where
     * @param array $field
     * @return mixed
     */
    public static  function findOne(array $where,$field=['username','id','age','sex','avatar']){
        $data = self::getDB()->select(self::tableName(),$field,$where);
        if (!empty($data)){
            $data[0]['avatar'] = BASE_URL.$data[0]['avatar'];
            return $data[0];
        }
        return !empty($data)? $data[0]:[];
    }

    /**
     * @param $data
     * @param $where
     * @return mixed
     */
    public static function update(array $data,array $where){
        return  App::$DI->db->update(self::tableName(),$data,$where);
    }

    /**
     * @param $data
     * @return bool
     */
    public static function add(array $data){
        $data['password'] = md5($data['password']);
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['avatar'] = "static/avatar/default.jpg";
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