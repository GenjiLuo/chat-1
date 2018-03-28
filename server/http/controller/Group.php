<?php
namespace server\http\controller;

use common\lib\MyRedis;
use common\model\GroupModel;
use core\App;

class Group extends Auth {
    /**
     * @return array|mixed
     */
    public function create(){
        $ids = $this->request->post['ids'];
        $userId= $this->user['id'];
        $groupName = $this->request->post['name'];
        $groupModel = App::createObject(GroupModel::class);
        if($groupId = $groupModel->create($userId,$ids,$groupName)){
            $redis = App::createObject(MyRedis::class);
            $redis->publish("createGroup",$groupId);
            return ['status'=>1];
        }
        return ['status'=>0];
    }


}