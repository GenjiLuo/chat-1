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
    public function isFriend($userId, $targetId)
    {
        $result = $this->medoo->select(self::$tableName, '*', ['user_id' => $userId, 'friend_id' => $targetId]);
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * @param $userId
     * @return array|bool
     */
    public function find($userId)
    {
        $where = ['user_id' => $userId];
        $join = [
            "[>]" . UserModel::$tableName => ['friend_id' => 'id']
        ];
        $fields = ['username', 'sex', 'age', 'avatar', UserModel::$tableName . '.id'];
        $result = $this->medoo->select(self::$tableName, $join, $fields, $where);
        if ($result) {
            foreach ($result as $key => &$user) {
                $user['avatar'] = BASE_URL . UserModel::$defaultPath . $user['avatar'];
            }
        }
        return $result;
    }

    /**
     * @param $sponsorId
     * @param $targetId
     * @return bool
     */
    public function add($sponsorId, $targetId)
    {
        $date = date("Y-m-d H:i:s");
        $dataOne = [
            'user_id' => $sponsorId,
            'friend_id' => $targetId,
            'sponsor_id' => $sponsorId,
            'add_time' => $date
        ];
        $dataTwo = [
            'user_id' => $targetId,
            'friend_id' => $sponsorId,
            'sponsor_id' => $sponsorId,
            'add_time' => $date
        ];
        return $this->medoo->insert(self::$tableName, $dataOne) && $this->medoo->insert(self::$tableName, $dataTwo);
    }

    /**
     * @param $userId
     * @param $targetId
     * @return bool
     * @throws \Exception
     */
    public function deleteFriend($userId, $targetId)
    {
        return $this->medoo->action(function () use ($userId, $targetId) {
            try {
                $where = [
                    "OR #1" => [
                        "AND #1" => ["user_id" => $userId, "friend_id" => $targetId],
                        "AND #2" => ["user_id" => $targetId, "friend_id" => $userId],
                    ],
                ];
                $this->delete($where);
                $chatModel = new ChatModel($this->medoo);
                $chatWhere = [
                    "OR #1" => [
                        "AND #1" => ["user_id" => $userId, "target_id" => $targetId],
                        "AND #2" => ["user_id" => $targetId, "target_id" => $userId],
                    ]
                ];
                $chatModel->delete($chatWhere);
                $messageModel = new MessageModel($this->medoo);
                $messageWhere = [
                    "OR #1" => [
                        "AND #1" => ["from_id" => $userId, "to_id" => $targetId],
                        "AND #2" => ["from_id" => $targetId, "to_id" => $userId],
                    ]
                ];
                $messageModel->delete($messageWhere);
            } catch (\Exception $e) {
                return false;
            }
            return true;
        });

    }
}