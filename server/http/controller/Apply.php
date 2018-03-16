<?php

namespace server\http\controller;

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
        if ($model->medoo->id()) {
            return ['status' => 1];
        }
        return ['status'=>0];
    }
}