<?php

namespace common\model;

use common\lib\DB;
use Medoo\Medoo;

/**
 * Class FriendApplyModel
 * @package common\lib
 */
class FriendApplyModel extends DB
{

    public static $tableName = 'friend_apply';

    /**
     * @param $where
     * @return array|bool
     */
    public function findWithUser($where)
    {
        $where['ORDER'] = ['friend_apply.created_at' => 'DESC'];
        $join = [
            "[>]" . UserModel::$tableName => ['sponsor_id' => 'id']
        ];
        $fields = ['friend_apply.is_read', 'friend_apply.status','reason',
            'user.avatar', 'user.username', 'user.age', 'user.sex', 'friend_apply.id'];
        $result = $this->medoo->select(self::$tableName, $join, $fields, $where);
        if ($result) {
            foreach ($result as $key => &$value) {
                $value['avatar'] = BASE_URL . UserModel::$defaultPath . $value['avatar'];
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
        $data['sponsor_id'] = $sponsorId;
        $data['target_id'] = $targetId;
        $data['created_at'] = date("Y-m-d H:i:s");
        $this->medoo->insert(self::$tableName, $data);
        if ($this->medoo->last()) {
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
    public function isApplying($sponsorId, $targetId)
    {
        $where = [
            'sponsor_id' => $sponsorId,
            'target_id' => $targetId,
            'status' => 0
        ];
        $result = $this->medoo->select(self::$tableName, '*', $where);
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * @param $applyId
     * @return bool|\PDOStatement
     */
    public function reject($applyId)
    {
        return $this->medoo->update(self::$tableName, ['status' => 2], ['id' => $applyId]);
    }

    /**
     * @param $applyId
     * @return bool
     * @throws \Exception
     */
    public function agree($applyId)
    {
        $apply = $this->medoo->select(self::$tableName, "*", ['id' => $applyId])[0];
        // 事务
        return $this->medoo->action(function (Medoo $db) use ($apply) {
            // 查看是否有相反的申请,如果有则把状态也修改为同意
            $sponsorId = $apply['target_id'];
            $targetId = $apply['sponsor_id'];
            $applyReverse = $db->select(self::$tableName, "*",
                ['sponsor_id' => $sponsorId, 'target_id' => $targetId, 'status' => 0]);
            if ($applyReverse) {
                foreach ($applyReverse as $key => $val) {
                    $db->update(self::$tableName, ['status' => 1, ['id' => $val['id']]]);
                }
            }
            $result1 = $db->update(self::$tableName, ['status' => 1], ['id' => $apply['id']]);
            $userFriendModel = new UserFriendModel($db);
            $result2 = $userFriendModel->add($apply['sponsor_id'], $apply['target_id']);
            return $result1 && $result2;
        }) ? $apply : false;
    }


}