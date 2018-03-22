<?php

namespace common\model;

use common\lib\DB;

/**
 * Class ChatModel
 * @package common\model
 */
class ChatModel extends DB
{
    public static $tableName = 'chat';

    const TYPE_GROUP = 1;
    const TYPE_FRIEND = 0;

    /**
     * @param $userId
     * @return array|bool
     */
    public function findAllWithUser($userId)
    {
        $where = [
            'user_id' => $userId,
            'type'=>ChatModel::TYPE_FRIEND,
            'ORDER' => ['last_chat_time' => 'DESC']
        ];
        $join = [
            "[>]" . UserModel::$tableName => ['target_id' => 'id']
        ];
        $fields = ['chat_id','type','target_id', 'user.id', 'last_chat_time', 'user.avatar', 'user.username', 'user.age', 'user.sex'];
        $result = $this->medoo->select(self::$tableName, $join, $fields, $where);
        foreach ($result as $key => &$chat) {
            $chat['msgList'] = []; //todo
            $chat['avatar'] = BASE_URL . UserModel::$defaultPath . $chat['avatar'];
        }
        return $result;
    }

    /**
     * @param $where
     * @param array $fields
     * @return mixed
     */
    public function findOneWithUser($where,$fields = ['chat_id','target_id', 'user.id', 'last_chat_time', 'user.avatar', 'user.username', 'user.age', 'user.sex','type'])
    {
        $join = [
            "[>]" . UserModel::$tableName => ['target_id' => 'id']
        ];
        $result = $this->medoo->select(self::$tableName, $join, $fields, $where)[0];
        $result['msgList'] = []; //todo
        $result['avatar'] = BASE_URL . UserModel::$defaultPath . $result['avatar'];
        return $result;
    }

    /**
     * @param $userId
     * @param $targetId
     * @return bool
     * 新增好友聊天
     */
    public function add($userId, $targetId)
    {
        if (!$this->isExist($userId, $targetId)) {
            $date = date("Y-m-d H:i:s");
            $data = [
                'user_id' => $userId,
                'target_id' => $targetId,
                'type' => self::TYPE_FRIEND,
                'last_chat_time' => $date
            ];
            $this->medoo->insert(self::$tableName, $data);
            return  $this->medoo->id();
        }
        return false;
    }

    /**
     * @param $userId
     * @param $targetId
     * @return bool
     * 聊天是否存在
     */
    public function isExist($userId, $targetId)
    {
        $res = $this->medoo->select(self::$tableName, "*", ['user_id' => $userId, 'target_id' => $targetId]);
        return $res ? true : false;
    }

    /**
     * @param $where
     * @return array|bool
     */
    public function findOneWithGroup($where){
        $where['ORDER'] = ['last_chat_time' => 'DESC'];
        $where['type'] = self::TYPE_GROUP;
        $join = [
            "[>]" . GroupModel::$tableName => ['target_id' => 'group_id']
        ];
        $fields = ['chat_id','target_id','group_id', 'last_chat_time','group_name','type'];
        $result = $this->medoo->select(self::$tableName, $join, $fields, $where);
        return $result;
    }
}