<?php

namespace server\http\controller;

use common\model\UserModel;
use core\App;
use common\model\FriendApplyModel;
/**
 * Class User
 * @package server\http\controller
 */
class User extends Auth
{

    public function view()
    {

    }

    /**
     * @return array|mixed
     */
    public function create()
    {
        $post = $this->request->post;
        $username = $post['username'];
        $userModel = App::createObject(UserModel::class);
        if ($userModel->findOne(["username" => $username])) {
            return ['status' => 0, 'errMsg' => "用户名已存在"];
        } else {
            if ($userModel->add($post)) {
                return ['status' => 1];
            }
        }
        return ['status' => 0, 'errMsg' => "发生未知错误"];
    }

    /**
     * @return array|mixed
     */
    public function update(){
        $userId = isset($this->params['id']) ? $this->params['id'] : null;
        if ($userId) {
            $type = $this->request->post['type'];
            if($type === 'applyRead'){
                $model = App::createObject(FriendApplyModel::class);
                if($model->update(['is_read'=>1],['target_id'=>$userId])){
                    return ['status'=>1];
                }
            }

        }
        return ['status'=>0];
    }

}