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
     * @param $where
     * @return array|bool
     */
    public function find($where)
    {
        return $this->medoo->select(self::$tableName, "*", $where);
    }

}