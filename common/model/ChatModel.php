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

    public function findAll($userId)
    {
        $where = [
            'user_id' => $userId,
            'ORDER'=>['last_chat_time'=>'DESC']
        ];
        $join = [
            "[>]" . UserModel::$tableName => ['target_id' => 'id']
        ];
        $fields = ['chat_id','user.id', 'last_chat_time', 'user.avatar', 'user.username', 'user.age', 'user.sex'];
        $result = $this->medoo->select(self::$tableName, $join, $fields, $where);
        foreach ($result as $key => &$chat) {
            $chat['msgList'] = []; //todo
            $chat['avatar'] = BASE_URL . UserModel::$defaultPath . $chat['avatar'];
        }
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
            if (!$this->isExist($targetId, $userId)) {
                $dataReverse = [
                    'user_id' => $targetId,
                    'target_id' => $userId,
                    'type' => self::TYPE_FRIEND,
                    'last_chat_time' => $date
                ];
                $this->medoo->insert(self::$tableName, $dataReverse);
            }
            return true;
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
}