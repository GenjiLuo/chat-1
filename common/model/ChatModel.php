<?php

namespace common\model;

use common\lib\DB;
use common\lib\MyRedis;
use core\App;
use Medoo\Medoo;

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
            'type' => ChatModel::TYPE_FRIEND,
            'ORDER' => ['last_chat_time' => 'DESC']
        ];
        $join = [
            "[>]" . UserModel::$tableName => ['target_id' => 'id']
        ];
        $fields = ['chat_id', 'type', 'target_id', 'user.id', 'last_chat_time', 'user.avatar', 'user.username', 'user.age', 'user.sex'];
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
    public function findOneWithUser($where, $fields = ['chat_id', 'target_id', 'user.id', 'last_chat_time', 'user.avatar', 'user.username', 'user.age', 'user.sex', 'type'])
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
            return $this->medoo->id();
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
    public function findOneWithGroup($where)
    {
        $where['ORDER'] = ['last_chat_time' => 'DESC'];
        $where['type'] = self::TYPE_GROUP;
        $join = [
            "[>]" . GroupModel::$tableName => ['target_id' => 'group_id']
        ];
        $fields = ['chat_id', 'target_id', 'group_id', 'last_chat_time', 'group_name', 'type'];
        $result = $this->medoo->select(self::$tableName, $join, $fields, $where);
        return $result;
    }

    /**
     * @param $chatId
     * @param $userId
     * @return bool
     * @throws \Exception
     */
    public function deleteChat($chatId, $userId)
    {
        $where = ['chat_id' => $chatId, 'user_id' => $userId];
        $chat = $this->medoo->select(self::$tableName, '*', $where);
        if (!$chat) {
            return false;
        }
        $chat = $chat[0];
        if($chat['type'] ==self::TYPE_GROUP){
            $result =  $this->medoo->action(function (Medoo $db) use ($chat) {
                if ($db->delete(self::$tableName, ['chat_id'=>$chat['chat_id']])  && $db->delete(GroupUserModel::$tableName, ['group_id' => $chat['target_id'], 'user_id' => $chat['user_id']])) {
                    $db->delete(MessageModel::$tableName, ['chat_id' => $chat['chat_id']]);
                    $groupUser = $db->select(GroupUserModel::$tableName,"*",['group_id'=>$chat['target_id']]);
                    if(sizeof($groupUser) <=0 ){  //群组没人则删除群组
                        $db->delete(GroupModel::$tableName,['group_id'=>$chat['target_id']]);
                    }
                    return true;
                }
                return false;
            });
            if( $result) {
                $redis = App::createObject(MyRedis::class);
                $redis->publish("quitGroup",json_encode($chat));
            }
            return $result;
        }else{
            $row = $this->medoo->delete(self::$tableName,$where);
            if ($row) {
                (new MessageModel($this->medoo))->delete(['chat_id' => $chatId]);
                return true;
            }
            return false ;
        }

    }
}