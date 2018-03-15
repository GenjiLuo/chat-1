<?php

namespace common\model;

use common\lib\DB;

class UserModel extends DB
{
    public static $defaultPath = "/static/avatar/";
    public static $defaultAvatar = "default.jpg";
    public static $tableName = "user";

    /**
     * @param $where
     * @param array $field
     * @return mixed
     */
    public function findOne(array $where, $field = ['username', 'id', 'age', 'sex', 'avatar'])
    {
        $result = $this->medoo->select(self::$tableName, $field, $where);
        if (!empty($result)) {
            $user = $result[0];
            $user['avatar'] = BASE_URL . self::$defaultPath . $user['avatar'];
            return $user;
        }
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    public function add(array $data)
    {
        $data['password'] = md5($data['password']);
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['avatar'] = self::$defaultAvatar;
        $this->medoo->insert(self::$tableName, $data);
        if ($this->medoo->id()) {
            return $this->medoo->id();
        }
        return false;
    }

    /**
     * @param array $where
     * @return array|bool
     */
    public function findAll($where = [])
    {
        $users = $this->medoo->select(self::$tableName, ['username', 'id', 'avatar', 'sex', 'age'], $where);
        array_walk($users, function (&$v, $k) {
            $v['avatar'] = BASE_URL . self::$defaultPath . $v['avatar'];
        });
        return $users;
    }
}