<?php
namespace common\model;
use common\lib\DB;

/**
 * Class FriendApplyModel
 * @package common\lib
 */
class FriendApplyModel extends DB {

    public static $tableName = 'friend_apply';

    /**
     * @param $where
     * @return array|bool
     */
    public function find($where){
        $where['ORDER'] = ['created_at'=>'DESC'];
        return $this->medoo->select(self::$tableName,"*",$where);
    }

    /**
     * @param $sponsorId
     * @param $targetId
     * @return bool
     */
    public function add($sponsorId,$targetId){
        $data['sponsor_id'] = $sponsorId;
        $data['target_id'] = $targetId;
        $data['created_at'] = date("Y-m-d H:i:s");
        $this->medoo->insert(self::$tableName,$data);
        if($this->medoo->last()){
            return true;
        }
        return false;
    }

    /**
     * @param $sponsorId
     * @param $targetId
     * @return bool
     * 是否申请中
     */
    public function isApplying($sponsorId,$targetId){
        $where = [
            'sponsor_id'=>$sponsorId,
            'target_id'=>$targetId,
            'is_agree'=>0
        ];
        $result = $this->medoo->select(self::$tableName,'*',$where);
        if($result){
            return true;
        }
        return false;

    }

}