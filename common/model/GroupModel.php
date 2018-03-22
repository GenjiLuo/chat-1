<?php

namespace common\model;

use common\lib\DB;

class GroupModel extends DB
{

    public static $tableName = "group";

    /**
     * @param int $sponsorId
     * @param array $targetIds
     * @param string $name
     * @return bool|\PDOStatement
     */
    public function create(int $sponsorId, array $targetIds,string $name)
    {
        $date = date("Y-m-d H:i:s");
        $data = [
            'sponsor_id' => $sponsorId,
            'created_at' => $date,
            'group_name'=>$name
        ];
        $groupId = $this->insert($data);
        $targetIds[] = $sponsorId;
        if ($groupId) {
            $groupUserModel = new GroupUserModel($this->medoo);
            foreach ($targetIds as $key => $id) {
                $groupUserData = [
                    "group_id" => $groupId,
                    'user_id' => $id,
                    'created_at' => $date
                ];
                $groupUserModel->insert($groupUserData);
            }
            return $groupId;
        }
        return false;
    }

    /**
     * @param $groupId
     * @return array|bool
     */
    public function findOneWithUser($groupId){
        $group = $this->medoo->select(self::$tableName,['group_name','group_id'],['group_id'=>$groupId])[0];
        $group['userIds'] = $this->medoo->select(GroupUserModel::$tableName,['user_id'],['group_id'=>$groupId]);
        return $group;
    }
}