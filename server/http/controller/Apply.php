<?php

namespace server\http\controller;

use common\lib\MyRedis;
use common\model\FriendApplyModel;
use core\App;

class Apply extends Auth
{

    public function index(){
        $userId = $this->user['id'];
        $applyModel = App::createObject(FriendApplyModel::class);
        $list = $applyModel->findWithUser(['user_id'=>$userId]);
        return ['status'=>1,'applyList'=>$list];
    }

    /**
     * @return array|mixed
     */
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
        $applyId = $model->insert($data);

        if ($id = $model->medoo->id()) {
            $redis = App::createObject(MyRedis::class);
            //发布
            $redis->publish('applyCH',$applyId);
            return ['status'=>1];
        }
        return ['status'=>0];
    }

    /**
     * @return array|mixed
     */
    public function update()
    {
        $applyId= $this->params['id'];
        $applyModel = App::createObject(FriendApplyModel::class);
        $apply =$applyModel->selectOne(['id'=>$applyId,'target_id'=>$this->user['id']]);
        if($apply){
            $status  = $this->request->post['status'];
            if($status ==1){
                if($applyModel->agree($applyId)){
                    $redis = App::createObject(MyRedis::class);
                    $redis->publish('agreeCH',$applyId);
                    return ['status'=>1];
                }
            }else{
                if($applyModel->update(['status'=>$status],['id'=>$applyId])){
                    return ['status'=>1];
                }
            }
        }
        return ['status'=>0];
    }
}