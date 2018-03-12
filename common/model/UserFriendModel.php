<?php

namespace common\model;

use common\lib\DB;

class UserFriendModel extends DB
{
    public static $tableName = "user_friend";

    /**
     * @param $userId
     * @param $targetId
     * @return bool
     */
    public function isFriend($userId,$targetId){
        $result =  $this->medoo->select(self::$tableName,'*',['user_id'=>$userId,'friend_id'=>$targetId]);
        if($result){
            return true;
        }
        return false;
    }

    /**
     * @param $userId
     * @return array|bool
     */
    public function find($userId){
        $where = ['user_id'=>$userId];
        $join = [
            "[>]".UserModel::$tableName => ['friend_id'=>'id']
        ];
        $fields = ['username','sex','age','avatar',UserModel::$tableName.'.id'];
        $result = $this->medoo->select(self::$tableName,$join,$fields,$where);
        if( $result ){
            foreach ($result as $key =>&$user ){
                $user['avatar'] = BASE_URL . UserModel::$defaultPath . $user['avatar'];
            }
        }
        return $result;
    }
}