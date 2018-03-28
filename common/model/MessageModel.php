<?php

namespace common\model;

use common\lib\DB;

/**
 * Class MessageModel
 * @package common\model
 */
class MessageModel extends DB
{

    public static $tableName = "message";

    /**
     * @param $message
     * @return bool
     */
    public function add($message)
    {
        $db = $this->medoo;
        $db->insert(self::$tableName, $message);
        if ($db->id()) {
            return false;
        }
        return true;
    }

    /**
     * @param array $where
     * @return array|bool
     */
    public function find(array $where)
    {
        return $this->medoo->select(self::$tableName, "*", $where);
    }

    /**
     * @param array $where
     * @return array|bool
     */
    public function findWithAvatar(array  $where) {
        $join = [
            "[>]" . UserModel::$tableName => ['from_id' => 'id']
        ];
        $fields = ['message.id','from_id','to_id','chat_id','time','msg','is_read','avatar'];
        $list =  $this->medoo->select(self::$tableName, $join,$fields, $where);
        array_walk($list, function (&$v, $k) {
            $v['avatar'] = BASE_URL . UserModel::$defaultPath . $v['avatar'];
        });
        return $list;
    }

}