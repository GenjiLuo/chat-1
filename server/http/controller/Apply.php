<?php

namespace server\http\controller;

use common\lib\MyRedis;
use common\model\FriendApplyModel;
use core\App;

class Apply extends Auth
{
    public function create()
    {
        $targetId = $this->request->post['targetId'];
        $reason = $this->request->post['reason'];
        $model = App::createObject(FriendApplyModel::class);
        $data = [
            'sponsor_id' => $this->user['id'],
            'target_id' => $targetId,
            'created_at' => date('Y-m-d H:i:s'),
            'reason' => $reason
        ];
        $model->insert($data);
        if ($id = $model->medoo->id()) {
            $redis = App::createObject(MyRedis::class);
            // 加入到通知队列中
            $redis->lPush("applyNotice",$id);
            return ['status' => 1];

        }
        return ['status'=>0];
    }
}